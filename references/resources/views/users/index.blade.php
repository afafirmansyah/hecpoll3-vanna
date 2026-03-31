@extends('layouts.app')

@section('title', 'HecPoll 3 - User Management')

@section('content')
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900 flex items-center">
                <i class="fas fa-users w-6 h-6 mr-6 text-gray-900"></i>
                User Management
            </h1>

            <!-- Search Users -->
            <div class="relative">
                <form method="GET" action="{{ route('users.index') }}" id="searchForm">
                    @foreach (request()->except('search') as $key => $value)
                        @if (is_array($value))
                            @foreach ($value as $item)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search"
                            class="w-64 pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            oninput="setTimeout(() => this.form.submit(), 500)">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Filters -->
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
            <form method="GET" action="{{ route('users.index') }}" class="flex flex-wrap items-center justify-between gap-2">
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Role Filter -->
                    <div class="relative">
                        <button id="roleDropdown" data-dropdown-toggle="roleDropdownMenu" type="button"
                            class="inline-flex items-center justify-center text-gray-900 bg-white border border-gray-300 focus:ring-4 focus:ring-blue-300 font-medium rounded text-xs px-3 py-1.5 transition-all duration-300"
                            onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(20, 184, 166, 0.1) 100%)'"
                            onmouseout="this.style.background='white'">
                            <i class="fas fa-user-tag mr-1"></i>Role
                            @if (request('role'))
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-1.5 py-0.5 rounded-full ml-1">1</span>
                            @endif
                            <svg class="w-3 h-3 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="roleDropdownMenu" class="z-50 hidden bg-white border border-gray-300 rounded-lg shadow-lg w-48" style="max-height: 280px; overflow-y: auto;">
                            <ul class="p-1 text-xs text-gray-700">
                                @foreach($roles as $role)
                                    <li>
                                        <div class="inline-flex items-center w-full p-1.5 rounded transition-all duration-300"
                                            onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white'; this.querySelector('label').style.color='white';"
                                            onmouseout="this.style.background=''; this.style.color=''; this.querySelector('label').style.color='';">
                                            <input id="role-{{ $role->id }}" type="checkbox" name="role" value="{{ $role->id }}"
                                                class="w-3 h-3 border border-gray-300 rounded bg-gray-50 focus:ring-2 focus:ring-blue-500"
                                                {{ request('role') == $role->id ? 'checked' : '' }}>
                                            <label for="role-{{ $role->id }}" class="ms-2 text-xs font-medium text-gray-900">{{ $role->display_name }}</label>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="relative">
                        <button id="statusDropdown" data-dropdown-toggle="statusDropdownMenu" type="button"
                            class="inline-flex items-center justify-center text-gray-900 bg-white border border-gray-300 focus:ring-4 focus:ring-blue-300 font-medium rounded text-xs px-3 py-1.5 transition-all duration-300"
                            onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(20, 184, 166, 0.1) 100%)'"
                            onmouseout="this.style.background='white'">
                            <i class="fas fa-toggle-on mr-1"></i>Status
                            @if (request('status'))
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-1.5 py-0.5 rounded-full ml-1">1</span>
                            @endif
                            <svg class="w-3 h-3 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="statusDropdownMenu" class="z-50 hidden bg-white border border-gray-300 rounded-lg shadow-lg w-48" style="max-height: 280px; overflow-y: auto;">
                            <ul class="p-1 text-xs text-gray-700">
                                <li>
                                    <div class="inline-flex items-center w-full p-1.5 rounded transition-all duration-300"
                                        onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white'; this.querySelector('label').style.color='white';"
                                        onmouseout="this.style.background=''; this.style.color=''; this.querySelector('label').style.color='';">
                                        <input id="status-active" type="checkbox" name="status" value="1"
                                            class="w-3 h-3 border border-gray-300 rounded bg-gray-50 focus:ring-2 focus:ring-blue-500"
                                            {{ request('status') == '1' ? 'checked' : '' }}>
                                        <label for="status-active" class="ms-2 text-xs font-medium text-gray-900">Active</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="inline-flex items-center w-full p-1.5 rounded transition-all duration-300"
                                        onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white'; this.querySelector('label').style.color='white';"
                                        onmouseout="this.style.background=''; this.style.color=''; this.querySelector('label').style.color='';">
                                        <input id="status-inactive" type="checkbox" name="status" value="0"
                                            class="w-3 h-3 border border-gray-300 rounded bg-gray-50 focus:ring-2 focus:ring-blue-500"
                                            {{ request('status') == '0' ? 'checked' : '' }}>
                                        <label for="status-inactive" class="ms-2 text-xs font-medium text-gray-900">Inactive</label>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Access Filter -->
                    <div class="relative">
                        <button id="accessDropdown" data-dropdown-toggle="accessDropdownMenu" type="button"
                            class="inline-flex items-center justify-center text-gray-900 bg-white border border-gray-300 focus:ring-4 focus:ring-blue-300 font-medium rounded text-xs px-3 py-1.5 transition-all duration-300"
                            onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(20, 184, 166, 0.1) 100%)'"
                            onmouseout="this.style.background='white'">
                            <i class="fas fa-key mr-1"></i>Access
                            @if (request('permissions'))
                                <span class="bg-purple-100 text-purple-800 text-xs font-medium px-1.5 py-0.5 rounded-full ml-1">{{ count(request('permissions')) }}</span>
                            @endif
                            <svg class="w-3 h-3 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="accessDropdownMenu" class="z-50 hidden bg-white border border-gray-300 rounded-lg shadow-lg w-48" style="max-height: 280px; overflow-y: auto;">
                            <ul class="p-1 text-xs text-gray-700">
                                @php
                                    $permissions = \App\Models\Permission::all()->sortBy(function($permission) {
                                        if (str_starts_with($permission->name, 'view_')) return '1_' . $permission->name;
                                        if (str_starts_with($permission->name, 'export_')) return '2_' . $permission->name;
                                        if (str_starts_with($permission->name, 'update_') || str_starts_with($permission->name, 'edit_')) return '3_' . $permission->name;
                                        if (str_starts_with($permission->name, 'manage_')) return '4_' . $permission->name;
                                        return '5_' . $permission->name;
                                    });
                                @endphp
                                @foreach($permissions as $permission)
                                    <li>
                                        <div class="inline-flex items-center w-full p-1.5 rounded transition-all duration-300"
                                            onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white'; this.querySelector('label').style.color='white';"
                                            onmouseout="this.style.background=''; this.style.color=''; this.querySelector('label').style.color='';">
                                            <input id="permission-{{ $permission->id }}" type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                class="w-3 h-3 border border-gray-300 rounded bg-gray-50 focus:ring-2 focus:ring-blue-500"
                                                {{ in_array($permission->name, request('permissions', [])) ? 'checked' : '' }}>
                                            <label for="permission-{{ $permission->id }}" class="ms-2 text-xs font-medium text-gray-900">{{ $permission->display_name }}</label>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <button type="submit"
                        class="text-white font-medium rounded text-xs px-3 py-1.5 focus:outline-none transition-all duration-300"
                        style="background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);"
                        onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.8) 0%, rgba(20, 184, 166, 0.8) 100%)';"
                        onmouseout="this.style.background='linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%)';"
                        onfocus="this.style.boxShadow='0 0 0 2px rgba(59, 130, 246, 0.3)';" onblur="this.style.boxShadow='';">
                        <i class="fas fa-search mr-1"></i>Apply
                    </button>
                    <a href="{{ route('users.index') }}"
                        class="text-white font-medium rounded text-xs px-3 py-1.5 focus:outline-none transition-all duration-300 inline-block"
                        style="background: linear-gradient(135deg, #F43F5E 0%, #F97316 100%);"
                        onmouseover="this.style.background='linear-gradient(135deg, rgba(244, 63, 94, 0.8) 0%, rgba(249, 115, 22, 0.8) 100%)';"
                        onmouseout="this.style.background='linear-gradient(135deg, #F43F5E 0%, #F97316 100%)';"
                        onfocus="this.style.boxShadow='0 0 0 2px rgba(244, 63, 94, 0.3)';" onblur="this.style.boxShadow='';">
                        <i class="fas fa-times mr-1"></i>Clear
                    </a>
                </div>
                
                <div class="flex items-center gap-2">
                    <button type="button" id="deleteBtn" class="hidden text-white font-medium rounded text-xs px-3 py-1.5 focus:outline-none transition-all duration-300"
                        style="background: linear-gradient(135deg, #e74a3b, #c41e3a);"
                        onmouseover="this.style.background='linear-gradient(135deg, rgba(231, 74, 59, 0.8), rgba(196, 30, 58, 0.8))';"
                        onmouseout="this.style.background='linear-gradient(135deg, #e74a3b, #c41e3a)';"
                        onfocus="this.style.boxShadow='0 0 0 2px rgba(231, 74, 59, 0.3)';" onblur="this.style.boxShadow='';">
                        <i class="fas fa-trash mr-1"></i>Delete
                    </button>
                    <button type="button" onclick="openRoleManagement()"
                        class="text-white font-medium rounded text-xs px-3 py-1.5 focus:outline-none transition-all duration-300"
                        style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);"
                        onmouseover="this.style.background='linear-gradient(135deg, rgba(16, 185, 129, 0.8) 0%, rgba(5, 150, 105, 0.8) 100%)';"
                        onmouseout="this.style.background='linear-gradient(135deg, #10B981 0%, #059669 100%)';"
                        onfocus="this.style.boxShadow='0 0 0 2px rgba(16, 185, 129, 0.3)';" onblur="this.style.boxShadow='';">
                        <i class="fas fa-user-tag mr-1"></i>Manage Roles
                    </button>
                    <button type="button" data-modal-target="userModal" data-modal-toggle="userModal" onclick="openAddModal()"
                        class="text-white font-medium rounded text-xs px-3 py-1.5 focus:outline-none transition-all duration-300"
                        style="background: linear-gradient(135deg, #4e73df, #224abe);"
                        onmouseover="this.style.background='linear-gradient(135deg, rgba(78, 115, 223, 0.8), rgba(34, 74, 190, 0.8))';"
                        onmouseout="this.style.background='linear-gradient(135deg, #4e73df, #224abe)';"
                        onfocus="this.style.boxShadow='0 0 0 2px rgba(78, 115, 223, 0.3)';" onblur="this.style.boxShadow='';">
                        <i class="fas fa-plus mr-1"></i>Add User
                    </button>
                </div>
            </form>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <div class="overflow-y-auto" style="height: calc(100vh - 320px);">
                <table class="w-full text-xs text-left rtl:text-right text-gray-500 dark:text-gray-400"
                    style="white-space: nowrap;">
                    <thead class="text-xs text-white uppercase dark:bg-gray-700 dark:text-gray-400 sticky top-0 z-10"
                        style="background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);">
                        <tr>
                            <th scope="col" class="px-3 py-2 text-center" style="white-space: nowrap;">
                            </th>
                            <th scope="col" class="px-6 py-2 text-left" style="white-space: nowrap;">Name</th>
                            <th scope="col" class="px-6 py-2 text-left" style="white-space: nowrap;">Username</th>
                            <th scope="col" class="px-6 py-2 text-left" style="white-space: nowrap;">Email</th>
                            <th scope="col" class="px-6 py-2 text-center" style="white-space: nowrap;">Role</th>
                            <th scope="col" class="px-6 py-2 text-center" style="white-space: nowrap;">Status</th>
                            <th scope="col" class="px-6 py-2 text-center" style="white-space: nowrap;">Created</th>
                            <th scope="col" class="px-6 py-2 text-center" style="white-space: nowrap;">Last Login</th>
                            <th scope="col" class="px-6 py-2 text-center" style="white-space: nowrap;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr class="{{ $loop->odd ? 'bg-gray-50' : 'bg-white' }} border-b dark:bg-gray-800 dark:border-gray-700 transition-all duration-300"
                                onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(20, 184, 166, 0.1) 100%)'"
                                onmouseout="this.style.background='{{ $loop->odd ? '#f9fafb' : '#ffffff' }}';">
                                <td class="px-3 py-2 text-center" style="white-space: nowrap;">
                                    @if($user->id !== auth()->id())
                                        <input type="checkbox" class="row-checkbox w-3 h-3" value="{{ $user->id }}">
                                    @endif
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400" style="white-space: nowrap;">
                                    {{ $user->name }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400" style="white-space: nowrap;">
                                    {{ $user->username ?? '-' }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400" style="white-space: nowrap;">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-2 text-center" style="white-space: nowrap;">
                                    @if($user->role)
                                        @if($user->role->name === 'administrator')
                                            <span class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                                {{ $user->role->display_name }}
                                            </span>
                                        @elseif($user->role->name === 'editor')
                                            <span class="bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                                {{ $user->role->display_name }}
                                            </span>
                                        @elseif($user->role->name === 'viewer')
                                            <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                                {{ $user->role->display_name }}
                                            </span>
                                        @else
                                            <span class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                                                {{ $user->role->display_name }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                                            No Role
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-2 text-center" style="white-space: nowrap;">
                                    @if($user->is_active)
                                        <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                            Active
                                        </span>
                                    @else
                                        <span class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400 text-center" style="white-space: nowrap;">
                                    {{ $user->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400 text-center" style="white-space: nowrap;">
                                    {{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never' }}
                                </td>
                                <td class="px-6 py-2 text-center" style="white-space: nowrap;">
                                    <button type="button" onclick="editUser({{ $user->id }}, '{{ $user->name }}', '{{ $user->username }}', '{{ $user->email }}', {{ $user->role_id }}, {{ $user->is_active ? 'true' : 'false' }})" 
                                        class="text-white font-medium rounded text-xs px-2.5 py-0.5 focus:outline-none transition-all duration-300 mr-2"
                                        style="background: linear-gradient(135deg, #4e73df, #224abe);"
                                        onmouseover="this.style.background='linear-gradient(135deg, rgba(78, 115, 223, 0.8), rgba(34, 74, 190, 0.8))';"
                                        onmouseout="this.style.background='linear-gradient(135deg, #4e73df, #224abe)';"
                                        onfocus="this.style.boxShadow='0 0 0 2px rgba(78, 115, 223, 0.3)';" onblur="this.style.boxShadow='';">                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </button>
                                    <button type="button" onclick="editAccess({{ $user->id }}, '{{ $user->name }}')" 
                                        class="text-white font-medium rounded text-xs px-2.5 py-0.5 focus:outline-none transition-all duration-300 mr-1"
                                        style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);"
                                        onmouseover="this.style.background='linear-gradient(135deg, rgba(16, 185, 129, 0.8) 0%, rgba(5, 150, 105, 0.8) 100%)';"
                                        onmouseout="this.style.background='linear-gradient(135deg, #10B981 0%, #059669 100%)';"
                                        onfocus="this.style.boxShadow='0 0 0 2px rgba(16, 185, 129, 0.3)';" onblur="this.style.boxShadow='';">                                        <i class="fas fa-key mr-1"></i>Access
                                    </button>
                                    <button type="button" onclick="editStationAccess({{ $user->id }}, '{{ $user->name }}')" 
                                        class="text-white font-medium rounded text-xs px-2.5 py-0.5 focus:outline-none transition-all duration-300"
                                        style="background: linear-gradient(135deg, #8B5CF6 0%, #A855F7 100%);"
                                        onmouseover="this.style.background='linear-gradient(135deg, rgba(139, 92, 246, 0.8) 0%, rgba(168, 85, 247, 0.8) 100%)';"
                                        onmouseout="this.style.background='linear-gradient(135deg, #8B5CF6 0%, #A855F7 100%)';"
                                        onfocus="this.style.boxShadow='0 0 0 2px rgba(139, 92, 246, 0.3)';" onblur="this.style.boxShadow='';">                                        <i class="fas fa-map-marker-alt mr-1"></i>Station
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-white">
                                <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No users found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $users->links('custom-pagination') }}
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

    <!-- User Modal -->
    <div id="userModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-xs max-h-full" style="margin-left: 60%;">
            <div class="relative bg-white rounded-lg shadow-lg">
                <div class="px-4 py-3 border-b rounded-t-lg" style="background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);">
                    <h3 id="modalTitle" class="text-sm font-semibold text-white text-center">
                        ADD USER
                    </h3>
                </div>
                <div class="p-4">
                    <form id="userForm" method="POST">
                        @csrf
                        <input type="hidden" id="methodField" name="_method" value="" disabled>
                        <input type="hidden" id="userId" name="user_id" value="">
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block mb-1 text-xs font-medium text-gray-700">NAME</label>
                                <input type="text" name="name" id="name" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500" required>
                                <div id="nameError" class="text-red-500 text-xs mt-1 hidden"></div>
                            </div>
                            <div>
                                <label for="username" class="block mb-1 text-xs font-medium text-gray-700">USERNAME</label>
                                <input type="text" name="username" id="username" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                <div id="usernameError" class="text-red-500 text-xs mt-1 hidden"></div>
                            </div>
                            <div>
                                <label for="email" class="block mb-1 text-xs font-medium text-gray-700">EMAIL</label>
                                <input type="email" name="email" id="email" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500" required>
                                <div id="emailError" class="text-red-500 text-xs mt-1 hidden"></div>
                            </div>
                            <div>
                                <label for="password" class="block mb-1 text-xs font-medium text-gray-700">PASSWORD</label>
                                <input type="password" name="password" id="password" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                <p id="passwordHelp" class="text-xs text-gray-500 mt-1 hidden">Leave blank to keep current password</p>
                                <div id="passwordError" class="text-red-500 text-xs mt-1 hidden"></div>
                            </div>
                            <div>
                                <label for="role_id" class="block mb-1 text-xs font-medium text-gray-700">ROLE</label>
                                <div class="relative">
                                    <button id="modalRoleDropdown" data-dropdown-toggle="modalRoleDropdownMenu" type="button"
                                        class="w-full inline-flex items-center justify-between text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-1 focus:ring-blue-500 font-medium rounded text-xs px-2 py-1.5">
                                        <span id="selectedRoleText">Select Role</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <input type="hidden" name="role_id" id="role_id" required>
                                    <div id="modalRoleDropdownMenu" class="z-50 hidden bg-white border border-gray-300 rounded-lg shadow-lg w-full" style="max-height: 200px; overflow-y: auto;">
                                        <ul class="p-1 text-xs text-gray-700">
                                            @foreach($roles as $role)
                                                <li>
                                                    <div class="inline-flex items-center w-full p-1.5 rounded transition-all duration-300 cursor-pointer"
                                                        onclick="selectRole({{ $role->id }}, '{{ $role->display_name }}')"
                                                        onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white'; this.querySelector('span').style.color='white';"
                                                        onmouseout="this.style.background=''; this.style.color=''; this.querySelector('span').style.color='';">
                                                        <span class="text-xs font-medium text-gray-900">{{ $role->display_name }}</span>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div id="roleError" class="text-red-500 text-xs mt-1 hidden"></div>
                                </div>
                            </div>
                            <div id="activeField" class="hidden">
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_active" id="is_active" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 w-3 h-3">
                                    <span class="ml-2 text-xs text-gray-700">Active</span>
                                </label>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 mt-4">
                            <button type="button" data-modal-hide="userModal" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-xs font-medium px-3 py-1.5 hover:text-gray-900">
                                <i class="fas fa-times mr-1"></i>Cancel
                            </button>
                            <button type="submit" class="text-white text-xs px-3 py-1.5 rounded" style="background: linear-gradient(135deg, #4e73df, #224abe);">
                                <i class="fas fa-save mr-1"></i><span id="submitText">Save</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Access Modal -->
    <div id="accessModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-4xl max-h-full">
            <div class="relative bg-white rounded-lg shadow-lg">
                <div class="px-4 py-3 border-b rounded-t-lg" style="background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);">
                    <h3 id="accessModalTitle" class="text-sm font-semibold text-white text-center">
                        EDIT ACCESS
                    </h3>
                </div>
                <div class="p-4">
                    <form id="accessForm" method="POST">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" id="accessUserId" name="user_id" value="">
                        <div class="grid grid-cols-2 gap-4">
                            @php
                                $cleanPermissions = \App\Models\Permission::whereNotIn('name', ['view_only', 'edit_transactions'])
                                    ->whereNotLike('name', '%.%')
                                    ->get()
                                    ->groupBy(function($permission) {
                                        if (str_starts_with($permission->name, 'view_')) return 'View Permissions';
                                        if (str_starts_with($permission->name, 'export_')) return 'Export Permissions';
                                        if (str_starts_with($permission->name, 'edit_') || str_starts_with($permission->name, 'manage_') || str_starts_with($permission->name, 'update_')) return 'Management Permissions';
                                        return 'Other Permissions';
                                    });
                            @endphp
                            @foreach($cleanPermissions as $group => $perms)
                                <div class="border rounded p-3">
                                    <div class="text-xs font-medium text-gray-700 mb-2">{{ strtoupper($group) }}</div>
                                    <div class="grid grid-cols-1 gap-1">
                                        @foreach($perms->sortBy('display_name') as $permission)
                                            <label class="flex items-center text-xs hover:bg-gray-50 p-1 rounded">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="w-3 h-3 mr-2 permission-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                <span class="text-gray-700">{{ $permission->display_name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex justify-end gap-2 mt-4">
                            <button type="button" onclick="closeAccessModal()" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-xs font-medium px-3 py-1.5 hover:text-gray-900">
                                <i class="fas fa-times mr-1"></i>Cancel
                            </button>
                            <button type="submit" class="text-white text-xs px-3 py-1.5 rounded" style="background: linear-gradient(135deg, #4e73df, #224abe);">
                                <i class="fas fa-save mr-1"></i>Save Access
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Station Access Modal -->
    <div id="stationAccessModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-4xl max-h-full">
            <div class="relative bg-white rounded-lg shadow-lg">
                <div class="px-4 py-3 border-b rounded-t-lg" style="background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);">
                    <h3 id="stationAccessModalTitle" class="text-sm font-semibold text-white text-center">
                        EDIT STATION ACCESS
                    </h3>
                </div>
                <div class="p-4">
                    <form id="stationAccessForm" method="POST">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" id="stationAccessUserId" name="user_id" value="">
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Stations -->
                            <div class="border rounded p-3">
                                <div class="text-xs font-medium text-gray-700 mb-2">STATIONS ACCESS</div>
                                <div id="stationsContainer" class="grid grid-cols-1 gap-1 max-h-64 overflow-y-auto"></div>
                            </div>
                            <!-- Terminals -->
                            <div class="border rounded p-3">
                                <div class="text-xs font-medium text-gray-700 mb-2">TERMINALS ACCESS</div>
                                <div id="terminalsContainer" class="grid grid-cols-1 gap-1 max-h-64 overflow-y-auto"></div>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 mt-4">
                            <button type="button" onclick="closeStationAccessModal()" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-xs font-medium px-3 py-1.5 hover:text-gray-900">
                                <i class="fas fa-times mr-1"></i>Cancel
                            </button>
                            <button type="submit" class="text-white text-xs px-3 py-1.5 rounded" style="background: linear-gradient(135deg, #4e73df, #224abe);">
                                <i class="fas fa-save mr-1"></i>Save Access
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <div class="p-4 md:p-5 text-center">
                    <i class="fas fa-exclamation-triangle mx-auto mb-4 text-gray-400 w-12 h-12 text-4xl"></i>
                    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">
                        Are you sure you want to delete <span id="deleteCount" class="font-semibold">0</span> selected user(s)?
                    </h3>
                    <button id="confirmDelete" type="button" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center me-2">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                    <button id="cancelDelete" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Role Management Modal -->
    <div id="roleManagementModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-lg shadow-lg">
                <div class="px-4 py-3 border-b rounded-t-lg" style="background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);">
                    <h3 class="text-sm font-semibold text-white text-center">
                        ROLE MANAGEMENT
                    </h3>
                </div>
                <div class="p-4">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-sm font-medium text-gray-700">Existing Roles</h4>
                        <button type="button" onclick="openAddRoleModal()"
                            class="text-white font-medium rounded text-xs px-3 py-1.5 focus:outline-none transition-all duration-300"
                            style="background: linear-gradient(135deg, #4e73df, #224abe);"
                            onmouseover="this.style.background='linear-gradient(135deg, rgba(78, 115, 223, 0.8), rgba(34, 74, 190, 0.8))';"
                            onmouseout="this.style.background='linear-gradient(135deg, #4e73df, #224abe)';"
                            onfocus="this.style.boxShadow='0 0 0 2px rgba(78, 115, 223, 0.3)';" onblur="this.style.boxShadow='';">
                            <i class="fas fa-plus mr-1"></i>Add Role
                        </button>
                    </div>
                    <div id="rolesContainer" class="space-y-2 max-h-64 overflow-y-auto">
                        <!-- Roles will be loaded here -->
                    </div>
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" onclick="closeRoleManagement()" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-xs font-medium px-3 py-1.5 hover:text-gray-900">
                            <i class="fas fa-times mr-1"></i>Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Role Modal -->
    <div id="roleModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-xs max-h-full" style="margin-left: 60%;">
            <div class="relative bg-white rounded-lg shadow-lg">
                <div class="px-4 py-3 border-b rounded-t-lg" style="background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);">
                    <h3 id="roleModalTitle" class="text-sm font-semibold text-white text-center">
                        ADD ROLE
                    </h3>
                </div>
                <div class="p-4">
                    <form id="roleForm">
                        @csrf
                        <input type="hidden" id="roleId" name="role_id" value="">
                        <input type="hidden" id="roleMethodField" name="_method" value="" disabled>
                        <div class="space-y-4">
                            <div>
                                <label for="roleName" class="block mb-1 text-xs font-medium text-gray-700">ROLE NAME</label>
                                <input type="text" name="name" id="roleName" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500" required>
                                <div id="roleNameError" class="text-red-500 text-xs mt-1 hidden"></div>
                            </div>
                            <div>
                                <label for="roleDisplayName" class="block mb-1 text-xs font-medium text-gray-700">DISPLAY NAME</label>
                                <input type="text" name="display_name" id="roleDisplayName" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500" required>
                                <div id="roleDisplayNameError" class="text-red-500 text-xs mt-1 hidden"></div>
                            </div>
                            <div>
                                <label for="roleDescription" class="block mb-1 text-xs font-medium text-gray-700">DESCRIPTION</label>
                                <textarea name="description" id="roleDescription" rows="3" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500"></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 mt-4">
                            <button type="button" onclick="closeRoleModal()" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-xs font-medium px-3 py-1.5 hover:text-gray-900">
                                <i class="fas fa-times mr-1"></i>Cancel
                            </button>
                            <button type="submit" class="text-white text-xs px-3 py-1.5 rounded" style="background: linear-gradient(135deg, #4e73df, #224abe);">
                                <i class="fas fa-save mr-1"></i><span id="roleSubmitText">Save</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Role Confirmation Modal -->
    <div id="deleteRoleModal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <div class="p-4 md:p-5 text-center">
                    <i class="fas fa-exclamation-triangle mx-auto mb-4 text-gray-400 w-12 h-12 text-4xl"></i>
                    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">
                        Are you sure you want to delete role "<span id="deleteRoleName" class="font-semibold"></span>"?
                    </h3>
                    <p class="mb-5 text-sm text-red-600">This action cannot be undone and may affect users assigned to this role.</p>
                    <button id="confirmDeleteRole" type="button" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center me-2">
                        <i class="fas fa-trash mr-2"></i>Delete Role
                    </button>
                    <button id="cancelDeleteRole" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Checkbox functionality
        document.querySelectorAll('.row-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
                const deleteBtn = document.getElementById('deleteBtn');
                
                deleteBtn.classList.toggle('hidden', checkedBoxes.length === 0);
            });
        });

        // Delete confirmation
        document.getElementById('deleteBtn').addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
            const userIds = Array.from(checkedBoxes).map(cb => cb.value);
            document.getElementById('deleteCount').textContent = userIds.length;
            
            // Store user IDs for deletion
            window.selectedUserIds = userIds;
            
            // Show delete modal
            const modal = document.getElementById('deleteModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
        
        // Confirm delete
        document.getElementById('confirmDelete').addEventListener('click', function() {
            const userIds = window.selectedUserIds;
            
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("users.bulk-delete") }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            userIds.forEach(userId => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'user_ids[]';
                input.value = userId;
                form.appendChild(input);
            });
            
            document.body.appendChild(form);
            form.submit();
        });
        
        // Cancel delete
        document.getElementById('cancelDelete').addEventListener('click', function() {
            const modal = document.getElementById('deleteModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        });

        // Open add modal
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'ADD USER';
            document.getElementById('userForm').action = '{{ route("users.store") }}';
            document.getElementById('methodField').disabled = true;
            document.getElementById('userId').value = '';
            document.getElementById('role_id').value = '';
            document.getElementById('password').required = true;
            document.getElementById('passwordHelp').classList.add('hidden');
            document.getElementById('activeField').classList.add('hidden');
            document.getElementById('submitText').textContent = 'Save';
            
            // Reset form and errors
            document.getElementById('userForm').reset();
            document.getElementById('selectedRoleText').textContent = 'Select Role';
            hideErrors();
            
            // Show modal
            const modal = document.getElementById('userModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        
        // Hide error messages
        function hideErrors() {
            document.getElementById('nameError').classList.add('hidden');
            document.getElementById('usernameError').classList.add('hidden');
            document.getElementById('emailError').classList.add('hidden');
            document.getElementById('passwordError').classList.add('hidden');
            document.getElementById('roleError').classList.add('hidden');
        }
        
        // Form validation and submission
        document.getElementById('userForm').addEventListener('submit', function(e) {
            e.preventDefault();
            let hasError = false;
            hideErrors();
            
            // Check name
            if (!document.getElementById('name').value.trim()) {
                document.getElementById('nameError').textContent = 'Name is required';
                document.getElementById('nameError').classList.remove('hidden');
                hasError = true;
            }
            
            // Check email
            if (!document.getElementById('email').value.trim()) {
                document.getElementById('emailError').textContent = 'Email is required';
                document.getElementById('emailError').classList.remove('hidden');
                hasError = true;
            }
            
            // Check password for add user
            if (document.getElementById('password').required && !document.getElementById('password').value.trim()) {
                document.getElementById('passwordError').textContent = 'Password is required';
                document.getElementById('passwordError').classList.remove('hidden');
                hasError = true;
            }
            
            // Check role
            if (!document.getElementById('role_id').value) {
                document.getElementById('roleError').textContent = 'Role is required';
                document.getElementById('roleError').classList.remove('hidden');
                hasError = true;
            }
            
            if (hasError) {
                return false;
            }
            
            // Submit form via AJAX
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    closeModal();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(data.message || 'An error occurred', 'error');
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            const errorDiv = document.getElementById(key + 'Error');
                            if (errorDiv) {
                                errorDiv.textContent = data.errors[key][0];
                                errorDiv.classList.remove('hidden');
                            }
                        });
                    }
                }
            })
            .catch(error => {
                showNotification('Network error occurred', 'error');
            });
        });
        
        // Show notification
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed top-5 text-white px-3 py-1.5 rounded-lg shadow-lg z-50 text-sm`;
            notification.style.left = 'calc(50% + 8rem)';
            notification.style.transform = 'translateX(-50%)';
            
            if (type === 'success') {
                notification.style.background = 'linear-gradient(135deg, #10B981 0%, #059669 100%)';
                notification.innerHTML = `<i class="fas fa-check-circle mr-1"></i>${message}`;
            } else if (type === 'info') {
                notification.style.background = 'linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%)';
                notification.innerHTML = `<i class="fas fa-info-circle mr-1"></i>${message}`;
            } else {
                notification.style.background = 'linear-gradient(135deg, #e74a3b 0%, #c41e3a 100%)';
                notification.innerHTML = `<i class="fas fa-exclamation-circle mr-1"></i>${message}`;
            }
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Open edit modal
        function editUser(id, name, username, email, roleId, isActive) {
            document.getElementById('modalTitle').textContent = 'EDIT USER';
            document.getElementById('userForm').action = `/users/${id}`;
            document.getElementById('methodField').value = 'PUT';
            document.getElementById('methodField').disabled = false;
            document.getElementById('userId').value = id;
            document.getElementById('name').value = name;
            document.getElementById('username').value = username || '';
            document.getElementById('email').value = email;
            document.getElementById('role_id').value = roleId;
            
            // Set role dropdown text
            const roleText = @json($roles->pluck('display_name', 'id'));
            document.getElementById('selectedRoleText').textContent = roleText[roleId] || 'Select Role';
            
            document.getElementById('is_active').checked = isActive;
            document.getElementById('password').required = false;
            document.getElementById('passwordHelp').classList.remove('hidden');
            document.getElementById('activeField').classList.remove('hidden');
            document.getElementById('submitText').textContent = 'Update';
            
            // Show modal
            const modal = document.getElementById('userModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        // Close modal
        function closeModal() {
            const modal = document.getElementById('userModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        
        // Auto-hide alerts after 3 seconds
        setTimeout(function() {
            const successAlert = document.getElementById('successAlert');
            const errorAlert = document.getElementById('errorAlert');
            if (successAlert) successAlert.style.display = 'none';
            if (errorAlert) errorAlert.style.display = 'none';
        }, 3000);

        // Role selection for modal
        function selectRole(roleId, roleName) {
            document.getElementById('role_id').value = roleId;
            document.getElementById('selectedRoleText').textContent = roleName;
            document.getElementById('modalRoleDropdownMenu').classList.add('hidden');
            document.getElementById('roleError').classList.add('hidden');
        }

        // Edit Access function
        function editAccess(userId, userName) {
            document.getElementById('accessModalTitle').textContent = `EDIT ACCESS - ${userName}`;
            document.getElementById('accessForm').action = `/users/${userId}/permissions`;
            document.getElementById('accessUserId').value = userId;
            
            // Reset checkboxes
            document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
            
            // Load user permissions
            fetch(`/users/${userId}/permissions`)
                .then(response => response.json())
                .then(data => {
                    console.log('Loaded permissions:', data.permissions);
                    data.permissions.forEach(permissionId => {
                        const checkbox = document.querySelector(`input[name="permissions[]"][value="${permissionId}"]`);
                        console.log('Looking for checkbox with value:', permissionId, 'Found:', checkbox);
                        if (checkbox) {
                            checkbox.checked = true;
                        }
                    });
                })
                .catch(error => {
                    console.error('Error loading permissions:', error);
                });
            
            // Show modal
            const modal = document.getElementById('accessModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        
        // Close access modal
        function closeAccessModal() {
            const modal = document.getElementById('accessModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        
        // Access form submission
        document.getElementById('accessForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    closeAccessModal();
                } else {
                    showNotification(data.message || 'An error occurred', 'error');
                }
            })
            .catch(error => {
                showNotification('Network error occurred', 'error');
            });
        });

        // Close modal when clicking outside
        document.getElementById('userModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
        
        document.getElementById('accessModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAccessModal();
            }
        });
        
        document.getElementById('stationAccessModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeStationAccessModal();
            }
        });
        
        // Station Access functions
        function editStationAccess(userId, userName) {
            document.getElementById('stationAccessModalTitle').textContent = `EDIT STATION ACCESS - ${userName}`;
            document.getElementById('stationAccessForm').action = `/users/${userId}/access`;
            document.getElementById('stationAccessUserId').value = userId;
            
            // Load stations and terminals
            fetch(`/users/${userId}/access`)
                .then(response => response.json())
                .then(data => {
                    populateStationAccess(data.stations, data.terminals, data.access);
                    const modal = document.getElementById('stationAccessModal');
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                })
                .catch(error => {
                    console.error('Error loading access data:', error);
                });
        }
        
        let allTerminals = [];
        let selectedAccess = {};
        
        function populateStationAccess(stations, terminals, access) {
            
            allTerminals = terminals;
            selectedAccess = access;
            
            const stationsContainer = document.getElementById('stationsContainer');
            const terminalsContainer = document.getElementById('terminalsContainer');
            
            stationsContainer.innerHTML = '';
            terminalsContainer.innerHTML = '';
            
            // Auto-select stations that contain previously granted terminals
            const stationsWithGrantedTerminals = [];
            if (access.terminal_ids && access.terminal_ids.length > 0) {
                terminals.forEach(terminal => {
                    if (access.terminal_ids.includes(terminal.ID_TERMINALS.toString())) {
                        if (!stationsWithGrantedTerminals.includes(terminal.StationsID)) {
                            stationsWithGrantedTerminals.push(terminal.StationsID);
                        }
                    }
                });
            }
            
            // Populate stations
            stations.forEach(station => {
                const wasExplicitlySelected = access.station_ids && access.station_ids.includes(station.ID_STATIONS.toString());
                const hasGrantedTerminals = stationsWithGrantedTerminals.includes(station.StationsID);
                const isChecked = wasExplicitlySelected || hasGrantedTerminals;
                
                const stationDiv = document.createElement('label');
                stationDiv.className = 'flex items-center text-xs hover:bg-gray-50 p-1 rounded cursor-pointer';
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.name = 'station_ids[]';
                checkbox.value = station.ID_STATIONS;
                checkbox.checked = isChecked;
                checkbox.className = 'w-3 h-3 mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500';
                checkbox.addEventListener('change', updateTerminals);
                
                const span = document.createElement('span');
                span.className = 'text-gray-700';
                span.textContent = station.StationName;
                
                stationDiv.appendChild(checkbox);
                stationDiv.appendChild(span);
                stationsContainer.appendChild(stationDiv);
            });
            
            updateTerminals();
        }
        
        function updateTerminals() {
            const terminalsContainer = document.getElementById('terminalsContainer');
            const selectedStations = Array.from(document.querySelectorAll('input[name="station_ids[]"]:checked')).map(cb => parseInt(cb.value));
            
            // Get currently selected terminals to preserve selections
            const currentlySelectedTerminals = Array.from(document.querySelectorAll('input[name="terminal_ids[]"]:checked')).map(cb => parseInt(cb.value));
            
            terminalsContainer.innerHTML = '';
            
            if (selectedStations.length === 0) {
                terminalsContainer.innerHTML = '<div class="text-xs text-gray-500 p-2">Select stations first to see terminals</div>';
                return;
            }
            
            const filteredTerminals = allTerminals.filter(terminal => {
                return terminal.StationsID && selectedStations.includes(parseInt(terminal.StationsID));
            });
            
            if (filteredTerminals.length === 0) {
                terminalsContainer.innerHTML = '<div class="text-xs text-gray-500 p-2">No terminals found for selected stations</div>';
                return;
            }
            
            filteredTerminals.forEach(terminal => {
                // Check if terminal was originally selected OR currently selected
                const wasOriginallySelected = selectedAccess.terminal_ids && selectedAccess.terminal_ids.includes(terminal.ID_TERMINALS.toString());
                const isCurrentlySelected = currentlySelectedTerminals.includes(parseInt(terminal.ID_TERMINALS));
                const isChecked = wasOriginallySelected || isCurrentlySelected;
                
                const terminalDiv = document.createElement('label');
                terminalDiv.className = 'flex items-center text-xs hover:bg-gray-50 p-1 rounded cursor-pointer';
                terminalDiv.innerHTML = `
                    <input type="checkbox" name="terminal_ids[]" value="${terminal.ID_TERMINALS}" 
                           ${isChecked ? 'checked' : ''}
                           class="w-3 h-3 mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-gray-700">${terminal.Description}</span>
                `;
                terminalsContainer.appendChild(terminalDiv);
            });
        }
        
        function closeStationAccessModal() {
            const modal = document.getElementById('stationAccessModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        
        // Station access form submission
        document.getElementById('stationAccessForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    closeStationAccessModal();
                } else {
                    showNotification(data.message || 'An error occurred', 'error');
                }
            })
            .catch(error => {
                showNotification('Network error occurred', 'error');
            });
        });

        // Dropdown functionality
        document.getElementById('roleDropdown').addEventListener('click', function() {
            document.getElementById('roleDropdownMenu').classList.toggle('hidden');
        });
        
        document.getElementById('statusDropdown').addEventListener('click', function() {
            document.getElementById('statusDropdownMenu').classList.toggle('hidden');
        });
        
        document.getElementById('accessDropdown').addEventListener('click', function() {
            document.getElementById('accessDropdownMenu').classList.toggle('hidden');
        });
        
        document.getElementById('modalRoleDropdown').addEventListener('click', function() {
            document.getElementById('modalRoleDropdownMenu').classList.toggle('hidden');
        });
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#roleDropdown') && !e.target.closest('#roleDropdownMenu')) {
                document.getElementById('roleDropdownMenu').classList.add('hidden');
            }
            if (!e.target.closest('#statusDropdown') && !e.target.closest('#statusDropdownMenu')) {
                document.getElementById('statusDropdownMenu').classList.add('hidden');
            }
            if (!e.target.closest('#accessDropdown') && !e.target.closest('#accessDropdownMenu')) {
                document.getElementById('accessDropdownMenu').classList.add('hidden');
            }
            if (!e.target.closest('#modalRoleDropdown') && !e.target.closest('#modalRoleDropdownMenu')) {
                document.getElementById('modalRoleDropdownMenu').classList.add('hidden');
            }
        });

        // Role Management Functions
        function openRoleManagement() {
            loadRoles();
            const modal = document.getElementById('roleManagementModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        
        function closeRoleManagement() {
            const modal = document.getElementById('roleManagementModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        
        function loadRoles() {
            fetch('/roles')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('rolesContainer');
                    container.innerHTML = '';
                    
                    data.roles.forEach(role => {
                        const roleDiv = document.createElement('div');
                        roleDiv.className = 'flex items-center justify-between p-3 border rounded hover:bg-gray-50';
                        roleDiv.innerHTML = `
                            <div>
                                <div class="text-sm font-medium text-gray-900">${role.display_name}</div>
                                <div class="text-xs text-gray-500">${role.name}</div>
                                ${role.description ? `<div class="text-xs text-gray-400 mt-1">${role.description}</div>` : ''}
                            </div>
                            <div class="flex gap-2">
                                <button type="button" onclick="editRole(${role.id}, '${role.name}', '${role.display_name}', '${role.description || ''}')"
                                    class="text-white font-medium rounded text-xs px-2 py-1 focus:outline-none transition-all duration-300"
                                    style="background: linear-gradient(135deg, #4e73df, #224abe);">
                                    <i class="fas fa-edit"></i>
                                </button>
                                ${!['administrator', 'editor', 'viewer'].includes(role.name) ? `
                                <button type="button" onclick="deleteRole(${role.id}, '${role.display_name}')"
                                    class="text-white font-medium rounded text-xs px-2 py-1 focus:outline-none transition-all duration-300"
                                    style="background: linear-gradient(135deg, #e74a3b, #c41e3a);">
                                    <i class="fas fa-trash"></i>
                                </button>` : ''}
                            </div>
                        `;
                        container.appendChild(roleDiv);
                    });
                })
                .catch(error => {
                    console.error('Error loading roles:', error);
                });
        }
        
        function openAddRoleModal() {
            document.getElementById('roleModalTitle').textContent = 'ADD ROLE';
            document.getElementById('roleForm').action = '/roles';
            document.getElementById('roleMethodField').disabled = true;
            document.getElementById('roleId').value = '';
            document.getElementById('roleSubmitText').textContent = 'Save';
            
            // Reset form
            document.getElementById('roleForm').reset();
            hideRoleErrors();
            
            const modal = document.getElementById('roleModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        
        function editRole(id, name, displayName, description) {
            document.getElementById('roleModalTitle').textContent = 'EDIT ROLE';
            document.getElementById('roleForm').action = `/roles/${id}`;
            document.getElementById('roleMethodField').value = 'PUT';
            document.getElementById('roleMethodField').disabled = false;
            document.getElementById('roleId').value = id;
            document.getElementById('roleName').value = name;
            document.getElementById('roleDisplayName').value = displayName;
            document.getElementById('roleDescription').value = description;
            document.getElementById('roleSubmitText').textContent = 'Update';
            
            hideRoleErrors();
            
            const modal = document.getElementById('roleModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        
        function closeRoleModal() {
            const modal = document.getElementById('roleModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        
        function hideRoleErrors() {
            document.getElementById('roleNameError').classList.add('hidden');
            document.getElementById('roleDisplayNameError').classList.add('hidden');
        }
        
        // Role form submission
        document.getElementById('roleForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const action = this.action || '/roles';
            
            fetch(action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    closeRoleModal();
                    loadRoles();
                    // Reload page to update role dropdowns
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(data.message || 'An error occurred', 'error');
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            const errorDiv = document.getElementById('role' + key.charAt(0).toUpperCase() + key.slice(1) + 'Error');
                            if (errorDiv) {
                                errorDiv.textContent = data.errors[key][0];
                                errorDiv.classList.remove('hidden');
                            }
                        });
                    }
                }
            })
            .catch(error => {
                showNotification('Network error occurred', 'error');
            });
        });
        
        function deleteRole(id, name) {
            document.getElementById('deleteRoleName').textContent = name;
            window.deleteRoleId = id;
            
            const modal = document.getElementById('deleteRoleModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        
        // Confirm delete role
        document.getElementById('confirmDeleteRole').addEventListener('click', function() {
            const roleId = window.deleteRoleId;
            
            fetch(`/roles/${roleId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    closeDeleteRoleModal();
                    loadRoles();
                    // Reload page to update role dropdowns
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(data.message || 'An error occurred', 'error');
                }
            })
            .catch(error => {
                showNotification('Network error occurred', 'error');
            });
        });
        
        // Cancel delete role
        document.getElementById('cancelDeleteRole').addEventListener('click', function() {
            closeDeleteRoleModal();
        });
        
        function closeDeleteRoleModal() {
            const modal = document.getElementById('deleteRoleModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        
        // Close modals when clicking outside
        document.getElementById('roleManagementModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRoleManagement();
            }
        });
        
        document.getElementById('roleModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRoleModal();
            }
        });
    </script>

@endsection