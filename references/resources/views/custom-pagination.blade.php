@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-2 py-1 text-xs font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-4 rounded">
                    Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-1 text-xs font-medium text-gray-700 bg-white border border-gray-300 leading-4 rounded hover:text-white focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150" style="transition: all 0.3s;" onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white';" onmouseout="this.style.background=''; this.style.color='';">
                    Previous
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-1 ml-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 leading-4 rounded hover:text-white focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150" style="transition: all 0.3s;" onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white';" onmouseout="this.style.background=''; this.style.color='';">
                    Next
                </a>
            @else
                <span class="relative inline-flex items-center px-2 py-1 ml-2 text-xs font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-4 rounded">
                    Next
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-xs text-gray-700 leading-4">
                    Showing
                    @if ($paginator->firstItem())
                        <span class="font-medium">{{ $paginator->firstItem() }}</span>
                        to
                        <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    of
                    <span class="font-medium">{{ $paginator->total() }}</span>
                    results
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex shadow-sm rounded-md">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="Previous">
                            <span class="relative inline-flex items-center px-1.5 py-1 text-xs font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-l leading-4" aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-1.5 py-1 text-xs font-medium text-gray-500 bg-white border border-gray-300 rounded-l leading-4 hover:text-white focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-300" aria-label="Previous" onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white';" onmouseout="this.style.background=''; this.style.color='';">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @php
                        $currentPage = $paginator->currentPage();
                        $lastPage = $paginator->lastPage();
                        $start = max(1, $currentPage - 1);
                        $end = min($lastPage, $start + 2);
                        $start = max(1, $end - 2);
                    @endphp

                    @if ($start > 1)
                        <a href="{{ $paginator->url(1) }}" class="relative inline-flex items-center px-2 py-1 -ml-px text-xs font-medium text-gray-700 bg-white border border-gray-300 leading-4 hover:text-white focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-300 min-w-[28px] justify-center" onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white';" onmouseout="this.style.background=''; this.style.color='';">1</a>
                        @if ($start > 2)
                            <span class="relative inline-flex items-center px-2 py-1 -ml-px text-xs font-medium text-gray-700 bg-white border border-gray-300 cursor-default leading-4 min-w-[28px] justify-center">...</span>
                        @endif
                    @endif

                    @for ($page = $start; $page <= $end; $page++)
                        @if ($page == $currentPage)
                            <span aria-current="page">
                                <span class="relative inline-flex items-center px-2 py-1 -ml-px text-xs font-medium text-white border border-gray-300 cursor-default leading-4 min-w-[28px] justify-center" style="background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);">{{ $page }}</span>
                            </span>
                        @else
                            <a href="{{ $paginator->url($page) }}" class="relative inline-flex items-center px-2 py-1 -ml-px text-xs font-medium text-gray-700 bg-white border border-gray-300 leading-4 hover:text-white focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-300 min-w-[28px] justify-center" onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white';" onmouseout="this.style.background=''; this.style.color='';">{{ $page }}</a>
                        @endif
                    @endfor

                    @if ($end < $lastPage)
                        @if ($end < $lastPage - 1)
                            <span class="relative inline-flex items-center px-2 py-1 -ml-px text-xs font-medium text-gray-700 bg-white border border-gray-300 cursor-default leading-4 min-w-[28px] justify-center">...</span>
                        @endif
                        <a href="{{ $paginator->url($lastPage) }}" class="relative inline-flex items-center px-2 py-1 -ml-px text-xs font-medium text-gray-700 bg-white border border-gray-300 leading-4 hover:text-white focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-300 min-w-[28px] justify-center" onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white';" onmouseout="this.style.background=''; this.style.color='';">{{ $lastPage }}</a>
                    @endif

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-1.5 py-1 -ml-px text-xs font-medium text-gray-500 bg-white border border-gray-300 rounded-r leading-4 hover:text-white focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-300" aria-label="Next" onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white';" onmouseout="this.style.background=''; this.style.color='';">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="Next">
                            <span class="relative inline-flex items-center px-1.5 py-1 -ml-px text-xs font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-r leading-4" aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif