<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Actions\RegisterUserAction;
use App\Models\School;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

#[Layout('components.layouts.app')]
class Register extends Component
{
    public $user_role = null; // Default role
    public $first_name;
    public $last_name;
    public $email;
    public $contact_number;
    public $password;
    public $password_confirmation;
    public $selected_school;
    public $new_school_name;
    public $new_school_address;

    public $schools = [];

    public function mount()
    {
        $this->schools = School::all()->map(function($school) {
            return [
                'school_id' => $school->school_id,
                'school_name' => $school->school_name,
            ];
        })->toArray();
    }

    public function updatedUserRole($value)
    {
        // Reset everything when switching roles to prevent "sticky" fields
        $this->reset(['selected_school', 'new_school_name', 'new_school_address']);
    }

    public function updatedSelectedSchool($value)
    {
        if ($value !== 'other') {
            $this->new_school_name = null;
            $this->new_school_address = null;
        }
    }

    public function rules()
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('tbl_user', 'email'),
            ],
            'contact_number' => 'required|string|min:11|max:15|regex:/^[0-9]+$/',
            'password' => 'required|string|min:8|confirmed',
        ];

        if ($this->user_role === 'Intern') {
            $rules['selected_school'] = 'required';
            if ($this->selected_school === 'other') {
                $rules['new_school_name'] = 'required|string|max:255';
                $rules['new_school_address'] = 'required|string|max:255';
            }
        } elseif ($this->user_role === 'Coordinator') {
            $rules['selected_school'] = 'required|exists:tbl_school,school_id';
        }

        return $rules;
    }

    public function register()
    {
        $this->validate();

        $action = new RegisterUserAction();
        $user = $action->execute([
            'user_role' => $this->user_role,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'contact_number' => $this->contact_number,
            'password' => Hash::make($this->password),
            'selected_school' => $this->selected_school,
            'new_school_name' => $this->new_school_name,
            'new_school_address' => $this->new_school_address,
        ]);

        if ($this->user_role === 'Intern') {
            return redirect()->to('http://localhost:8000/applicant/login');
        } else {
            return redirect()->to('http://localhost:8000/coordinator/login');
        }
    }

    public function render()
    {
        return view('livewire.register');
    }
}