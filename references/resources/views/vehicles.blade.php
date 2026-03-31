@extends('layouts.app')

@section('title', 'HecPoll 3 - Vehicles')

@section('content')
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900 flex items-center">
                <i class="fas fa-truck w-6 h-6 mr-6 text-gray-900"></i>
                Vehicles
            </h1>

            <!-- Search Vehicles -->
            <div class="relative">
                <form method="GET" action="{{ route('vehicles') }}" id="searchForm">
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
            <form method="GET" action="{{ route('vehicles') }}" class="flex flex-wrap items-center gap-2">
                <!-- Customer Dropdown -->
                <div class="relative">
                    <button id="customerDropdown" data-dropdown-toggle="customerDropdownMenu" type="button"
                        class="inline-flex items-center justify-center text-gray-900 bg-white border border-gray-300 focus:ring-4 focus:ring-blue-300 font-medium rounded text-xs px-3 py-1.5 transition-all duration-300"
                        onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(20, 184, 166, 0.1) 100%)'"
                        onmouseout="this.style.background='white'">
                        <i class="fas fa-user mr-1"></i>Customer
                        @if (request('customers'))
                            <span
                                class="bg-blue-100 text-blue-800 text-xs font-medium px-1.5 py-0.5 rounded-full ml-1">{{ count(request('customers')) }}</span>
                        @endif
                        <svg class="w-3 h-3 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="customerDropdownMenu"
                        class="z-50 hidden bg-white border border-gray-300 rounded-lg shadow-lg w-48"
                        style="max-height: 280px; overflow-y: auto;">
                        <ul class="p-1 text-xs text-gray-700">
                            @foreach ($customers as $customer)
                                <li>
                                    <div class="inline-flex items-center w-full p-1.5 rounded transition-all duration-300"
                                        onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white'; this.querySelector('label').style.color='white';"
                                        onmouseout="this.style.background=''; this.style.color=''; this.querySelector('label').style.color='';">
                                        <input id="customer-{{ $customer->ID_CUSTOMERS }}" type="checkbox"
                                            name="customers[]" value="{{ $customer->ID_CUSTOMERS }}"
                                            class="w-3 h-3 border border-gray-300 rounded bg-gray-50 focus:ring-2 focus:ring-blue-500"
                                            {{ in_array($customer->ID_CUSTOMERS, request('customers', [])) ? 'checked' : '' }}>
                                        <label for="customer-{{ $customer->ID_CUSTOMERS }}"
                                            class="ms-2 text-xs font-medium text-gray-900">{{ $customer->lastname }}</label>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Make Dropdown -->
                <div class="relative">
                    <button id="makeDropdown" data-dropdown-toggle="makeDropdownMenu" type="button"
                        class="inline-flex items-center justify-center text-gray-900 bg-white border border-gray-300 focus:ring-4 focus:ring-blue-300 font-medium rounded text-xs px-3 py-1.5 transition-all duration-300"
                        onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(20, 184, 166, 0.1) 100%)'"
                        onmouseout="this.style.background='white'">
                        <i class="fas fa-car mr-1"></i>Make
                        @if (request('makes'))
                            <span
                                class="bg-green-100 text-green-800 text-xs font-medium px-1.5 py-0.5 rounded-full ml-1">{{ count(request('makes')) }}</span>
                        @endif
                        <svg class="w-3 h-3 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="makeDropdownMenu" class="z-50 hidden bg-white border border-gray-300 rounded-lg shadow-lg w-48"
                        style="max-height: 280px; overflow-y: auto;">
                        <ul class="p-1 text-xs text-gray-700">
                            @foreach ($makes as $make)
                                <li>
                                    <div class="inline-flex items-center w-full p-1.5 rounded transition-all duration-300"
                                        onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white'; this.querySelector('label').style.color='white';"
                                        onmouseout="this.style.background=''; this.style.color=''; this.querySelector('label').style.color='';">
                                        <input id="make-{{ $loop->index }}" type="checkbox" name="makes[]"
                                            value="{{ $make->Make }}"
                                            class="w-3 h-3 border border-gray-300 rounded bg-gray-50 focus:ring-2 focus:ring-blue-500"
                                            {{ in_array($make->Make, request('makes', [])) ? 'checked' : '' }}>
                                        <label for="make-{{ $loop->index }}"
                                            class="ms-2 text-xs font-medium text-gray-900">{{ $make->Make }}</label>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Type Dropdown -->
                <div class="relative">
                    <button id="typeDropdown" data-dropdown-toggle="typeDropdownMenu" type="button"
                        class="inline-flex items-center justify-center text-gray-900 bg-white border border-gray-300 focus:ring-4 focus:ring-blue-300 font-medium rounded text-xs px-3 py-1.5 transition-all duration-300"
                        onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(20, 184, 166, 0.1) 100%)'"
                        onmouseout="this.style.background='white'">
                        <i class="fas fa-tags mr-1"></i>Type
                        @if (request('types'))
                            <span
                                class="bg-orange-100 text-orange-800 text-xs font-medium px-1.5 py-0.5 rounded-full ml-1">{{ count(request('types')) }}</span>
                        @endif
                        <svg class="w-3 h-3 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="typeDropdownMenu" class="z-50 hidden bg-white border border-gray-300 rounded-lg shadow-lg w-48"
                        style="max-height: 280px; overflow-y: auto;">
                        <ul class="p-1 text-xs text-gray-700">
                            @foreach ($types as $type)
                                <li>
                                    <div class="inline-flex items-center w-full p-1.5 rounded transition-all duration-300"
                                        onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white'; this.querySelector('label').style.color='white';"
                                        onmouseout="this.style.background=''; this.style.color=''; this.querySelector('label').style.color='';">
                                        <input id="type-{{ $loop->index }}" type="checkbox" name="types[]"
                                            value="{{ $type->Type }}"
                                            class="w-3 h-3 border border-gray-300 rounded bg-gray-50 focus:ring-2 focus:ring-blue-500"
                                            {{ in_array($type->Type, request('types', [])) ? 'checked' : '' }}>
                                        <label for="type-{{ $loop->index }}"
                                            class="ms-2 text-xs font-medium text-gray-900">{{ $type->Type }}</label>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Group Dropdown -->
                <div class="relative">
                    <button id="groupDropdown" data-dropdown-toggle="groupDropdownMenu" type="button"
                        class="inline-flex items-center justify-center text-gray-900 bg-white border border-gray-300 focus:ring-4 focus:ring-blue-300 font-medium rounded text-xs px-3 py-1.5 transition-all duration-300"
                        onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(20, 184, 166, 0.1) 100%)'"
                        onmouseout="this.style.background='white'">
                        <i class="fas fa-layer-group mr-1"></i>Group
                        @if (request('groups'))
                            <span
                                class="bg-purple-100 text-purple-800 text-xs font-medium px-1.5 py-0.5 rounded-full ml-1">{{ count(request('groups')) }}</span>
                        @endif
                        <svg class="w-3 h-3 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="groupDropdownMenu"
                        class="z-50 hidden bg-white border border-gray-300 rounded-lg shadow-lg w-48"
                        style="max-height: 280px; overflow-y: auto;">
                        <ul class="p-1 text-xs text-gray-700">
                            @foreach ($vehicleGroups as $group)
                                <li>
                                    <div class="inline-flex items-center w-full p-1.5 rounded transition-all duration-300"
                                        onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white'; this.querySelector('label').style.color='white';"
                                        onmouseout="this.style.background=''; this.style.color=''; this.querySelector('label').style.color='';">
                                        <input id="group-{{ $group->ID_VEHICLEGROUPS }}" type="checkbox" name="groups[]"
                                            value="{{ $group->ID_VEHICLEGROUPS }}"
                                            class="w-3 h-3 border border-gray-300 rounded bg-gray-50 focus:ring-2 focus:ring-blue-500"
                                            {{ in_array($group->ID_VEHICLEGROUPS, request('groups', [])) ? 'checked' : '' }}>
                                        <label for="group-{{ $group->ID_VEHICLEGROUPS }}"
                                            class="ms-2 text-xs font-medium text-gray-900">{{ $group->Description }}</label>
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
                <a href="{{ route('vehicles') }}"
                    class="text-white font-medium rounded text-xs px-3 py-1.5 focus:outline-none transition-all duration-300 inline-block"
                    style="background: linear-gradient(135deg, #F43F5E 0%, #F97316 100%);"
                    onmouseover="this.style.background='linear-gradient(135deg, rgba(244, 63, 94, 0.8) 0%, rgba(249, 115, 22, 0.8) 100%)';"
                    onmouseout="this.style.background='linear-gradient(135deg, #F43F5E 0%, #F97316 100%)';"
                    onfocus="this.style.boxShadow='0 0 0 2px rgba(244, 63, 94, 0.3)';" onblur="this.style.boxShadow='';">
                    <i class="fas fa-times mr-1"></i>Clear
                </a>
                @if(auth()->user()->hasPermission('export_vehicles'))
                <a href="{{ route('vehicles.export') }}?{{ http_build_query(request()->query()) }}"
                    class="text-white font-medium rounded text-xs px-3 py-1.5 focus:outline-none transition-all duration-300 inline-block"
                    style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);"
                    onmouseover="this.style.background='linear-gradient(135deg, rgba(16, 185, 129, 0.8) 0%, rgba(5, 150, 105, 0.8) 100%)';"
                    onmouseout="this.style.background='linear-gradient(135deg, #10B981 0%, #059669 100%)';"
                    onfocus="this.style.boxShadow='0 0 0 2px rgba(16, 185, 129, 0.3)';" onblur="this.style.boxShadow='';">
                    <i class="fas fa-file-excel mr-1"></i>Export
                </a>
                @endif
            </form>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <div class="overflow-y-auto" style="height: calc(100vh - 320px);">
                <table class="w-full text-xs text-left rtl:text-right text-gray-500 dark:text-gray-400"
                    style="white-space: nowrap;">
                    <thead class="text-xs text-white uppercase dark:bg-gray-700 dark:text-gray-400 sticky top-0 z-10"
                        style="background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-right" style="white-space: nowrap;">Vehicle ID</th>
                            <th scope="col" class="px-6 py-3" style="white-space: nowrap;">Customer</th>
                            <th scope="col" class="px-6 py-3" style="white-space: nowrap;">Make</th>
                            <th scope="col" class="px-6 py-3" style="white-space: nowrap;">Type</th>
                            <th scope="col" class="px-6 py-3" style="white-space: nowrap;">License Plate</th>
                            <th scope="col" class="px-6 py-3" style="white-space: nowrap;">Group</th>
                            <th scope="col" class="px-6 py-3 text-right" style="white-space: nowrap;">Target</th>
                            <th scope="col" class="px-6 py-3 text-right" style="white-space: nowrap;">Mileage</th>
                            <th scope="col" class="px-6 py-3" style="white-space: nowrap;">Last Transaction Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehicles as $vehicle)
                            <tr class="{{ $loop->odd ? 'bg-gray-50' : 'bg-white' }} border-b dark:bg-gray-800 dark:border-gray-700 transition-all duration-300"
                                onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(20, 184, 166, 0.1) 100%)'"
                                onmouseout="this.style.background='{{ $loop->odd ? '#f9fafb' : '#ffffff' }}';">
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400 text-right"
                                    style="white-space: nowrap;">
                                    {{ $vehicle->ID_VEHICLES ?? '-' }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400"
                                    style="white-space: nowrap;">
                                    {{ $vehicle->CustomerName ?? '-' }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400"
                                    style="white-space: nowrap;">
                                    {{ $vehicle->Make ?? '-' }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400"
                                    style="white-space: nowrap;">
                                    {{ $vehicle->Type ?? '-' }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400"
                                    style="white-space: nowrap;">
                                    {{ $vehicle->LicensePlate ?? '-' }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400"
                                    style="white-space: nowrap;">
                                    {{ $vehicle->GroupName ?? '-' }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400 text-right"
                                    style="white-space: nowrap;">
                                    {{ $vehicle->Target ?? '-' }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400 text-right"
                                    style="white-space: nowrap;">
                                    {{ $vehicle->Mileage ?? '-' }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400"
                                    style="white-space: nowrap;">
                                    {{ $vehicle->LastTransactionDate ? \Carbon\Carbon::parse($vehicle->LastTransactionDate)->format('Y-m-d H:i:s') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-white">
                                <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No
                                    vehicles found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $vehicles->links('custom-pagination') }}
        </div>
    </div>


@endsection
