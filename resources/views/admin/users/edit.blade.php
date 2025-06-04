<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-semibold text-gray-900">Edit User: {{ $user->username }}</h2>
                    <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Back to Users
                    </a>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg">
                <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-6 p-6">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Username -->
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                            <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('username')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Full Name -->
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $user->full_name) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('full_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $user->phone_number) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('phone_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password (Optional) -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                New Password <span class="text-gray-500">(leave blank to keep current)</span>
                            </label>
                            <input type="password" name="password" id="password"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password Confirmation -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Role -->
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                            <select name="role" id="role" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="client" {{ (old('role', $user->role) === 'client') ? 'selected' : '' }}>Client</option>
                                <option value="partner" {{ (old('role', $user->role) === 'partner') ? 'selected' : '' }}>Partner</option>
                                <option value="admin" {{ (old('role', $user->role) === 'admin') ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="active" {{ (old('status', $user->status) === 'active') ? 'selected' : '' }}>Active</option>
                                <option value="suspended" {{ (old('status', $user->status) === 'suspended') ? 'selected' : '' }}>Suspended</option>
                                <option value="banned" {{ (old('status', $user->status) === 'banned') ? 'selected' : '' }}>Banned</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Bio -->
                    <div>
                        <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                        <textarea name="bio" id="bio" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('bio', $user->bio) }}</textarea>
                        @error('bio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Profile Image Preview -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Current Profile Image</label>
                        <div class="mt-2">
                            <img src="{{ $user->profile_image }}" alt="{{ $user->username }}" class="h-32 w-32 rounded-full object-cover">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Update User
                        </button>
                    </div>
                </form>
            </div>

            <!-- Additional Information -->
            <div class="mt-6 bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">User ID</p>
                        <p class="font-medium">{{ $user->user_id }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Joined Date</p>
                        <p class="font-medium">{{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Last Updated</p>
                        <p class="font-medium">{{ $user->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Profile Completed</p>
                        <p class="font-medium">{{ $user->is_profile_completed ? 'Yes' : 'No' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
