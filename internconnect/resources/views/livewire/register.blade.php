<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-lg p-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Register for InternConnect</h2>
            <p class="mt-2 text-sm text-gray-600">Create your account as an Intern or School Coordinator</p>
        </div>

        <form wire:submit.prevent="register" class="space-y-4">
            <!-- Role Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Your Role</label>
                <div class="flex justify-center space-x-4">
                    <label class="flex items-center">
                        <input type="radio" name="user_role" wire:model.live="user_role" value="Intern" class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">Intern</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="user_role" wire:model.live="user_role" value="Coordinator" class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">School Coordinator</span>
                    </label>
                </div>
                @error('user_role') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- First Name -->
            <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                <input type="text" wire:model="first_name" id="first_name" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500">
                @error('first_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Last Name -->
            <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                <input type="text" wire:model="last_name" id="last_name" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500">
                @error('last_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" wire:model="email" id="email" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500">
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Contact Number -->
            <div>
                <label for="contact_number" class="block text-sm font-medium text-gray-700">Contact Number</label>
                <input type="text" wire:model="contact_number" id="contact_number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500">
                @error('contact_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- School Selection (only for Interns) -->
            @if($user_role === 'Intern')
            <div>
                <label for="selected_school" class="block text-sm font-medium text-gray-700">Select Your School</label>
                <select wire:model.live="selected_school" id="selected_school" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500">
                    <option value="">Choose a school</option>
                    @foreach($schools as $school)
                        <option value="{{ $school['school_id'] }}">{{ $school['school_name'] }}</option>
                    @endforeach
                    <option value="other">School not in the list?</option>
                </select>
                @error('selected_school') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            @endif

            <!-- School Selection for Coordinators -->
            @if($user_role === 'Coordinator')
            <div>
                <label for="selected_school" class="block text-sm font-medium text-gray-700">Select Your School</label>
                <select wire:model.live="selected_school" id="selected_school" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500">
                    <option value="">Choose a school</option>
                    @foreach($schools as $school)
                        @if($school['school_id'] !== 'other')
                        <option value="{{ $school['school_id'] }}">{{ $school['school_name'] }}</option>
                        @endif
                    @endforeach
                    <option value="other">School not in the list?</option>
                </select>
                @error('selected_school') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            @endif

            <!-- New School Fields (for both roles when 'other' is selected) -->
            <div wire:key="new-school-fields-{{ $selected_school }}">
                @if($selected_school === 'other')
                <div>
                    <label for="new_school_name" class="block text-sm font-medium text-gray-700">New School Name</label>
                    <input type="text" wire:model="new_school_name" id="new_school_name" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500">
                    @error('new_school_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="new_school_address" class="block text-sm font-medium text-gray-700">New School Address</label>
                    <input type="text" wire:model="new_school_address" id="new_school_address" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500">
                    @error('new_school_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                @endif
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" wire:model="password" id="password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500">
                @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Password Confirmation -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" wire:model="password_confirmation" id="password_confirmation" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500">
                @error('password_confirmation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-black" style="background-color: #f9be0f;">
                    Register
                </button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Already have an account?
                <a href="#" class="font-medium text-teal-600 hover:text-teal-500">Sign in</a>
            </p>
        </div>
    </div>
</div>