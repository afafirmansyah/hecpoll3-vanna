<aside id="sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700">
    <div class="h-full px-3 py-4 overflow-y-auto bg-gray-50 dark:bg-gray-800">
        <!-- Logo -->
        <div class="flex items-center mb-5">
            <button id="sidebarToggle"
                class="flex items-center p-2 text-gray-500 rounded-lg transition-all duration-300 ease-in-out mr-3 sm:cursor-pointer cursor-default"
                onclick="if(window.innerWidth >= 640) toggleSidebar()"
                onmouseover="if(window.innerWidth >= 640) { this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.querySelector('svg').style.color='white'; }"
                onmouseout="if(window.innerWidth >= 640) { this.style.background=''; this.querySelector('svg').style.color=''; }"> <svg class="w-5 h-5"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                        clip-rule="evenodd"></path>
                </svg>
            </button>
            <img id="logo" src="{{ asset('img/hectronic_sidebar.png') }}" alt="Logo"
                class="h-16 transition-all duration-300 ml-0">
        </div>



        <!-- Navigation -->
        <ul class="space-y-2 font-medium">
            @if(auth()->user()->hasPermission('view_dashboard'))
            <li class="sidebar-item relative">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center p-2 text-gray-500 rounded-lg dark:text-white group {{ request()->routeIs('dashboard*') ? 'text-white' : '' }} transition-all duration-300 ease-in-out"
                    style="{{ request()->routeIs('dashboard*') ? 'background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);' : '' }}"
                    @if (!request()->routeIs('dashboard*')) onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white'; this.querySelector('svg').style.color='white';"
                    onmouseout="this.style.background=''; this.style.color=''; this.querySelector('svg').style.color='';"
                    @else
                    onmouseover="this.querySelector('svg').style.color='white';"
                    onmouseout="this.querySelector('svg').style.color='white';" @endif>
                    <svg class="w-5 h-5 {{ request()->routeIs('dashboard*') ? 'text-white' : 'text-gray-500' }} transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                        <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
                    </svg>
                    <span class="ms-3">Dashboards</span>
                </a>
                <div class="sidebar-tooltip">Dashboards</div>
            </li>
            @endif

            @if(auth()->user()->hasPermission('view_cards'))
            <li class="sidebar-item relative">
                <a href="{{ route('cards') }}"
                    class="flex items-center p-2 text-gray-500 rounded-lg dark:text-white group {{ request()->routeIs('cards*') ? 'text-white' : '' }} transition-all duration-300 ease-in-out"
                    style="{{ request()->routeIs('cards*') ? 'background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);' : '' }}"
                    @if (!request()->routeIs('cards*')) onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white'; this.querySelector('svg').style.color='white';"
                    onmouseout="this.style.background=''; this.style.color=''; this.querySelector('svg').style.color='';"
                    @else
                    onmouseover="this.querySelector('svg').style.color='white';"
                    onmouseout="this.querySelector('svg').style.color='white';" @endif>
                    <svg class="w-5 h-5 transition duration-75"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                        <path fill-rule="evenodd"
                            d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="ms-3">Cards</span>
                </a>
                <div class="sidebar-tooltip">Cards</div>
            </li>
            @endif

            @if(auth()->user()->hasPermission('view_vehicles'))
            <li class="sidebar-item relative">
                <a href="{{ route('vehicles') }}"
                    class="flex items-center p-2 text-gray-500 rounded-lg dark:text-white group {{ request()->routeIs('vehicles*') ? 'text-white' : '' }} transition-all duration-300 ease-in-out"
                    style="{{ request()->routeIs('vehicles*') ? 'background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);' : '' }}"
                    @if (!request()->routeIs('vehicles*')) onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white'; this.querySelector('i').style.color='white';"
                    onmouseout="this.style.background=''; this.style.color=''; this.querySelector('i').style.color='';"
                    @else
                    onmouseover="this.querySelector('i').style.color='white';"
                    onmouseout="this.querySelector('i').style.color='white';" @endif>
                    <i class="fas fa-truck w-5 h-5 transition duration-75"></i>
                    <span class="ms-3">Vehicles</span>
                </a>
                <div class="sidebar-tooltip">Vehicles</div>
            </li>
            @endif

            @if(auth()->user()->hasPermission('view_transactions'))
            <li class="sidebar-item relative">
                <a href="{{ route('transactions') }}"
                    class="flex items-center p-2 text-gray-500 rounded-lg dark:text-white group {{ request()->routeIs('transactions*') ? 'text-white' : '' }} transition-all duration-300 ease-in-out"
                    style="{{ request()->routeIs('transactions*') ? 'background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);' : '' }}"
                    @if (!request()->routeIs('transactions*')) onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white'; this.querySelector('i').style.color='white';"
                    onmouseout="this.style.background=''; this.style.color=''; this.querySelector('i').style.color='';"
                    @else
                    onmouseover="this.querySelector('i').style.color='white';"
                    onmouseout="this.querySelector('i').style.color='white';" @endif>
                    <i class="fas fa-exchange-alt w-5 h-5 transition duration-75"></i>
                    <span class="ms-3">Transactions</span>
                </a>
                <div class="sidebar-tooltip">Transactions</div>
            </li>
            @endif

            @if(auth()->user()->hasPermission('view_reconciliations'))
            <li class="sidebar-item relative">
                <a href="{{ route('reconciliations') }}"
                    class="flex items-center p-2 text-gray-500 rounded-lg dark:text-white group {{ request()->routeIs('reconciliations*') ? 'text-white' : '' }} transition-all duration-300 ease-in-out"
                    style="{{ request()->routeIs('reconciliations*') ? 'background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);' : '' }}"
                    @if (!request()->routeIs('reconciliations*')) onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white'; this.querySelector('svg').style.color='white';"
                    onmouseout="this.style.background=''; this.style.color=''; this.querySelector('svg').style.color='';"
                    @else
                    onmouseover="this.querySelector('svg').style.color='white';"
                    onmouseout="this.querySelector('svg').style.color='white';" @endif>
                    <svg class="w-5 h-5 transition duration-75"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm1 2a1 1 0 000 2h6a1 1 0 100-2H7zm6 7a1 1 0 011 1v3a1 1 0 11-2 0v-3a1 1 0 011-1zm-3 3a1 1 0 100 2h.01a1 1 0 100-2H10zm-4 1a1 1 0 011-1h.01a1 1 0 110 2H7a1 1 0 01-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="ms-3">Reconciliations</span>
                </a>
                <div class="sidebar-tooltip">Reconciliations</div>
            </li>
            @endif








            @if(auth()->user()->hasPermission('view_daily_reports'))
            <li class="sidebar-item relative">
                <a href="{{ route('daily-reports') }}"
                    class="flex items-center p-2 text-gray-500 rounded-lg dark:text-white group {{ request()->routeIs('daily-reports*') ? 'text-white' : '' }} transition-all duration-300 ease-in-out"
                    style="{{ request()->routeIs('daily-reports*') ? 'background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);' : '' }}"
                    @if (!request()->routeIs('daily-reports*')) onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white'; this.querySelector('svg').style.color='white';"
                    onmouseout="this.style.background=''; this.style.color=''; this.querySelector('svg').style.color='';"
                    @else
                    onmouseover="this.querySelector('svg').style.color='white';"
                    onmouseout="this.querySelector('svg').style.color='white';" @endif>
                    <svg class="w-5 h-5 transition duration-75"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                    </svg>
                    <span class="ms-3">Daily Reports</span>
                </a>
                <div class="sidebar-tooltip">Daily Reports</div>
            </li>
            @endif

            @if(auth()->user()->hasPermission('view_daily_ratio'))
            <li class="sidebar-item relative">
                <a href="{{ route('daily-ratio') }}"
                    class="flex items-center p-2 text-gray-500 rounded-lg dark:text-white group {{ request()->routeIs('daily-ratio*') ? 'text-white' : '' }} transition-all duration-300 ease-in-out"
                    style="{{ request()->routeIs('daily-ratio*') ? 'background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);' : '' }}"
                    @if (!request()->routeIs('daily-ratio*')) onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white'; this.querySelector('i').style.color='white';"
                    onmouseout="this.style.background=''; this.style.color=''; this.querySelector('i').style.color='';"
                    @else
                    onmouseover="this.querySelector('i').style.color='white';"
                    onmouseout="this.querySelector('i').style.color='white';" @endif>
                    <i class="fas fa-chart-bar w-5 h-5 transition duration-75"></i>
                    <span class="ms-3">Daily Ratio</span>
                </a>
                <div class="sidebar-tooltip">Daily Ratio</div>
            </li>
            @endif

            @if(auth()->user()->hasPermission('view_events'))
            <li class="sidebar-item relative">
                <a href="{{ route('events') }}"
                    class="flex items-center p-2 text-gray-500 rounded-lg dark:text-white group {{ request()->routeIs('events*') ? 'text-white' : '' }} transition-all duration-300 ease-in-out"
                    style="{{ request()->routeIs('events*') ? 'background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);' : '' }}"
                    @if (!request()->routeIs('events*')) onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white'; this.querySelector('svg').style.color='white';"
                    onmouseout="this.style.background=''; this.style.color=''; this.querySelector('svg').style.color='';"
                    @else
                    onmouseover="this.querySelector('svg').style.color='white';"
                    onmouseout="this.querySelector('svg').style.color='white';" @endif>
                    <svg class="w-5 h-5 transition duration-75"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="ms-3">Events</span>
                </a>
                <div class="sidebar-tooltip">Events</div>
            </li>
            @endif

            @if(auth()->user()->hasPermission('manage_users'))
            <li class="sidebar-item relative">
                <a href="{{ route('users.index') }}"
                    class="flex items-center p-2 text-gray-500 rounded-lg dark:text-white group {{ request()->routeIs('users*') ? 'text-white' : '' }} transition-all duration-300 ease-in-out"
                    style="{{ request()->routeIs('users*') ? 'background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);' : '' }}"
                    @if (!request()->routeIs('users*')) onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white'; this.querySelector('i').style.color='white';"
                    onmouseout="this.style.background=''; this.style.color=''; this.querySelector('i').style.color='';"
                    @else
                    onmouseover="this.querySelector('i').style.color='white';"
                    onmouseout="this.querySelector('i').style.color='white';" @endif>
                    <i class="fas fa-users w-5 h-5 transition duration-75"></i>
                    <span class="ms-3">User Management</span>
                </a>
                <div class="sidebar-tooltip">User Management</div>
            </li>
            @endif
        </ul>

        <!-- User Info & Logout -->
        <div class="pt-4 mt-4 space-y-2 border-t border-gray-200 dark:border-gray-700">
            <!-- User Info -->
            <div class="sidebar-item relative">
                <div class="flex items-center p-2 text-gray-500 dark:text-white">
                    <i class="fas fa-user flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400"></i>
                    <div class="ms-3 overflow-hidden">
                        <span class="block text-sm font-medium truncate">{{ auth()->user()->name }}</span>
                        <span class="block text-xs text-gray-400 truncate">{{ auth()->user()->email }}</span>
                    </div>
                </div>
                <div class="sidebar-tooltip">{{ auth()->user()->name }}<br>{{ auth()->user()->email }}</div>
            </div>
            
            <!-- Logout -->
            <div class="sidebar-item relative">
                <button data-modal-target="logoutModal" data-modal-toggle="logoutModal"
                    class="flex items-center w-full p-2 text-base text-gray-500 transition-all duration-300 ease-in-out rounded-lg group dark:text-white"
                    onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.6) 0%, rgba(20, 184, 166, 0.6) 100%)'; this.style.color='white'; this.querySelector('i').style.color='white';"
                    onmouseout="this.style.background=''; this.style.color=''; this.querySelector('i').style.color='';">
                    <i class="fas fa-sign-out-alt flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400"></i>
                    <span class="ms-3">Logout</span>
                </button>
                <div class="sidebar-tooltip">Logout</div>
            </div>
        </div>
    </div>
</aside>
