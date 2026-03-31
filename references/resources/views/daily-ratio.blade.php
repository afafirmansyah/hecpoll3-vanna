@extends('layouts.app')

@section('title', 'HecPoll 3 - Daily Ratio')

@section('content')
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900 flex items-center">
                <i class="fas fa-chart-bar w-6 h-6 mr-6 text-gray-900"></i>
                Daily Ratio
            </h1>

            <!-- Search Daily Ratio -->
            <div class="relative">
                <form method="GET" action="{{ route('daily-ratio') }}" id="searchForm">
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
            <form method="GET" action="{{ route('daily-ratio') }}" class="flex flex-wrap items-center justify-between gap-2">
                <div class="flex flex-wrap items-center gap-2">
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
                            <input type="hidden" id="from_date" name="fromdate" value="{{ $fromDate }}">
                            <input type="hidden" id="to_date" name="todate" value="{{ $toDate }}">
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
                    <a href="{{ route('daily-ratio') }}"
                        class="text-white font-medium rounded text-xs px-3 py-1.5 focus:outline-none transition-all duration-300 inline-block"
                        style="background: linear-gradient(135deg, #F43F5E 0%, #F97316 100%);"
                        onmouseover="this.style.background='linear-gradient(135deg, rgba(244, 63, 94, 0.8) 0%, rgba(249, 115, 22, 0.8) 100%)';"
                        onmouseout="this.style.background='linear-gradient(135deg, #F43F5E 0%, #F97316 100%)';"
                        onfocus="this.style.boxShadow='0 0 0 2px rgba(244, 63, 94, 0.3)';" onblur="this.style.boxShadow='';">
                        <i class="fas fa-times mr-1"></i>Clear
                    </a>
                    @if(auth()->user()->hasPermission('export_daily_ratio'))
                    <a href="{{ route('daily-ratio.export') }}?{{ http_build_query(request()->query()) }}"
                        class="text-white font-medium rounded text-xs px-3 py-1.5 focus:outline-none transition-all duration-300 inline-block"
                        style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);"
                        onmouseover="this.style.background='linear-gradient(135deg, rgba(16, 185, 129, 0.8) 0%, rgba(5, 150, 105, 0.8) 100%)';"
                        onmouseout="this.style.background='linear-gradient(135deg, #10B981 0%, #059669 100%)';"
                        onfocus="this.style.boxShadow='0 0 0 2px rgba(16, 185, 129, 0.3)';" onblur="this.style.boxShadow='';">
                        <i class="fas fa-file-excel mr-1"></i>Export
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('manage_daily_ratio'))
                    <button type="button" id="deleteBtn" class="hidden text-white font-medium rounded text-xs px-3 py-1.5 focus:outline-none transition-all duration-300"
                        style="background: linear-gradient(135deg, #e74a3b, #c41e3a);"
                        onmouseover="this.style.background='linear-gradient(135deg, rgba(231, 74, 59, 0.8), rgba(196, 30, 58, 0.8))';"
                        onmouseout="this.style.background='linear-gradient(135deg, #e74a3b, #c41e3a)';"
                        onfocus="this.style.boxShadow='0 0 0 2px rgba(231, 74, 59, 0.3)';" onblur="this.style.boxShadow='';">
                        <i class="fas fa-trash mr-1"></i>Delete
                    </button>
                    @endif
                </div>
                
                @if(auth()->user()->hasPermission('manage_daily_ratio'))
                <button type="button" data-modal-target="inputModal" data-modal-toggle="inputModal"
                    class="text-white font-medium rounded text-xs px-3 py-1.5 focus:outline-none transition-all duration-300"
                    style="background: linear-gradient(135deg, #4e73df, #224abe);"
                    onmouseover="this.style.background='linear-gradient(135deg, rgba(78, 115, 223, 0.8), rgba(34, 74, 190, 0.8))';"
                    onmouseout="this.style.background='linear-gradient(135deg, #4e73df, #224abe)';"
                    onfocus="this.style.boxShadow='0 0 0 2px rgba(78, 115, 223, 0.3)';" onblur="this.style.boxShadow='';">
                    <i class="fas fa-plus mr-1"></i>Input
                </button>
                @endif
            </form>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <div class="overflow-y-auto" style="height: calc(100vh - 320px);">
                <table class="w-full text-xs text-right rtl:text-right text-gray-500 dark:text-gray-400"
                    style="white-space: nowrap;">
                    <thead class="text-xs text-white uppercase dark:bg-gray-700 dark:text-gray-400 sticky top-0 z-10"
                        style="background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);">
                        <tr>
                            @if(auth()->user()->hasPermission('manage_daily_ratio'))
                            <th scope="col" class="px-3 py-2 text-center" rowspan="2" style="white-space: nowrap; vertical-align: middle;">
                            </th>
                            @endif
                            <th scope="col" class="px-6 py-2 text-left border-r border-white" rowspan="2" style="white-space: nowrap; vertical-align: middle;">Transaction Date</th>
                            <th scope="col" class="px-6 py-2 text-center border-r border-white" colspan="3" style="white-space: nowrap;">Over Burden</th>
                            <th scope="col" class="px-6 py-2 text-center border-r border-white" colspan="3" style="white-space: nowrap;">COAL</th>
                            <th scope="col" class="px-6 py-2 text-center" colspan="3" style="white-space: nowrap;">PORT</th>
                        </tr>
                        <tr>
                            <th scope="col" class="px-6 py-2 text-right" style="white-space: nowrap;">Issued</th>
                            <th scope="col" class="px-6 py-2 text-right" style="white-space: nowrap;">Input</th>
                            <th scope="col" class="px-6 py-2 text-right border-r border-white" style="white-space: nowrap;">Ratio</th>
                            <th scope="col" class="px-6 py-2 text-right" style="white-space: nowrap;">Issued</th>
                            <th scope="col" class="px-6 py-2 text-right" style="white-space: nowrap;">Input</th>
                            <th scope="col" class="px-6 py-2 text-right border-r border-white" style="white-space: nowrap;">Ratio</th>
                            <th scope="col" class="px-6 py-2 text-right" style="white-space: nowrap;">Issued</th>
                            <th scope="col" class="px-6 py-2 text-right" style="white-space: nowrap;">Input</th>
                            <th scope="col" class="px-6 py-2 text-right" style="white-space: nowrap;">Ratio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dailyRatios as $data)
                            <tr class="{{ $loop->odd ? 'bg-gray-50' : 'bg-white' }} border-b dark:bg-gray-800 dark:border-gray-700 transition-all duration-300"
                                onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(20, 184, 166, 0.1) 100%)'"
                                onmouseout="this.style.background='{{ $loop->odd ? '#f9fafb' : '#ffffff' }}';">
                                @if(auth()->user()->hasPermission('manage_daily_ratio'))
                                <td class="px-3 py-2 text-center" style="white-space: nowrap;">
                                    <input type="checkbox" class="row-checkbox w-3 h-3" value="{{ $data->date }}">
                                </td>
                                @endif
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400 text-left"
                                    style="white-space: nowrap;">
                                    {{ $data->date ? \Carbon\Carbon::parse($data->date)->format('F j, Y') : '-' }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400"
                                    style="white-space: nowrap;">
                                    {{ number_format($data->ob_fuel ?? 0, 0) }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400"
                                    style="white-space: nowrap;">
                                    {{ number_format($data->ob_prod ?? 0, 0) }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400"
                                    style="white-space: nowrap;">
                                    {{ number_format($data->ob_ratio ?? 0, 2) }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400"
                                    style="white-space: nowrap;">
                                    {{ number_format($data->coal_fuel ?? 0, 0) }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400"
                                    style="white-space: nowrap;">
                                    {{ number_format($data->coal_prod ?? 0, 0) }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400"
                                    style="white-space: nowrap;">
                                    {{ number_format($data->coal_ratio ?? 0, 2) }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400"
                                    style="white-space: nowrap;">
                                    {{ number_format($data->port_fuel ?? 0, 0) }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400"
                                    style="white-space: nowrap;">
                                    {{ number_format($data->port_prod ?? 0, 0) }}
                                </td>
                                <td class="px-6 py-2 text-xs text-gray-500 dark:text-gray-400"
                                    style="white-space: nowrap;">
                                    {{ number_format($data->port_ratio ?? 0, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-white">
                                <td colspan="{{ auth()->user()->hasPermission('manage_daily_ratio') ? '11' : '10' }}" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No daily ratio data found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $dailyRatios->links('custom-pagination') }}
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

    <!-- Input Modal -->
    <div id="inputModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-xs max-h-full" style="margin-left: 60%;">
            <div class="relative bg-white rounded-lg shadow-lg">
                <div class="px-4 py-3 border-b rounded-t-lg" style="background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);">
                    <h3 class="text-sm font-semibold text-white text-center">
                        INPUT DAILY RATIO
                    </h3>
                </div>
                <div class="p-4">
                    <form id="dailyRatioForm" action="{{ route('daily-ratio.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="date" class="block mb-1 text-xs font-medium text-gray-700">TRANSACTION DATE</label>
                                <input type="date" name="date" id="date" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div>
                                <label for="ob_bism" class="block mb-1 text-xs font-medium text-gray-700">OVER BURDEN</label>
                                <input type="number" name="OB_BISM" id="ob_bism" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500" step="1" required>
                            </div>
                            <div>
                                <label for="coal_bism" class="block mb-1 text-xs font-medium text-gray-700">COAL</label>
                                <input type="number" name="COAL_BISM" id="coal_bism" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500" step="1" required>
                            </div>
                            <div>
                                <label for="port_bism" class="block mb-1 text-xs font-medium text-gray-700">PORT</label>
                                <input type="number" name="PORT_BISM" id="port_bism" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500" step="1" required>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 mt-4">
                            <button type="button" data-modal-hide="inputModal" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-xs font-medium px-3 py-1.5 hover:text-gray-900">
                                <i class="fas fa-times mr-1"></i>Cancel
                            </button>
                            <button type="submit" class="text-white text-xs px-3 py-1.5 rounded" style="background: linear-gradient(135deg, #4e73df, #224abe);">
                                <i class="fas fa-save mr-1"></i>Save
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
                        Are you sure you want to delete <span id="deleteCount" class="font-semibold">0</span> selected record(s)?
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
            const dates = Array.from(checkedBoxes).map(cb => cb.value);
            document.getElementById('deleteCount').textContent = dates.length;
            
            // Store dates for deletion
            window.selectedDates = dates;
            
            // Show delete modal
            const modal = document.getElementById('deleteModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
        
        // Confirm delete
        document.getElementById('confirmDelete').addEventListener('click', function() {
            const dates = window.selectedDates;
            
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("daily-ratio.delete") }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            dates.forEach(date => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'dates[]';
                input.value = date;
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
        
        // Close input modal function
        function closeInputModal() {
            const modal = document.getElementById('inputModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        
        // Close modal when clicking outside
        document.getElementById('inputModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeInputModal();
            }
        });
        
        // Auto-hide alerts after 3 seconds
        setTimeout(function() {
            const successAlert = document.getElementById('successAlert');
            const errorAlert = document.getElementById('errorAlert');
            if (successAlert) successAlert.style.display = 'none';
            if (errorAlert) errorAlert.style.display = 'none';
        }, 3000);
    </script>

@endsection