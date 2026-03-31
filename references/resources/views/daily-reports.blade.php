@extends('layouts.app')

@section('title', 'HecPoll 3 - Daily Reports')

@section('content')
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900 flex items-center">
                <svg class="w-6 h-6 mr-6 text-gray-900" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                </svg>
                Daily Reports
            </h1>

            <!-- Search Bar -->
            <div class="relative">
                <form method="GET" action="{{ route('daily-reports') }}" id="searchForm">
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
            <form method="GET" action="{{ route('daily-reports') }}" class="flex flex-wrap items-center gap-2">
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
                        <input type="hidden" id="from_date" name="fromDate" value="{{ $fromDate }}">
                        <input type="hidden" id="to_date" name="toDate" value="{{ $toDate }}">
                    </div>
                </div>

                <!-- Vehicle Filter -->
                <div class="relative">
                    <button id="vehicleDropdown" data-dropdown-toggle="vehicleDropdownMenu" type="button"
                        class="inline-flex items-center justify-center text-gray-900 bg-white border border-gray-300 focus:ring-4 focus:ring-blue-300 font-medium rounded text-xs px-3 py-1.5 transition-all duration-300"
                        onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(20, 184, 166, 0.1) 100%)'"
                        onmouseout="this.style.background='white'">
                        <i class="fas fa-truck mr-1"></i>Vehicles
                        @if(request('vehicles'))
                            <span class="bg-pink-100 text-pink-800 text-xs font-medium px-1.5 py-0.5 rounded-full ml-1">{{ count(request('vehicles')) }}</span>
                        @endif
                        <svg class="w-3 h-3 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="vehicleDropdownMenu" class="absolute z-50 hidden bg-white border border-gray-300 rounded-lg shadow-lg w-48" style="max-height: 280px; overflow-y: auto; top: 100%; left: 0;">
                        <ul class="p-1 text-xs text-gray-700">
                            @foreach($allVehicles as $vehicleOption)
                                <li>
                                    <div class="inline-flex items-center w-full p-1.5 rounded transition-all duration-300" onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white'; this.querySelector('label').style.color='white';" onmouseout="this.style.background=''; this.style.color=''; this.querySelector('label').style.color='';">
                                        <input id="vehicle-{{ $loop->index }}" type="checkbox" name="vehicles[]" value="{{ $vehicleOption }}"
                                            class="w-3 h-3 border border-gray-300 rounded bg-gray-50 focus:ring-2 focus:ring-blue-500"
                                            {{ in_array($vehicleOption, request('vehicles', [])) ? 'checked' : '' }}>
                                        <label for="vehicle-{{ $loop->index }}" class="ms-2 text-xs font-medium text-gray-900">{{ $vehicleOption }}</label>
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
                <a href="{{ route('daily-reports') }}"
                    class="text-white font-medium rounded text-xs px-3 py-1.5 focus:outline-none transition-all duration-300 inline-block"
                    style="background: linear-gradient(135deg, #F43F5E 0%, #F97316 100%);"
                    onmouseover="this.style.background='linear-gradient(135deg, rgba(244, 63, 94, 0.8) 0%, rgba(249, 115, 22, 0.8) 100%)';"
                    onmouseout="this.style.background='linear-gradient(135deg, #F43F5E 0%, #F97316 100%)';"
                    onfocus="this.style.boxShadow='0 0 0 2px rgba(244, 63, 94, 0.3)';" onblur="this.style.boxShadow='';">
                    <i class="fas fa-times mr-1"></i>Clear
                </a>
                <a href="{{ route('daily-reports.export') }}?fromDate={{ $fromDate }}&toDate={{ $toDate }}@if(request('vehicles'))@foreach(request('vehicles') as $v)&vehicles[]={{ $v }}@endforeach @endif{{ request('search') ? '&search=' . request('search') : '' }}"
                    class="text-white font-medium rounded text-xs px-3 py-1.5 focus:outline-none transition-all duration-300 inline-block"
                    style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);"
                    onmouseover="this.style.background='linear-gradient(135deg, rgba(16, 185, 129, 0.8) 0%, rgba(5, 150, 105, 0.8) 100%)';"
                    onmouseout="this.style.background='linear-gradient(135deg, #10B981 0%, #059669 100%)';"
                    onfocus="this.style.boxShadow='0 0 0 2px rgba(16, 185, 129, 0.3)';" onblur="this.style.boxShadow='';">
                    <i class="fas fa-file-excel mr-1"></i>Export
                </a>
            </form>
        </div>

        <!-- Heatmap -->
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <div class="overflow-y-auto" style="height: calc(100vh - 320px);">
                <table class="w-full text-xs text-left rtl:text-right text-gray-500 dark:text-gray-400"
                    style="white-space: nowrap;">
                    <thead class="text-xs text-white uppercase dark:bg-gray-700 dark:text-gray-400 sticky top-0 z-10"
                        style="background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);">
                        <tr>
                            <th scope="col" class="px-6 py-3" style="white-space: nowrap;">
                                Vehicle
                            </th>
                            @foreach($dates as $date)
                                <th scope="col" class="px-2 py-3 text-center" style="white-space: nowrap; min-width: 50px;">
                                    {{ \Carbon\Carbon::parse($date)->format('d') }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($heatmapData as $vehicle => $data)
                            <tr class="{{ $loop->odd ? 'bg-gray-50' : 'bg-white' }} border-b dark:bg-gray-800 dark:border-gray-700 transition-all duration-300"
                                onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(20, 184, 166, 0.1) 100%)'"
                                onmouseout="this.style.background='{{ $loop->odd ? '#f9fafb' : '#ffffff' }}';">
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400"
                                    style="white-space: nowrap;">
                                    {{ $vehicle }}
                                </td>
                                @foreach($dates as $date)
                                    @php
                                        $quantity = $data[$date];
                                        $maxQuantity = collect($heatmapData)->flatten()->max();
                                        $intensity = $maxQuantity > 0 ? ($quantity / $maxQuantity) : 0;
                                        $bgColor = $quantity > 0 ? 'rgba(244, 63, 94, ' . ($intensity * 0.8 + 0.1) . ')' : 'transparent';
                                        $textColor = $intensity > 0.5 ? 'white' : 'black';
                                    @endphp
                                    <td class="px-2 py-2 text-center text-xs text-gray-500 dark:text-gray-400" 
                                        style="background-color: {{ $bgColor }}; color: {{ $textColor }}; min-width: 50px;">
                                        {{ $quantity > 0 ? number_format($quantity, 0) : '' }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="mt-6">
            {{ $vehicles->links('custom-pagination') }}
        </div>
    </div>
@endsection

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

        // Dropdown functionality
        document.querySelectorAll('[data-dropdown-toggle]').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const dropdownId = this.getAttribute('data-dropdown-toggle');
                const dropdown = document.getElementById(dropdownId);
                
                // Close all other dropdowns
                document.querySelectorAll('[id$="DropdownMenu"]').forEach(otherDropdown => {
                    if (otherDropdown.id !== dropdownId) {
                        otherDropdown.classList.add('hidden');
                    }
                });
                
                dropdown.classList.toggle('hidden');
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('[data-dropdown-toggle]') && !event.target.closest('[id$="DropdownMenu"]')) {
                document.querySelectorAll('[id$="DropdownMenu"]').forEach(dropdown => {
                    dropdown.classList.add('hidden');
                });
            }
        });
    </script>