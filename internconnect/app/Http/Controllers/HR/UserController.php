<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Coordinator;
use App\Models\School;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $users = User::when($q, function($query) use ($q) {
            $query->where('first_name', 'like', "%{$q}%")->orWhere('last_name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%");
        })->orderBy('user_id','desc')->paginate(15);

        return view('hr.users.index', compact('users','q'));
    }

    public function create()
    {
        $schools = School::all();
        $coordinators = Coordinator::with('school')->get();
        return view('hr.users.create', compact('schools', 'coordinators'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:tbl_user,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string',
            'school_id' => 'nullable|exists:tbl_school,school_id',
            'assign_coordinator_id' => 'nullable|exists:tbl_coordinator,coordinator_id',
        ]);

        $roleMap = [
            'admin' => 'Admin',
            'hr' => 'HR',
            'coordinator' => 'Coordinator',
            'student' => 'Intern',
        ];

        DB::beginTransaction();
        
        try {
            $coordinatorId = null;
            
            if ($data['role'] === 'coordinator') {
                $uniqueKey = strtoupper(Str::random(8));
                
                $coordinator = Coordinator::create([
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'school_id' => $data['school_id'] ?? null,
                    'unique_key' => $uniqueKey,
                ]);
                
                $coordinatorId = $coordinator->coordinator_id;
            }

            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'user_role' => $roleMap[$data['role']] ?? 'Intern',
                'school_id' => $data['school_id'] ?? null,
                'coordinator_id' => $coordinatorId ?? ($data['assign_coordinator_id'] ?? null),
            ]);
            
            DB::commit();
            
            return redirect()->route('hr.users.index')->with('success', 'User created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('User creation failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withInput()->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()]);
        }
    }

    public function edit(User $user)
    {
        $schools = School::all();
        $coordinators = Coordinator::with('school')->get();
        return view('hr.users.edit', compact('user', 'schools', 'coordinators'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:tbl_user,email,'.$user->user_id.',user_id',
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|string',
            'school_id' => 'nullable|exists:tbl_school,school_id',
            'assign_coordinator_id' => 'nullable|exists:tbl_coordinator,coordinator_id',
        ]);

        $roleMap = [
            'admin' => 'Admin',
            'hr' => 'HR',
            'coordinator' => 'Coordinator',
            'student' => 'Intern',
        ];

        DB::beginTransaction();
        
        try {
            $newRole = $roleMap[$data['role']] ?? 'Intern';
            $oldRole = $user->user_role;
            
            if ($newRole === 'Coordinator' && $oldRole !== 'Coordinator') {
                $uniqueKey = strtoupper(Str::random(8));
                
                $coordinator = Coordinator::create([
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'school_id' => $data['school_id'] ?? null,
                    'unique_key' => $uniqueKey,
                ]);
                
                $user->coordinator_id = $coordinator->coordinator_id;
            } elseif ($newRole === 'Coordinator' && $oldRole === 'Coordinator' && $user->coordinator_id) {
                $coordinator = Coordinator::find($user->coordinator_id);
                if ($coordinator) {
                    $coordinator->update([
                        'first_name' => $data['first_name'],
                        'last_name' => $data['last_name'],
                        'email' => $data['email'],
                        'school_id' => $data['school_id'] ?? null,
                    ]);
                }
            } elseif ($newRole !== 'Coordinator' && $oldRole === 'Coordinator' && $user->coordinator_id) {
                $user->coordinator_id = null;
            }

            $user->first_name = $data['first_name'];
            $user->last_name = $data['last_name'];
            $user->email = $data['email'];
            $user->user_role = $newRole;
            $user->school_id = $data['school_id'] ?? null;
            
            if ($newRole !== 'Coordinator') {
                $user->coordinator_id = $data['assign_coordinator_id'] ?? null;
            }
            
            if (!empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }
            
            $user->save();
            
            DB::commit();
            
            return redirect()->route('hr.users.index')->with('success', 'User updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to update user: ' . $e->getMessage()]);
        }
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('hr.users.index')->with('success', 'User deleted');
    }
}
