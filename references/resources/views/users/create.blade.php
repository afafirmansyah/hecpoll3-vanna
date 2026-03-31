@extends('layouts.app')

@section('title', 'HecPoll 3 - Create User')

@section('content')
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center mb-6">
            <a href="{{ route('users.index') }}" class="text-blue-600 hover:text-blue-800 mr-4 text-sm">
                <i class="fas fa-arrow-left mr-1"></i>Back to Users
            </a>
            <h1 class="text-2xl font-semibold text-gray-900 flex items-center">
                <i class="fas fa-user-plus w-6 h-6 mr-3 text-gray-700"></i>
                Create New User
            </h1>
        </div>

        <div class="max-w-md mx-auto">
            <div class="relative bg-white rounded-lg shadow-lg">
                <div class="px-4 py-3 border-b rounded-t-lg" style="background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);">
                    <h3 class="text-sm font-semibold text-white text-center">
                        CREATE NEW USER
                    </h3>
                </div>
                <div class="p-4">
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block mb-1 text-xs font-medium text-gray-700">NAME</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" 
                                    class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500" required>
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="email" class="block mb-1 text-xs font-medium text-gray-700">EMAIL</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" 
                                    class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500" required>
                                @error('email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="password" class="block mb-1 text-xs font-medium text-gray-700">PASSWORD</label>
                                <input type="password" name="password" id="password" 
                                    class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500" required>
                                @error('password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="role_id" class="block mb-1 text-xs font-medium text-gray-700">ROLE</label>
                                <select name="role_id" id="role_id" 
                                    class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                            {{ $role->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 mt-4">
                            <a href="{{ route('users.index') }}" 
                                class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-xs font-medium px-3 py-1.5 hover:text-gray-900">
                                <i class="fas fa-times mr-1"></i>Cancel
                            </a>
                            <button type="submit" class="text-white text-xs px-3 py-1.5 rounded" style="background: linear-gradient(135deg, #4e73df, #224abe);">
                                <i class="fas fa-save mr-1"></i>Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div id="successAlert" class="fixed top-5 text-white px-3 py-1.5 rounded-lg shadow-lg z-50 text-sm" style="left: calc(50% + 8rem); transform: translateX(-50%); background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
            <i class="fas fa-check-circle mr-1"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div id="errorAlert" class="fixed top-5 text-white px-3 py-1.5 rounded-lg shadow-lg z-50 text-sm" style="left: calc(50% + 8rem); transform: translateX(-50%); background: linear-gradient(135deg, #e74a3b 0%, #c41e3a 100%);">
            <i class="fas fa-exclamation-circle mr-1"></i>{{ session('error') }}
        </div>
    @endif

    <script>
        // Auto-hide alerts after 3 seconds
        setTimeout(function() {
            const successAlert = document.getElementById('successAlert');
            const errorAlert = document.getElementById('errorAlert');
            if (successAlert) successAlert.style.display = 'none';
            if (errorAlert) errorAlert.style.display = 'none';
        }, 3000);
    </script>

@endsection