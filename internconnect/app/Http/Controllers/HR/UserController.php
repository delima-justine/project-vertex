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
        try {
            DB::beginTransaction();

            // Delete related records first (cascade delete manually)
            // Delete notifications
            \App\Models\Notification::where('user_id', $user->user_id)->delete();
            
            // Delete job applications
            \App\Models\JobApplication::where('user_id', $user->user_id)->delete();
            
            // Delete progress records
            \App\Models\Progress::where('user_id', $user->user_id)->delete();
            
            // Delete documents
            \App\Models\Document::where('user_id', $user->user_id)->delete();
            
            // Delete the user
            $user->delete();

            DB::commit();
            return redirect()->route('hr.users.index')->with('success', 'User and related records deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('User deletion failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete user: ' . $e->getMessage()]);
        }
    }

    public function backup()
    {
        try {
            $users = User::all();
            $filename = 'users_backup_' . date('Y-m-d_H-i-s') . '.json';
            
            $backup = [
                'backup_date' => now(),
                'total_users' => count($users),
                'users' => $users
            ];

            return response()->json($backup, 200, [
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ])->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Backup failed: ' . $e->getMessage()]);
        }
    }

    public function restoreForm()
    {
        return view('hr.users.restore');
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:json'
        ]);

        try {
            $file = $request->file('backup_file');
            $content = json_decode(file_get_contents($file->getRealPath()), true);

            if (!isset($content['users']) || !is_array($content['users'])) {
                return back()->withErrors(['error' => 'Invalid backup file format']);
            }

            DB::beginTransaction();

            $restored = 0;
            foreach ($content['users'] as $userData) {
                $updateData = [
                    'first_name' => $userData['first_name'],
                    'last_name' => $userData['last_name'],
                    'email' => $userData['email'],
                    'user_role' => $userData['user_role'],
                    'school_id' => $userData['school_id'] ?? null,
                    'coordinator_id' => $userData['coordinator_id'] ?? null,
                ];

                // Include password if available in backup, otherwise use a temporary one
                if (!empty($userData['password'])) {
                    $updateData['password'] = $userData['password'];
                } else {
                    // Generate a temporary password if not in backup
                    $updateData['password'] = Hash::make('TempPassword123!' . $userData['user_id']);
                }

                $user = User::updateOrCreate(
                    ['user_id' => $userData['user_id']],
                    $updateData
                );
                $restored++;
            }

            DB::commit();

            return redirect()->route('hr.users.index')->with('success', "Successfully restored {$restored} users from backup");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('User restore failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Restore failed: ' . $e->getMessage()]);
        }
    }
}
