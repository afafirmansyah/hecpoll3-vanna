@extends('layouts.app')

@section('title', 'HecPoll 3 - Dashboards')

@section('content')
    <div class="space-y-3">
        <!-- Date Filter -->
        <div class="p-2 bg-white border-lg border-gray-200 rounded shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <form action="{{ route('dashboard.filter') }}" method="POST"
                class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                @csrf
                <input type="hidden" id="fromdate" name="fromdate" value="{{ $fromDate }}">
                <input type="hidden" id="todate" name="todate" value="{{ $toDate }}">
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-3 h-3 text-gray-900 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
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
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="submit"
                        class="text-white font-medium rounded text-xs px-3 py-1.5 focus:outline-none transition-all duration-300"
                        style="background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);"
                        onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.8) 0%, rgba(20, 184, 166, 0.8) 100%)';"
                        onmouseout="this.style.background='linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%)';"
                        onfocus="this.style.boxShadow='0 0 0 2px rgba(59, 130, 246, 0.3)';" onblur="this.style.boxShadow='';">
                        <i class="fas fa-search mr-1"></i>Apply
                    </button>
                    <a href="/dashboards"
                        class="text-white font-medium rounded text-xs px-3 py-1.5 focus:outline-none transition-all duration-300 inline-block"
                        style="background: linear-gradient(135deg, #F43F5E 0%, #F97316 100%);"
                        onmouseover="this.style.background='linear-gradient(135deg, rgba(244, 63, 94, 0.8) 0%, rgba(249, 115, 22, 0.8) 100%)';"
                        onmouseout="this.style.background='linear-gradient(135deg, #F43F5E 0%, #F97316 100%)';"
                        onfocus="this.style.boxShadow='0 0 0 2px rgba(244, 63, 94, 0.3)';" onblur="this.style.boxShadow='';">
                        <i class="fas fa-times mr-1"></i>Clear
                    </a>
                    @if(auth()->user()->hasPermission('edit_dashboard'))
                    <button type="button" onclick="openTerminalSettings()"
                        class="text-white font-medium rounded text-xs px-3 py-1.5 focus:outline-none transition-all duration-300 sm:hidden"
                        style="background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);"
                        onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.8) 0%, rgba(20, 184, 166, 0.8) 100%)';"
                        onmouseout="this.style.background='linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%)';"
                        onfocus="this.style.boxShadow='0 0 0 2px rgba(59, 130, 246, 0.3)';" onblur="this.style.boxShadow='';">
                        <i class="fas fa-cog mr-1"></i>Terminal Settings
                    </button>
                    @endif
                </div>
                @if(auth()->user()->hasPermission('edit_dashboard'))
                <div class="hidden sm:block ml-auto">
                    <button type="button" onclick="openTerminalSettings()"
                        class="text-white font-medium rounded text-xs px-3 py-1.5 focus:outline-none transition-all duration-300"
                        style="background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);"
                        onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.8) 0%, rgba(20, 184, 166, 0.8) 100%)';"
                        onmouseout="this.style.background='linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%)';"
                        onfocus="this.style.boxShadow='0 0 0 2px rgba(59, 130, 246, 0.3)';" onblur="this.style.boxShadow='';">
                        <i class="fas fa-cog mr-1"></i>Terminal Settings
                    </button>
                </div>
                @endif
            </form>
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
                            document.getElementById('fromdate').value = start;
                            document.getElementById('todate').value = end;
                        }
                    }
                });
            });
        </script>

        <!-- Summary Cards Desktop -->
        <div class="hidden sm:grid grid-cols-5 gap-4">
            <div
                class="p-6 bg-blue-500 border border-gray-200 rounded-lg shadow hover:shadow-lg transition-shadow flex items-center justify-between text-white">
                <div class="flex-1">
                    <p class="text-xs font-medium uppercase tracking-wider mb-1">
                        Total Received
                    </p>
                    <p class="text-2xl font-bold">
                        {{ $totalReceived }} KL
                    </p>
                </div>
                <i class="fas fa-download text-white text-lg"></i>
            </div>


            <div
                class="p-6 bg-green-500 border border-gray-200 rounded-lg shadow hover:shadow-lg transition-shadow flex items-center justify-between text-white">
                <div class="flex-1">
                    <p class="text-xs font-medium uppercase tracking-wider mb-1">
                        Stock Transfer
                    </p>
                    <p class="text-2xl font-bold">
                        {{ $stockTransfer }} KL
                    </p>
                </div>
                <i class="fas fa-exchange-alt text-white text-lg"></i>
            </div>

            <div
                class="p-6 bg-teal-500 border border-gray-200 rounded-lg shadow hover:shadow-lg transition-shadow flex items-center justify-between text-white">
                <div class="flex-1">
                    <p class="text-xs font-medium uppercase tracking-wider mb-1">
                        Issued to BAR - VMS
                    </p>
                    <p class="text-2xl font-bold">
                        {{ $issuedToVms }} KL
                    </p>
                </div>
                <i class="fas fa-truck text-white text-lg"></i>
            </div>

            <div
                class="p-6 bg-rose-500 border border-gray-200 rounded-lg shadow hover:shadow-lg transition-shadow flex items-center justify-between text-white">
                <div class="flex-1">
                    <p class="text-xs font-medium uppercase tracking-wider mb-1">
                        Issued from FT
                    </p>
                    <p class="text-2xl font-bold">
                        {{ $fuelIssued }} KL
                    </p>
                </div>
                <i class="fas fa-gas-pump text-white text-lg"></i>
            </div>

            <div
                class="p-6 bg-orange-500 border border-gray-200 rounded-lg shadow hover:shadow-lg transition-shadow flex items-center justify-between text-white">
                <div class="flex-1">
                    <p class="text-xs font-medium uppercase tracking-wider mb-1">
                        Total Stocks
                    </p>
                    <p class="text-2xl font-bold">
                        {{ $totalStock }} KL
                    </p>
                </div>
                <i class="fas fa-tint text-white text-lg"></i>
            </div>
        </div>

        <!-- Summary Cards Mobile -->
        <div class="grid sm:hidden grid-cols-2 gap-2">
            <div
                class="p-3 bg-blue-500 border border-gray-200 rounded-lg shadow hover:shadow-lg transition-shadow flex items-center justify-between text-white">
                <div class="flex-1">
                    <p class="text-xs font-medium uppercase tracking-wider mb-1">
                        Total Received
                    </p>
                    <p class="text-lg font-bold">
                        {{ $totalReceived }} KL
                    </p>
                </div>
                <i class="fas fa-download text-white text-sm"></i>
            </div>

            <div
                class="p-3 bg-green-500 border border-gray-200 rounded-lg shadow hover:shadow-lg transition-shadow flex items-center justify-between text-white">
                <div class="flex-1">
                    <p class="text-xs font-medium uppercase tracking-wider mb-1">
                        Stock Transfer
                    </p>
                    <p class="text-lg font-bold">
                        {{ $stockTransfer }} KL
                    </p>
                </div>
                <i class="fas fa-exchange-alt text-white text-sm"></i>
            </div>

            <div
                class="p-3 bg-teal-500 border border-gray-200 rounded-lg shadow hover:shadow-lg transition-shadow flex items-center justify-between text-white">
                <div class="flex-1">
                    <p class="text-xs font-medium uppercase tracking-wider mb-1">
                        Issued to BAR - VMS
                    </p>
                    <p class="text-lg font-bold">
                        {{ $issuedToVms }} KL
                    </p>
                </div>
                <i class="fas fa-truck text-white text-sm"></i>
            </div>

            <div
                class="p-3 bg-rose-500 border border-gray-200 rounded-lg shadow hover:shadow-lg transition-shadow flex items-center justify-between text-white">
                <div class="flex-1">
                    <p class="text-xs font-medium uppercase tracking-wider mb-1">
                        Issued from FT
                    </p>
                    <p class="text-lg font-bold">
                        {{ $fuelIssued }} KL
                    </p>
                </div>
                <i class="fas fa-gas-pump text-white text-sm"></i>
            </div>

            <div
                class="col-span-2 p-3 bg-orange-500 border border-gray-200 rounded-lg shadow hover:shadow-lg transition-shadow flex items-center justify-between text-white">
                <div class="flex-1">
                    <p class="text-xs font-medium uppercase tracking-wider mb-1">
                        Total Stocks
                    </p>
                    <p class="text-lg font-bold">
                        {{ $totalStock }} KL
                    </p>
                </div>
                <i class="fas fa-tint text-white text-sm"></i>
            </div>
        </div>

        <!-- Charts Grid Desktop -->
        <div class="hidden sm:grid grid-cols-5 gap-4">
            <!-- Total Issued by Category Chart -->
            <div
                class="col-span-2 p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="mb-4">
                    <h5 class="text-sm font-semibold leading-none text-gray-900 dark:text-white">VEHICLE CATEGORY WISE
                        DISPENSED
                    </h5>
                </div>
                <div id="vehicleGroupChart" class="h-50"></div>
            </div>

            <!-- Fuel Ratio Chart -->
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="mb-4">
                    <h5 class="text-sm font-semibold leading-none text-gray-900 dark:text-white">FUEL RATIO</h5>
                </div>
                <div id="fuelRatioChart" class="h-50"></div>
            </div>

            <!-- Total Issued by Terminal Chart -->
            <div
                class="col-span-2 p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="mb-4">
                    <h5 class="text-sm font-semibold leading-none text-gray-900 dark:text-white">TERMINAL WISE
                        DISPENSED
                    </h5>
                </div>
                <div id="terminalChart" class="h-50"></div>
            </div>
        </div>

        <!-- Charts Grid Mobile -->
        <div class="grid sm:hidden grid-cols-1 gap-2">
            <!-- Vehicle Group Chart Mobile -->
            <div class="p-2 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="mb-4">
                    <h5 class="text-sm font-semibold leading-none text-gray-900 dark:text-white">VEHICLE CATEGORY WISE DISPENSED</h5>
                </div>
                <div id="vehicleGroupChartMobile" class="h-50"></div>
            </div>

            <!-- Fuel Ratio Chart Mobile -->
            <div class="p-2 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="mb-4">
                    <h5 class="text-sm font-semibold leading-none text-gray-900 dark:text-white">FUEL RATIO</h5>
                </div>
                <div id="fuelRatioChartMobile" class="h-50"></div>
            </div>

            <!-- Terminal Chart Mobile -->
            <div class="p-2 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="mb-4">
                    <h5 class="text-sm font-semibold leading-none text-gray-900 dark:text-white">TERMINAL WISE DISPENSED</h5>
                </div>
                <div id="terminalChartMobile" class="h-50"></div>
            </div>

            <!-- Cost Center Chart Mobile -->
            <div class="p-2 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="mb-4">
                    <h5 class="text-sm font-semibold leading-none text-gray-900 dark:text-white">COST CENTER WISE DISPENSED</h5>
                </div>
                <div id="costCenterChartMobile" class="h-50"></div>
            </div>

            <!-- Mileage Ratio Table Mobile -->
            <div class="p-2 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="flow-root">
                    <div class="overflow-x-auto">
                        <div class="inline-block min-w-full py-2 align-middle">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 rounded overflow-hidden">
                                <thead style="background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);">
                                    <tr>
                                        <th scope="col" class="px-2 py-1 text-xs font-medium text-white uppercase tracking-wider text-center">Vehicle</th>
                                        <th scope="col" class="px-2 py-1 text-xs font-medium text-white uppercase tracking-wider text-center">Target</th>
                                        <th scope="col" class="px-2 py-1 text-xs font-medium text-white uppercase tracking-wider text-center">Actual</th>
                                        <th scope="col" class="px-2 py-1 text-xs font-medium text-white uppercase tracking-wider text-center">Ratio</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" id="mileageTableBodyMobile">
                                    @foreach ($mileageRatio as $ratio)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 mileage-row-mobile">
                                            <td class="px-2 py-1 text-xs text-gray-900 text-center dark:text-white">{{ $ratio->VehicleNumber }}</td>
                                            <td class="px-2 py-1 text-xs text-gray-900 text-center dark:text-white">{{ number_format($ratio->Target, 2) }}</td>
                                            <td class="px-2 py-1 text-xs text-gray-900 text-center dark:text-white">{{ number_format($ratio->Actual, 2) }}</td>
                                            <td class="px-2 py-1 text-center">
                                                @if ($ratio->TargetPercent >= 100)
                                                    <span class="inline-flex items-center px-1 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">{{ number_format($ratio->TargetPercent / 100, 2) }}</span>
                                                @else
                                                    <span class="inline-flex items-center px-1 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">{{ number_format($ratio->TargetPercent / 100, 2) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <button id="prevBtnMobile" class="px-2 py-1 text-xs bg-gray-200 text-gray-600 rounded hover:bg-gray-300 disabled:opacity-50" disabled><i class="fas fa-chevron-left"></i></button>
                        <span id="pageInfoMobile" class="text-xs text-gray-600">Page 1</span>
                        <button id="nextBtnMobile" class="px-2 py-1 text-xs bg-gray-200 text-gray-600 rounded hover:bg-gray-300 disabled:opacity-50"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>

            <!-- Top Consumptions Chart Mobile -->
            <div class="p-2 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="mb-4">
                    <h5 class="text-sm font-semibold leading-none text-gray-900 dark:text-white">TOP CONSUMPTIONS WISE DISPENSED</h5>
                </div>
                <div id="topConsumptionsChartMobile" class="h-50"></div>
            </div>

            <!-- Daily Chart Mobile -->
            <div class="p-2 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="mb-4">
                    <h5 class="text-sm font-semibold leading-none text-gray-900 dark:text-white">FUEL MOVEMENT SUMMARY WISE DISPENSED</h5>
                </div>
                <div id="dailyChartMobile" class="h-50"></div>
            </div>
        </div>

        <!-- Cost Center Chart Desktop -->
        <div class="hidden sm:grid grid-cols-3 gap-4">
            <div
                class="col-span-1 p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="mb-4">
                    <h5 class="text-sm font-semibold leading-none text-gray-900 dark:text-white">COST CENTER WISE DISPENSED
                    </h5>
                </div>
                <div id="costCenterChart" class="h-50"></div>
            </div>

            <!-- Mileage Ratio Table -->
            <div
                class="col-span-1 p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                {{-- <div class="flex items-center justify-between mb-4">
                    <h5 class="text-sm font-semibold leading-none text-gray-900 dark:text-white">Vehicle Performance</h5>
                </div> --}}
                <div class="flow-root">
                    <div class="overflow-x-auto">
                        <div class="inline-block min-w-full py-2 align-middle">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 rounded overflow-hidden">
                                <thead style="background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);">
                                    <tr>
                                        <th scope="col"
                                            class="px-3 py-2 text-xs font-medium text-white uppercase tracking-wider text-center">
                                            Vehicle</th>
                                        <th scope="col"
                                            class="px-3 py-2 text-xs font-medium text-white uppercase tracking-wider text-center">
                                            Target</th>
                                        <th scope="col"
                                            class="px-3 py-2 text-xs font-medium text-white uppercase tracking-wider text-center">
                                            Actual</th>
                                        <th scope="col"
                                            class="px-3 py-2 text-xs font-medium text-white uppercase tracking-wider text-center">
                                            Ratio</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700"
                                    id="mileageTableBody">
                                    @foreach ($mileageRatio as $ratio)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 mileage-row">
                                            <td class="px-3 py-2 text-xs text-gray-900 text-center dark:text-white">
                                                {{ $ratio->VehicleNumber }}</td>
                                            <td class="px-3 py-2 text-xs text-gray-900 text-center dark:text-white">
                                                {{ number_format($ratio->Target, 2) }}</td>
                                            <td class="px-3 py-2 text-xs text-gray-900 text-center dark:text-white">
                                                {{ number_format($ratio->Actual, 2) }}</td>
                                            <td class="px-3 py-2 text-center">
                                                @if ($ratio->TargetPercent >= 100)
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                                        {{ number_format($ratio->TargetPercent / 100, 2) }}
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                        {{ number_format($ratio->TargetPercent / 100, 2) }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Pagination -->
                    <div class="flex justify-between items-center mt-3">
                        <button id="prevBtn"
                            class="px-2 py-1 text-xs bg-gray-200 text-gray-600 rounded hover:bg-gray-300 disabled:opacity-50"
                            disabled>
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span id="pageInfo" class="text-xs text-gray-600">Page 1</span>
                        <button id="nextBtn"
                            class="px-2 py-1 text-xs bg-gray-200 text-gray-600 rounded hover:bg-gray-300 disabled:opacity-50">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Top Consumptions Chart -->
            <div
                class="col-span-1 p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="mb-4">
                    <h5 class="text-sm font-semibold leading-none text-gray-900 dark:text-white">TOP CONSUMPTIONS WISE
                        DISPENSED</h5>
                </div>
                <div id="topConsumptionsChart" class="h-50"></div>
            </div>


        </div>

        <!-- Bottom Charts Desktop -->
        <div class="hidden sm:grid lg:grid-cols-1 gap-4">

            <!-- Daily Data Chart -->
            <div
                class="col-span-1 p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h5 class="text-sm font-semibold leading-none text-gray-900 dark:text-white">FUEL MOVEMENT SUMMARY WISE
                        DISPENSED
                    </h5>
                </div>
                <div id="dailyChart" class="h-50"></div>
            </div>


        </div>
    </div>

    <!-- Terminal Settings Modal -->
    @if(auth()->user()->hasPermission('edit_dashboard'))
    <div id="terminalSettingsModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-lg shadow-lg">
                <div class="px-4 py-3 border-b rounded-t-lg" style="background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);">
                    <h3 class="text-sm font-semibold text-white text-center">
                        TERMINAL CONFIGURATION SETTINGS
                    </h3>
                </div>
                <div class="p-4">
                    <form id="terminalSettingsForm">
                        <div class="grid grid-cols-1 gap-4">
                            <!-- Decantation Terminals -->
                            <div class="border rounded p-3">
                                <div class="text-xs font-medium text-gray-700 mb-2">UNLOADING (DECANTATION) TERMINALS</div>
                                <div id="decantationTerminals" class="grid grid-cols-3 gap-2"></div>
                            </div>
                            
                            <!-- Toploading Terminals -->
                            <div class="border rounded p-3">
                                <div class="text-xs font-medium text-gray-700 mb-2">STOCK TRANSFER (TOPLOADING) TERMINALS</div>
                                <div id="toloadingTerminals" class="grid grid-cols-3 gap-2"></div>
                            </div>
                            
                            <!-- Fuel Dispensing Terminals -->
                            <div class="border rounded p-3">
                                <div class="text-xs font-medium text-gray-700 mb-2">FUEL DISPENSING TERMINALS</div>
                                <div id="fuelDispensingTerminals" class="grid grid-cols-3 gap-2"></div>
                            </div>
                        </div>
                        
                        <div class="flex justify-end gap-2 mt-4">
                            <button type="button" onclick="closeTerminalSettings()" 
                                class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-xs font-medium px-3 py-1.5 hover:text-gray-900">
                                <i class="fas fa-times mr-1"></i>Cancel
                            </button>
                            <button type="submit" 
                                class="text-white text-xs px-3 py-1.5 rounded" style="background: linear-gradient(135deg, #4e73df, #224abe);">
                                <i class="fas fa-save mr-1"></i>Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
        <script>
            // Chart data from PHP
            const vehicleGroupData = @json($vehicleGroupData);
            const costCenterData = @json($costCenterData);
            const dailyData = @json($dailyData);
            const vehicleData = @json($vehicleData);
            const terminalData = @json($terminalData);
            const ratioData = @json($ratioData);

            // Format date function
            function formatDateLabel(dateStr) {
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                const parts = dateStr.split('/');
                const day = parts[0];
                const monthIndex = parseInt(parts[1]) - 1;
                return day + ' ' + months[monthIndex];
            }

            // Vehicle Groups Chart
            const vehicleGroupChart = new ApexCharts(document.querySelector("#vehicleGroupChart"), {
                chart: {
                    type: 'bar',
                    height: 250,
                    toolbar: {
                        show: true
                    },
                    background: 'transparent',
                    fontFamily: 'Inter, sans-serif'
                },
                series: [{
                    name: 'Quantity',
                    data: vehicleGroupData.map(item => item.vgptotal)
                }],
                xaxis: {
                    categories: vehicleGroupData.map(item => item.vgp),
                    labels: {
                        style: {
                            colors: '#6B7280',
                            fontSize: '12px',
                            fontWeight: 500
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#6B7280',
                            fontSize: '12px'
                        },
                        formatter: function(val) {
                            return new Intl.NumberFormat('en-US', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }).format(val) + ' KL';
                        }

                    }
                },
                colors: ['#3B82F6', '#22C55E', '#14B8A6', '#F43F5E', '#F97316', '#A855F7'],
                plotOptions: {
                    bar: {
                        columnWidth: '70%',
                        distributed: true,
                        borderRadius: 1,
                        dataLabels: {
                            position: "top"
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    offsetY: -20,
                    style: {
                        colors: ['#374151'],
                        fontSize: '11px',
                        fontWeight: 600
                    },
                    formatter: function(val) {
                        return new Intl.NumberFormat('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }).format(val);
                    }
                },
                grid: {
                    borderColor: '#E5E7EB',
                    strokeDashArray: 4
                },
                legend: {
                    show: false
                },
                tooltip: {
                    enabled: false
                }
            });
            vehicleGroupChart.render();

            // Vehicle Groups Chart Mobile
            const vehicleGroupChartMobile = new ApexCharts(document.querySelector("#vehicleGroupChartMobile"), {
                chart: {
                    type: 'bar',
                    height: 250,
                    toolbar: {
                        show: true
                    },
                    background: 'transparent',
                    fontFamily: 'Inter, sans-serif'
                },
                series: [{
                    name: 'Quantity',
                    data: vehicleGroupData.map(item => item.vgptotal)
                }],
                xaxis: {
                    categories: vehicleGroupData.map(item => item.vgp),
                    labels: {
                        style: {
                            colors: '#6B7280',
                            fontSize: '10px',
                            fontWeight: 500
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#6B7280',
                            fontSize: '10px'
                        },
                        formatter: function(val) {
                            return new Intl.NumberFormat('en-US', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }).format(val) + ' KL';
                        }
                    }
                },
                colors: ['#3B82F6', '#22C55E', '#14B8A6', '#F43F5E', '#F97316', '#A855F7'],
                plotOptions: {
                    bar: {
                        columnWidth: '70%',
                        distributed: true,
                        borderRadius: 1,
                        dataLabels: {
                            position: "top"
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    offsetY: -20,
                    style: {
                        colors: ['#374151'],
                        fontSize: '9px',
                        fontWeight: 600
                    },
                    formatter: function(val) {
                        return new Intl.NumberFormat('en-US', {
                            minimumFractionDigits: 1,
                            maximumFractionDigits: 1
                        }).format(val);
                    }
                },
                grid: {
                    borderColor: '#E5E7EB',
                    strokeDashArray: 4
                },
                legend: {
                    show: false
                },
                tooltip: {
                    enabled: false
                }
            });
            if (document.querySelector("#vehicleGroupChartMobile")) {
                vehicleGroupChartMobile.render();
            }

            // Fuel Ratio Chart Mobile
            const fuelRatioChartMobile = new ApexCharts(document.querySelector("#fuelRatioChartMobile"), {
                chart: { type: 'bar', height: 250, toolbar: { show: true }, background: 'transparent', fontFamily: 'Inter, sans-serif' },
                series: [{ name: 'Ratio', data: ratioData.map(item => item.fuel_ratio) }],
                xaxis: { categories: ratioData.map(item => item.description), labels: { style: { colors: '#6B7280', fontSize: '10px', fontWeight: 500 } } },
                yaxis: { labels: { style: { colors: '#6B7280', fontSize: '10px' }, formatter: function(val) { return val.toFixed(2); } } },
                colors: ['#22C55E', '#3B82F6', '#14B8A6', '#F43F5E', '#F97316', '#A855F7'],
                plotOptions: { bar: { columnWidth: '70%', distributed: true, borderRadius: 1, dataLabels: { position: "top" } } },
                dataLabels: { enabled: true, offsetY: -20, style: { colors: ['#374151'], fontSize: '9px', fontWeight: 600 }, formatter: function(val) { return val.toFixed(2); } },
                grid: { borderColor: '#E5E7EB', strokeDashArray: 4 }, legend: { show: false }, tooltip: { enabled: false }
            });
            if (document.querySelector("#fuelRatioChartMobile")) { fuelRatioChartMobile.render(); }

            // Terminal Chart Mobile
            const terminalChartMobile = new ApexCharts(document.querySelector("#terminalChartMobile"), {
                chart: { type: 'bar', height: 250, toolbar: { show: true }, stacked: true, background: 'transparent', fontFamily: 'Inter, sans-serif' },
                plotOptions: { bar: { columnWidth: '70%', borderRadius: 1, dataLabels: { position: "top" } } },
                series: [{ name: 'RFID', data: terminalData.map(item => item.RFID_Quantity) }, { name: 'Proximity', data: terminalData.map(item => item.Proximity_Quantity) }],
                xaxis: { categories: terminalData.map(item => item.Terminal_name), labels: { style: { colors: '#6B7280', fontSize: '10px', fontWeight: 500 } } },
                yaxis: { labels: { style: { colors: '#6B7280', fontSize: '10px' }, formatter: function(val) { return val.toFixed(0) + ' KL'; } } },
                colors: ['#F43F5E', '#F97316'],
                dataLabels: { enabled: true, offsetY: -20, style: { colors: ['#374151'], fontSize: '9px', fontWeight: 600 }, formatter: function(val) { return val === 0 ? '' : val.toFixed(1); } },
                grid: { borderColor: '#E5E7EB', strokeDashArray: 4 }, legend: { show: true, position: 'top', fontSize: '10px' }, tooltip: { enabled: false }
            });
            if (document.querySelector("#terminalChartMobile")) { terminalChartMobile.render(); }

            // Cost Center Chart Mobile
            const costCenterChartMobile = new ApexCharts(document.querySelector("#costCenterChartMobile"), {
                chart: { type: 'bar', height: 250, toolbar: { show: true }, background: 'transparent', fontFamily: 'Inter, sans-serif' },
                series: [{ name: 'Quantity', data: costCenterData.map(item => ({ x: item.costcenter, y: item.total })) }],
                plotOptions: { bar: { borderRadius: 1, horizontal: true, distributed: true, dataLabels: { position: "top" } } },
                xaxis: { labels: { style: { colors: '#6B7280', fontSize: '10px' }, formatter: function(val) { return val.toFixed(0) + ' KL'; } } },
                yaxis: { labels: { style: { colors: '#6B7280', fontSize: '9px', fontWeight: 500 } } },
                colors: ['#3B82F6', '#22C55E', '#14B8A6', '#F43F5E', '#F97316', '#A855F7'],
                dataLabels: { enabled: true, offsetX: 20, style: { colors: ['#000000'], fontSize: '9px', fontWeight: 600 }, formatter: function(val) { return val.toFixed(1); } },
                grid: { borderColor: '#E5E7EB', strokeDashArray: 4 }, legend: { show: false }, tooltip: { enabled: false }
            });
            if (document.querySelector("#costCenterChartMobile")) { costCenterChartMobile.render(); }

            // Top Consumptions Chart Mobile
            const topConsumptionsChartMobile = new ApexCharts(document.querySelector("#topConsumptionsChartMobile"), {
                chart: { type: 'bar', height: 250, toolbar: { show: true }, background: 'transparent', fontFamily: 'Inter, sans-serif' },
                series: [{ name: 'Consumption', data: vehicleData.map(item => ({ x: item.VehicleLicensePlate, y: parseFloat(item.Total) })) }],
                plotOptions: { bar: { borderRadius: 1, horizontal: true, distributed: true, dataLabels: { position: "top" } } },
                xaxis: { labels: { style: { colors: '#6B7280', fontSize: '10px' }, formatter: function(val) { return val.toFixed(0) + ' KL'; } } },
                yaxis: { labels: { style: { colors: '#6B7280', fontSize: '9px', fontWeight: 500 } } },
                colors: ['#3B82F6', '#22C55E', '#14B8A6', '#F43F5E', '#F97316', '#A855F7'],
                dataLabels: { enabled: true, offsetX: 20, style: { colors: ['#000000'], fontSize: '9px', fontWeight: 600 }, formatter: function(val) { return val.toFixed(1); } },
                grid: { borderColor: '#E5E7EB', strokeDashArray: 4 }, legend: { show: false }, tooltip: { enabled: false }
            });
            if (document.querySelector("#topConsumptionsChartMobile")) { topConsumptionsChartMobile.render(); }

            // Daily Chart Mobile
            const dailyChartMobile = new ApexCharts(document.querySelector("#dailyChartMobile"), {
                chart: { type: 'area', height: 250, toolbar: { show: true }, background: 'transparent', fontFamily: 'Inter, sans-serif' },
                series: [{ name: 'FUEL ISSUED', data: dailyData.map(item => parseFloat(item.FuelIssued)) }, { name: 'UNLOADING', data: dailyData.map(item => parseFloat(item.Decantation)) }, { name: 'STOCK TRANSFER', data: dailyData.map(item => parseFloat(item.Toploading)) }, { name: 'TOTAL STOCKS', data: dailyData.map(item => parseFloat(item.TotalStock)) }],
                xaxis: { categories: dailyData.map(item => formatDateLabel(item.Date)), labels: { style: { colors: '#6B7280', fontSize: '10px' } } },
                yaxis: { labels: { style: { colors: '#6B7280', fontSize: '10px' }, formatter: function(val) { return val.toFixed(0) + ' KL'; } } },
                colors: ['#EF4444', '#3B82F6', '#10B981', '#F97316'], stroke: { width: 2, curve: 'smooth' },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.1, stops: [0, 90, 100] } },
                dataLabels: { enabled: true, style: { colors: ['#374151'], fontSize: '8px', fontWeight: 600 }, formatter: function(val) { return val.toFixed(1); } },
                grid: { borderColor: '#E5E7EB', strokeDashArray: 4 }, legend: { show: true, position: 'top', fontSize: '10px' }, tooltip: { enabled: false }
            });
            if (document.querySelector("#dailyChartMobile")) { dailyChartMobile.render(); }

            // Listen for sidebar toggle to resize charts
            window.addEventListener('resize', function() {
                setTimeout(function() {
                    vehicleGroupChart.resize();
                    dailyChart.resize();
                    topConsumptionsChart.resize();
                    fuelRatioChart.resize();
                    costCenterChart.resize();
                    terminalChart.resize();
                }, 300);
            });
            
            // Listen for sidebar toggle specifically
            const originalToggleSidebar = window.toggleSidebar;
            window.toggleSidebar = function() {
                originalToggleSidebar();
                setTimeout(function() {
                    vehicleGroupChart.resize();
                    dailyChart.resize();
                    topConsumptionsChart.resize();
                    fuelRatioChart.resize();
                    costCenterChart.resize();
                    terminalChart.resize();
                }, 350);
            };

            // Daily Chart
            const dailyChart = new ApexCharts(document.querySelector("#dailyChart"), {
                chart: {
                    type: 'area',
                    height: 250,
                    toolbar: {
                        show: true
                    },
                    background: 'transparent',
                    fontFamily: 'Inter, sans-serif'
                },
                series: [{
                        name: 'FUEL ISSUED',
                        data: dailyData.map(item => parseFloat(item.FuelIssued))
                    },
                    {
                        name: 'UNLOADING',
                        data: dailyData.map(item => parseFloat(item.Decantation))
                    },
                    {
                        name: 'STOCK TRANSFER',
                        data: dailyData.map(item => parseFloat(item.Toploading))
                    },
                    {
                        name: 'TOTAL STOCKS',
                        data: dailyData.map(item => parseFloat(item.TotalStock))
                    }
                ],
                xaxis: {
                    categories: dailyData.map(item => formatDateLabel(item.Date)),
                    labels: {
                        style: {
                            colors: '#6B7280',
                            fontSize: '12px',
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#6B7280',
                            fontSize: '12px'
                        },
                        offsetX: -10,
                        formatter: function(val) {
                            return new Intl.NumberFormat('en-US', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }).format(val) + ' KL';
                        }
                    }
                },
                tooltip: {
                    enabled: false
                },
                colors: ['#EF4444', '#3B82F6', '#10B981', '#F97316'],
                stroke: {
                    width: 2,
                    curve: 'smooth'
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.1,
                        stops: [0, 90, 100]
                    }
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        colors: ['#374151'],
                        fontSize: '10px',
                        fontWeight: 600
                    },
                    formatter: function(val) {
                        return new Intl.NumberFormat('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }).format(val);
                    }
                },
                grid: {
                    borderColor: '#E5E7EB',
                    strokeDashArray: 4
                },
                legend: {
                    show: true,
                    position: 'top',
                    horizontalAlign: 'center',
                    fontSize: '12px',
                    fontWeight: 500,
                    labels: {
                        colors: '#374151'
                    }
                }
            });
            dailyChart.render();

            // Top Consumptions Chart
            const topConsumptionsChart = new ApexCharts(document.querySelector("#topConsumptionsChart"), {
                chart: {
                    type: 'bar',
                    height: 250,
                    toolbar: {
                        show: true
                    },
                    background: 'transparent',
                    fontFamily: 'Inter, sans-serif'
                },
                series: [{
                    name: 'Consumption',
                    data: vehicleData.map(item => ({
                        x: item.VehicleLicensePlate,
                        y: parseFloat(item.Total)
                    }))
                }],
                plotOptions: {
                    bar: {
                        borderRadius: 1,
                        horizontal: true,
                        distributed: true,
                        dataLabels: {
                            position: "top"
                        }
                    }
                },
                xaxis: {
                    labels: {
                        style: {
                            colors: '#6B7280',
                            fontSize: '12px'
                        },
                        formatter: function(val) {
                            return new Intl.NumberFormat('en-US', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }).format(val) + ' KL';
                        }
                    },
                    tickAmount: 3,
                    forceNiceScale: false,
                    decimalsInFloat: 0,
                    axisBorder: {
                        show: true
                    },
                    axisTicks: {
                        show: true
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#6B7280',
                            fontSize: '10px',
                            fontWeight: 500
                        }
                    }
                },
                colors: ['#3B82F6', '#22C55E', '#14B8A6', '#F43F5E', '#F97316', '#A855F7', '#8B5CF6', '#06B6D4',
                    '#84CC16', '#EAB308'
                ],
                dataLabels: {
                    enabled: true,
                    offsetX: 40,
                    style: {
                        colors: ['#000000'],
                        fontSize: '10px',
                        fontWeight: 600
                    },
                    formatter: function(val) {
                        return new Intl.NumberFormat('en-US', {
                            minimumFractionDigits: 1,
                            maximumFractionDigits: 1
                        }).format(val);
                    }
                },
                grid: {
                    borderColor: '#E5E7EB',
                    strokeDashArray: 4
                },
                legend: {
                    show: false
                },
                tooltip: {
                    enabled: false
                }
            });
            topConsumptionsChart.render();

            // Fuel Ratio Chart
            const fuelRatioChart = new ApexCharts(document.querySelector("#fuelRatioChart"), {
                chart: {
                    type: 'bar',
                    height: 250,
                    toolbar: {
                        show: true
                    },
                    background: 'transparent',
                    fontFamily: 'Inter, sans-serif'
                },
                series: [{
                    name: 'Ratio',
                    data: ratioData.map(item => item.fuel_ratio)
                }],
                xaxis: {
                    categories: ratioData.map(item => item.description),
                    labels: {
                        style: {
                            colors: '#6B7280',
                            fontSize: '12px',
                            fontWeight: 500
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#6B7280',
                            fontSize: '12px'
                        },
                        formatter: function(val) {
                            return new Intl.NumberFormat('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }).format(val);
                        }
                    }
                },
                colors: ['#22C55E', '#3B82F6', '#14B8A6', '#F43F5E', '#F97316', '#A855F7'],
                plotOptions: {
                    bar: {
                        columnWidth: '70%',
                        distributed: true,
                        borderRadius: 1,
                        dataLabels: {
                            position: "top"
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    offsetY: -20,
                    style: {
                        colors: ['#374151'],
                        fontSize: '11px',
                        fontWeight: 600
                    },
                    formatter: function(val) {
                        return val.toFixed(2);
                    }
                },
                grid: {
                    borderColor: '#E5E7EB',
                    strokeDashArray: 4
                },
                legend: {
                    show: false
                },
                tooltip: {
                    enabled: false
                }
            });

            fuelRatioChart.render();

            // Cost Center Chart
            const costCenterChart = new ApexCharts(document.querySelector("#costCenterChart"), {
                chart: {
                    type: 'bar',
                    height: 250,
                    toolbar: {
                        show: true
                    },
                    background: 'transparent',
                    fontFamily: 'Inter, sans-serif'
                },
                series: [{
                    name: 'Quantity',
                    data: costCenterData.map(item => ({
                        x: item.costcenter,
                        y: item.total
                    }))
                }],
                plotOptions: {
                    bar: {
                        borderRadius: 1,
                        horizontal: true,
                        distributed: true,
                        dataLabels: {
                            position: "top"
                        }
                    }
                },
                xaxis: {
                    labels: {
                        style: {
                            colors: '#6B7280',
                            fontSize: '12px'
                        },
                        formatter: function(val) {
                            return new Intl.NumberFormat('en-US', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }).format(val) + ' KL';
                        }
                    },
                    tickAmount: 5,
                    forceNiceScale: false,
                    decimalsInFloat: 0,
                    axisBorder: {
                        show: true
                    },
                    axisTicks: {
                        show: true
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#6B7280',
                            fontSize: '10px',
                            fontWeight: 500
                        }
                    }
                },
                colors: ['#3B82F6', '#22C55E', '#14B8A6', '#F43F5E', '#F97316', '#A855F7', '#8B5CF6', '#06B6D4',
                    '#84CC16', '#EAB308'
                ],
                dataLabels: {
                    enabled: true,
                    offsetX: 40,
                    style: {
                        colors: ['#000000'],
                        fontSize: '10px',
                        fontWeight: 600
                    },
                    formatter: function(val) {
                        return new Intl.NumberFormat('en-US', {
                            minimumFractionDigits: 1,
                            maximumFractionDigits: 1
                        }).format(val);
                    }
                },
                grid: {
                    borderColor: '#E5E7EB',
                    strokeDashArray: 4
                },
                legend: {
                    show: false
                },
                tooltip: {
                    enabled: false
                }
            });
            costCenterChart.render();

            // Terminal Chart
            const terminalChart = new ApexCharts(document.querySelector("#terminalChart"), {
                chart: {
                    type: 'bar',
                    height: 250,
                    toolbar: {
                        show: true
                    },
                    stacked: true,
                    background: 'transparent',
                    fontFamily: 'Inter, sans-serif'
                },
                plotOptions: {
                    bar: {
                        columnWidth: '70%',
                        borderRadius: 1,
                        dataLabels: {
                            position: "top"
                        }
                    }
                },
                series: [{
                        name: 'RFID',
                        data: terminalData.map(item => item.RFID_Quantity)
                    },
                    {
                        name: 'Proximity',
                        data: terminalData.map(item => item.Proximity_Quantity)
                    }
                ],
                xaxis: {
                    categories: terminalData.map(item => item.Terminal_name),
                    labels: {
                        style: {
                            colors: '#6B7280',
                            fontSize: '12px',
                            fontWeight: 500
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#6B7280',
                            fontSize: '12px'
                        },
                        formatter: function(val) {
                            return new Intl.NumberFormat('en-US', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }).format(val) + ' KL';
                        }
                    }
                },
                colors: ['#F43F5E', '#F97316'],
                dataLabels: {
                    enabled: true,
                    offsetY: -20,
                    minAngleToShowLabel: 0,
                    style: {
                        colors: ['#374151'],
                        fontSize: '11px',
                        fontWeight: 600
                    },
                    formatter: function(val) {
                        if (val === 0) return '';
                        return new Intl.NumberFormat('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }).format(val);
                    }
                },
                grid: {
                    borderColor: '#E5E7EB',
                    strokeDashArray: 4
                },
                legend: {
                    show: true,
                    position: 'top',
                    horizontalAlign: 'center',
                    fontSize: '12px',
                    fontWeight: 500,
                    labels: {
                        colors: '#374151'
                    }
                },
                tooltip: {
                    enabled: false
                }
            });
            terminalChart.render();

            // Mileage Table Pagination
            const rowsPerPage = 5;
            const rows = document.querySelectorAll('.mileage-row');
            const totalPages = Math.ceil(rows.length / rowsPerPage);
            let currentPage = 1;

            function showPage(page) {
                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;

                rows.forEach((row, index) => {
                    row.style.display = (index >= start && index < end) ? '' : 'none';
                });

                document.getElementById('pageInfo').textContent = `Page ${page} of ${totalPages}`;
                document.getElementById('prevBtn').disabled = page === 1;
                document.getElementById('nextBtn').disabled = page === totalPages;
            }

            document.getElementById('prevBtn').addEventListener('click', () => {
                if (currentPage > 1) {
                    currentPage--;
                    showPage(currentPage);
                }
            });

            document.getElementById('nextBtn').addEventListener('click', () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    showPage(currentPage);
                }
            });

            // Initialize mileage pagination
            showPage(1);

            // Mileage Table Pagination Mobile
            const rowsMobile = document.querySelectorAll('.mileage-row-mobile');
            const totalPagesMobile = Math.ceil(rowsMobile.length / rowsPerPage);
            let currentPageMobile = 1;

            function showPageMobile(page) {
                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;

                rowsMobile.forEach((row, index) => {
                    row.style.display = (index >= start && index < end) ? '' : 'none';
                });

                if (document.getElementById('pageInfoMobile')) {
                    document.getElementById('pageInfoMobile').textContent = `Page ${page} of ${totalPagesMobile}`;
                    document.getElementById('prevBtnMobile').disabled = page === 1;
                    document.getElementById('nextBtnMobile').disabled = page === totalPagesMobile;
                }
            }

            if (document.getElementById('prevBtnMobile')) {
                document.getElementById('prevBtnMobile').addEventListener('click', () => {
                    if (currentPageMobile > 1) {
                        currentPageMobile--;
                        showPageMobile(currentPageMobile);
                    }
                });
            }

            if (document.getElementById('nextBtnMobile')) {
                document.getElementById('nextBtnMobile').addEventListener('click', () => {
                    if (currentPageMobile < totalPagesMobile) {
                        currentPageMobile++;
                        showPageMobile(currentPageMobile);
                    }
                });
            }

            // Initialize mobile mileage pagination
            if (rowsMobile.length > 0) {
                showPageMobile(1);
            }
            
            @if(auth()->user()->hasPermission('edit_dashboard'))
            // Terminal Settings Functions
            window.openTerminalSettings = function() {
                fetch('/dashboard/terminal-settings')
                    .then(response => response.json())
                    .then(data => {
                        populateTerminalSettings(data.terminals, data.configurations);
                        const modal = document.getElementById('terminalSettingsModal');
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                    });
            };
            
            window.closeTerminalSettings = function() {
                const modal = document.getElementById('terminalSettingsModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            };
            
            function populateTerminalSettings(terminals, configurations) {
                const types = ['decantation', 'toploading', 'fuelDispensing'];
                const containers = ['decantationTerminals', 'toloadingTerminals', 'fuelDispensingTerminals'];
                const configKeys = ['decantation', 'toploading', 'fuel_dispensing'];
                
                types.forEach((type, index) => {
                    const container = document.getElementById(containers[index]);
                    container.innerHTML = '';
                    
                    terminals.forEach(terminal => {
                        const isChecked = configurations[configKeys[index]].includes(terminal.ID_TERMINALS);
                        const checkbox = document.createElement('label');
                        checkbox.className = 'flex items-center text-xs hover:bg-gray-50 p-1 rounded cursor-pointer';
                        checkbox.innerHTML = `
                            <input type="checkbox" id="${type}_${terminal.ID_TERMINALS}" 
                                   name="${configKeys[index]}[]" value="${terminal.ID_TERMINALS}" 
                                   ${isChecked ? 'checked' : ''}
                                   class="w-3 h-3 mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-gray-700">${terminal.Description} (ID: ${terminal.ID_TERMINALS})</span>
                        `;
                        container.appendChild(checkbox);
                    });
                });
            }
            
            document.getElementById('terminalSettingsForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const data = {
                    decantation: formData.getAll('decantation[]').map(id => parseInt(id)),
                    toploading: formData.getAll('toploading[]').map(id => parseInt(id)),
                    fuel_dispensing: formData.getAll('fuel_dispensing[]').map(id => parseInt(id))
                };
                
                fetch('/dashboard/terminal-settings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        closeTerminalSettings();
                        location.reload();
                    }
                });
            });
            @endif
        </script>
    @endpush
@endsection
