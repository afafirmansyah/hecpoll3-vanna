@extends('layouts.app')

@section('title', 'HecPoll 3 - Transactions')

@section('content')
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900 flex items-center">
                <i class="fas fa-exchange-alt w-6 h-6 mr-6 text-gray-900"></i>
                Transactions
            </h1>

            <!-- Search Transactions -->
            <div class="relative">
                <form method="GET" action="{{ route('transactions') }}" id="searchForm">
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
            <form method="GET" action="{{ route('transactions') }}" class="flex flex-wrap items-center gap-2">
                <!-- Date Range -->
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-3 h-3 text-gray-900" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <input type="text" id="daterange" name="daterange"
                            value="{{ $fromDate }} - {{ $toDate }}"
                            class="text-gray-900 bg-white border border-gray-300 focus:ring-4 focus:ring-blue-300 font-medium rounded text-xs pl-8 pr-3 py-1.5 block transition-all duration-300" style="width: 12.5rem;"
                            onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(20, 184, 166, 0.1) 100%)'"
                            onmouseout="this.style.background='white'"
                            placeholder="Select date range">
                        <input type="hidden" id="from_date" name="from_date" value="{{ $fromDate }}">
                        <input type="hidden" id="to_date" name="to_date" value="{{ $toDate }}">
                    </div>
                </div>

                <!-- Terminal Dropdown -->
                <div class="relative">
                    <button id="terminalDropdown" data-dropdown-toggle="terminalDropdownMenu" type="button"
                        class="inline-flex items-center justify-center text-gray-900 bg-white border border-gray-300 focus:ring-4 focus:ring-blue-300 font-medium rounded text-xs px-3 py-1.5 transition-all duration-300"
                        onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(20, 184, 166, 0.1) 100%)'"
                        onmouseout="this.style.background='white'">
                        <i class="fas fa-server mr-1"></i>Terminal
                        @if (request('terminals'))
                            <span
                                class="bg-blue-100 text-blue-800 text-xs font-medium px-1.5 py-0.5 rounded-full ml-1">{{ count(request('terminals')) }}</span>
                        @endif
                        <svg class="w-3 h-3 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="terminalDropdownMenu"
                        class="z-50 hidden bg-white border border-gray-300 rounded-lg shadow-lg w-48"
                        style="max-height: 280px; overflow-y: auto;">
                        <ul class="p-1 text-xs text-gray-700">
                            @foreach ($terminals as $terminal)
                                <li>
                                    <div class="inline-flex items-center w-full p-1.5 rounded transition-all duration-300"
                                        onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white'; this.querySelector('label').style.color='white';"
                                        onmouseout="this.style.background=''; this.style.color=''; this.querySelector('label').style.color='';">
                                        <input id="terminal-{{ $terminal->ID_TERMINALS }}" type="checkbox"
                                            name="terminals[]" value="{{ $terminal->ID_TERMINALS }}"
                                            class="w-3 h-3 border border-gray-300 rounded bg-gray-50 focus:ring-2 focus:ring-blue-500"
                                            {{ in_array($terminal->ID_TERMINALS, request('terminals', [])) ? 'checked' : '' }}>
                                        <label for="terminal-{{ $terminal->ID_TERMINALS }}"
                                            class="ms-2 text-xs font-medium text-gray-900">{{ $terminal->Description }}</label>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Customer Dropdown -->
                <div class="relative">
                    <button id="customerDropdown" data-dropdown-toggle="customerDropdownMenu" type="button"
                        class="inline-flex items-center justify-center text-gray-900 bg-white border border-gray-300 focus:ring-4 focus:ring-blue-300 font-medium rounded text-xs px-3 py-1.5 transition-all duration-300"
                        onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(20, 184, 166, 0.1) 100%)'"
                        onmouseout="this.style.background='white'">
                        <i class="fas fa-user mr-1"></i>Customer
                        @if (request('customers'))
                            <span
                                class="bg-green-100 text-green-800 text-xs font-medium px-1.5 py-0.5 rounded-full ml-1">{{ count(request('customers')) }}</span>
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
                                        <input id="customer-{{ $loop->index }}" type="checkbox" name="customers[]"
                                            value="{{ $customer->CustomerName }}"
                                            class="w-3 h-3 border border-gray-300 rounded bg-gray-50 focus:ring-2 focus:ring-blue-500"
                                            {{ in_array($customer->CustomerName, request('customers', [])) ? 'checked' : '' }}>
                                        <label for="customer-{{ $loop->index }}"
                                            class="ms-2 text-xs font-medium text-gray-900">{{ $customer->CustomerName }}</label>
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
                                class="bg-orange-100 text-orange-800 text-xs font-medium px-1.5 py-0.5 rounded-full ml-1">{{ count(request('groups')) }}</span>
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

                <!-- Cost Center Dropdown -->
                <div class="relative">
                    <button id="costCenterDropdown" data-dropdown-toggle="costCenterDropdownMenu" type="button"
                        class="inline-flex items-center justify-center text-gray-900 bg-white border border-gray-300 focus:ring-4 focus:ring-blue-300 font-medium rounded text-xs px-3 py-1.5 transition-all duration-300"
                        onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(20, 184, 166, 0.1) 100%)'"
                        onmouseout="this.style.background='white'">
                        <i class="fas fa-building mr-1"></i>Cost Center
                        @if (request('cost_centers'))
                            <span
                                class="bg-purple-100 text-purple-800 text-xs font-medium px-1.5 py-0.5 rounded-full ml-1">{{ count(request('cost_centers')) }}</span>
                        @endif
                        <svg class="w-3 h-3 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="costCenterDropdownMenu"
                        class="z-50 hidden bg-white border border-gray-300 rounded-lg shadow-lg w-48"
                        style="max-height: 280px; overflow-y: auto;">
                        <ul class="p-1 text-xs text-gray-700">
                            @foreach ($costCenters as $costCenter)
                                <li>
                                    <div class="inline-flex items-center w-full p-1.5 rounded transition-all duration-300"
                                        onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white'; this.querySelector('label').style.color='white';"
                                        onmouseout="this.style.background=''; this.style.color=''; this.querySelector('label').style.color='';">
                                        <input id="costcenter-{{ $costCenter->ID_COSTCENTERS }}" type="checkbox"
                                            name="cost_centers[]" value="{{ $costCenter->ID_COSTCENTERS }}"
                                            class="w-3 h-3 border border-gray-300 rounded bg-gray-50 focus:ring-2 focus:ring-blue-500"
                                            {{ in_array($costCenter->ID_COSTCENTERS, request('cost_centers', [])) ? 'checked' : '' }}>
                                        <label for="costcenter-{{ $costCenter->ID_COSTCENTERS }}"
                                            class="ms-2 text-xs font-medium text-gray-900">{{ $costCenter->Description }}</label>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- License Plate Dropdown -->
                <div class="relative">
                    <button id="licensePlateDropdown" data-dropdown-toggle="licensePlateDropdownMenu" type="button"
                        class="inline-flex items-center justify-center text-gray-900 bg-white border border-gray-300 focus:ring-4 focus:ring-blue-300 font-medium rounded text-xs px-3 py-1.5 transition-all duration-300"
                        onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(20, 184, 166, 0.1) 100%)'"
                        onmouseout="this.style.background='white'">
                        <i class="fas fa-id-card mr-1"></i>License Plate
                        @if(request('license_plates'))
                            <span class="bg-pink-100 text-pink-800 text-xs font-medium px-1.5 py-0.5 rounded-full ml-1">{{ count(request('license_plates')) }}</span>
                        @endif
                        <svg class="w-3 h-3 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="licensePlateDropdownMenu" class="z-50 hidden bg-white border border-gray-300 rounded-lg shadow-lg w-48" style="max-height: 280px; overflow-y: auto;">
                        <ul class="p-1 text-xs text-gray-700">
                            @foreach ($licensePlates as $licensePlate)
                                <li>
                                    <div class="inline-flex items-center w-full p-1.5 rounded transition-all duration-300" onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white'; this.querySelector('label').style.color='white';" onmouseout="this.style.background=''; this.style.color=''; this.querySelector('label').style.color='';">
                                        <input id="licenseplate-{{ $loop->index }}" type="checkbox" name="license_plates[]" value="{{ $licensePlate->LicensePlate }}"
                                            class="w-3 h-3 border border-gray-300 rounded bg-gray-50 focus:ring-2 focus:ring-blue-500"
                                            {{ in_array($licensePlate->LicensePlate, request('license_plates', [])) ? 'checked' : '' }}>
                                        <label for="licenseplate-{{ $loop->index }}" class="ms-2 text-xs font-medium text-gray-900">{{ $licensePlate->LicensePlate }}</label>
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
                <a href="{{ route('transactions') }}"
                    class="text-white font-medium rounded text-xs px-3 py-1.5 focus:outline-none transition-all duration-300 inline-block"
                    style="background: linear-gradient(135deg, #F43F5E 0%, #F97316 100%);"
                    onmouseover="this.style.background='linear-gradient(135deg, rgba(244, 63, 94, 0.8) 0%, rgba(249, 115, 22, 0.8) 100%)';"
                    onmouseout="this.style.background='linear-gradient(135deg, #F43F5E 0%, #F97316 100%)';"
                    onfocus="this.style.boxShadow='0 0 0 2px rgba(244, 63, 94, 0.3)';" onblur="this.style.boxShadow='';">
                    <i class="fas fa-times mr-1"></i>Clear
                </a>
                @if(auth()->user()->hasPermission('export_transactions'))
                <a href="{{ route('transactions.export') }}?{{ http_build_query(request()->query()) }}"
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
                            <th scope="col" class="px-6 py-3 text-right" style="white-space: nowrap;">Trans ID</th>
                            <th scope="col" class="px-6 py-3" style="white-space: nowrap;">Date Time</th>
                            <th scope="col" class="px-6 py-3" style="white-space: nowrap;">Terminal</th>
                            <th scope="col" class="px-6 py-3" style="white-space: nowrap;">Customer</th>
                            <th scope="col" class="px-6 py-3" style="white-space: nowrap;">Card PAN</th>
                            <th scope="col" class="px-6 py-3" style="white-space: nowrap;">License Plate</th>
                            <th scope="col" class="px-6 py-3" style="white-space: nowrap;">Group</th>
                            <th scope="col" class="px-6 py-3" style="white-space: nowrap;">Cost Center</th>
                            <th scope="col" class="px-6 py-3" style="white-space: nowrap;">Additional Entry</th>
                            <th scope="col" class="px-6 py-3 text-right" style="white-space: nowrap;">Mileage</th>
                            <th scope="col" class="px-6 py-3 text-right" style="white-space: nowrap;">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                            <tr class="{{ $loop->odd ? 'bg-gray-50' : 'bg-white' }} border-b dark:bg-gray-800 dark:border-gray-700 transition-all duration-300"
                                onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(20, 184, 166, 0.1) 100%)'"
                                onmouseout="this.style.background='{{ $loop->odd ? '#f9fafb' : '#ffffff' }}';">
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400 text-right"
                                    style="white-space: nowrap;">
                                    {{ $transaction->TransactionsID ?? '-' }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400"
                                    style="white-space: nowrap;">
                                    {{ $transaction->TransDateTime ? \Carbon\Carbon::parse($transaction->TransDateTime)->format('Y-m-d H:i:s') : '-' }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400"
                                    style="white-space: nowrap;">
                                    {{ $transaction->TerminalName ?? '-' }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400"
                                    style="white-space: nowrap;">
                                    {{ $transaction->CustomerName ?? '-' }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400"
                                    style="white-space: nowrap;">
                                    {{ $transaction->CardPAN ?? '-' }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400"
                                    style="white-space: nowrap;">
                                    {{ $transaction->LicensePlate ?? '-' }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400"
                                    style="white-space: nowrap;">
                                    {{ $transaction->Group ?? '-' }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400"
                                    style="white-space: nowrap;">
                                    {{ $transaction->CostCenter ?? '-' }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400"
                                    style="white-space: nowrap;">
                                    {{ $transaction->AdditionalEntry ?? '-' }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400 text-right @if(auth()->user()->hasPermission('update_mileage')) mileage-editable cursor-pointer hover:bg-blue-50 @endif"
                                    style="white-space: nowrap;" data-id="{{ $transaction->TransactionsID }}" data-mileage="{{ $transaction->Mileage ?? 0 }}">
                                    {{ $transaction->Mileage ?? '-' }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400 text-right"
                                    style="white-space: nowrap;">
                                    {{ $transaction->TransQuantity ? number_format($transaction->TransQuantity, 2) : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-white">
                                <td colspan="11" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No
                                    transactions found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $transactions->links('custom-pagination') }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr('#daterange', {
                mode: 'range',
                dateFormat: 'Y-m-d',
                showMonths: 1,
                defaultDate: ['{{ $fromDate }}', '{{ $toDate }}'],
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        const start = flatpickr.formatDate(selectedDates[0], 'Y-m-d');
                        const end = flatpickr.formatDate(selectedDates[1], 'Y-m-d');
                        instance.input.value = start + ' - ' + end;
                        document.getElementById('from_date').value = start;
                        document.getElementById('to_date').value = end;
                    }
                }
            });
        });
        
        // Mileage editing functionality
        document.querySelectorAll('.mileage-editable').forEach(cell => {
            cell.addEventListener('click', function() {
                const currentValue = this.dataset.mileage;
                const transactionId = this.dataset.id;
                
                const input = document.createElement('input');
                input.type = 'number';
                input.value = currentValue;
                input.className = 'w-full px-2 py-1 text-xs border border-blue-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500';
                input.step = '0.01';
                
                this.innerHTML = '';
                this.appendChild(input);
                input.focus();
                input.select();
                
                const saveValue = () => {
                    const newValue = input.value;
                    
                    fetch('/transactions/update-mileage', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            id: transactionId,
                            mileage: newValue
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.dataset.mileage = newValue;
                            this.innerHTML = (newValue || '-') + '<span class="text-green-500 ml-1">*</span>';
                        } else {
                            this.innerHTML = currentValue || '-';
                            alert('Failed to update mileage');
                        }
                    })
                    .catch(() => {
                        this.innerHTML = currentValue || '-';
                        alert('Error updating mileage');
                    });
                };
                
                input.addEventListener('blur', saveValue);
                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        saveValue();
                    }
                });
            });
        });
    </script>

@endsection
