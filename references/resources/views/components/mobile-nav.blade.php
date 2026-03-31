<!-- Mobile Bottom Navigation (only visible on mobile) -->
<nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 sm:hidden">
    <div class="grid grid-cols-5 h-16">
        <a href="{{ route('dashboard') }}" 
           class="flex flex-col items-center justify-center text-xs {{ request()->routeIs('dashboard*') ? 'text-blue-600' : 'text-gray-500' }}">
            <svg class="w-5 h-5 mb-1" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
            </svg>
            <span>Dashboard</span>
        </a>
        
        <a href="{{ route('cards') }}" 
           class="flex flex-col items-center justify-center text-xs {{ request()->routeIs('cards*') ? 'text-blue-600' : 'text-gray-500' }}">
            <svg class="w-5 h-5 mb-1" fill="currentColor" viewBox="0 0 20 20">
                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path>
            </svg>
            <span>Cards</span>
        </a>
        
        <a href="{{ route('vehicles') }}" 
           class="flex flex-col items-center justify-center text-xs {{ request()->routeIs('vehicles*') ? 'text-blue-600' : 'text-gray-500' }}">
            <i class="fas fa-truck text-lg mb-1"></i>
            <span>Vehicles</span>
        </a>
        
        <a href="{{ route('transactions') }}" 
           class="flex flex-col items-center justify-center text-xs {{ request()->routeIs('transactions*') ? 'text-blue-600' : 'text-gray-500' }}">
            <i class="fas fa-exchange-alt text-lg mb-1"></i>
            <span>Transactions</span>
        </a>
        
        <button data-drawer-target="mobile-menu" data-drawer-toggle="mobile-menu" 
                class="flex flex-col items-center justify-center text-xs text-gray-500">
            <svg class="w-5 h-5 mb-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
            </svg>
            <span>More</span>
        </button>
    </div>
</nav>

<!-- Mobile Menu Drawer -->
<div id="mobile-menu" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50" data-drawer-hide="mobile-menu"></div>
    <div class="fixed bottom-0 left-0 right-0 bg-white rounded-t-lg max-h-96 overflow-y-auto">
        <div class="p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Menu</h3>
                <button data-drawer-hide="mobile-menu" class="text-gray-500">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
            
            <!-- User Info -->
            <div class="flex items-center p-3 mb-4 rounded-lg text-gray-700">
                <i class="fas fa-user w-5 h-5 mr-3"></i>
                <div>
                    <span class="block text-sm font-medium">{{ auth()->user()->name }}</span>
                    <span class="block text-xs text-gray-500">{{ auth()->user()->email }}</span>
                </div>
            </div>
            
            <div class="space-y-2">
                <a href="{{ route('reconciliations') }}" 
                   class="flex items-center p-3 rounded-lg {{ request()->routeIs('reconciliations*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm1 2a1 1 0 000 2h6a1 1 0 100-2H7zm6 7a1 1 0 011 1v3a1 1 0 11-2 0v-3a1 1 0 011-1zm-3 3a1 1 0 100 2h.01a1 1 0 100-2H10zm-4 1a1 1 0 011-1h.01a1 1 0 110 2H7a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    Reconciliations
                </a>
                
                <a href="{{ route('daily-reports') }}" 
                   class="flex items-center p-3 rounded-lg {{ request()->routeIs('daily-reports*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                    </svg>
                    Daily Reports
                </a>
                
                <a href="{{ route('daily-ratio') }}" 
                   class="flex items-center p-3 rounded-lg {{ request()->routeIs('daily-ratio*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700' }}">
                    <i class="fas fa-chart-bar w-5 h-5 mr-3"></i>
                    Daily Ratio
                </a>
                
                <a href="{{ route('events') }}" 
                   class="flex items-center p-3 rounded-lg {{ request()->routeIs('events*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                    Events
                </a>
                
                <button data-modal-target="logoutModal" data-modal-toggle="logoutModal" data-drawer-hide="mobile-menu"
                        class="flex items-center w-full p-3 rounded-lg text-red-600">
                    <i class="fas fa-sign-out-alt w-5 h-5 mr-3"></i>
                    Logout
                </button>
            </div>
        </div>
    </div>
</div>