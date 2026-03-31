<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HecPoll | BISM')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="{{ asset('css/flatpickr-custom.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.44.0/dist/apexcharts.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('styles')
</head>

<body class="bg-gray-50 font-inter antialiased">
    @include('components.sidebar')

    <div class="p-4 sm:ml-64 transition-all duration-300">


        <!-- Mobile Header -->
        <div class="sm:hidden bg-white border-b border-gray-200 p-4 mb-4">
            <div class="flex items-center gap-3">
                <button data-drawer-target="sidebar" data-drawer-toggle="sidebar" 
                    class="p-2 text-gray-900 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </button>
                <img src="{{ asset('img/hectronic_black.png') }}" alt="Logo" class="h-16">
            </div>
        </div>

        <main class="mt-0 pb-0">
            @yield('content')
        </main>
    </div>

    @include('components.logout-modal')
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>


        // Apply sidebar state
        function applySidebarState(isMinimized) {
            const sidebar = document.getElementById('sidebar');
            const logo = document.getElementById('logo');
            const spans = sidebar.querySelectorAll('span:not(.sr-only)');
            const dropdownButtons = sidebar.querySelectorAll('.dropdown-btn');
            const regularDropdowns = sidebar.querySelectorAll('ul[id^="dropdown-"]');
            const mainContent = document.querySelector('.p-4');

            if (isMinimized) {
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-16', 'sidebar-minimized');
                logo.style.display = 'none';
                spans.forEach(span => span.style.display = 'none');
                dropdownButtons.forEach(btn => {
                    btn.style.justifyContent = 'center';
                    const span = btn.querySelector('span');
                    const arrow = btn.querySelector('svg:last-child');
                    if (span) span.style.display = 'none';
                    if (arrow) arrow.style.display = 'none';
                });
                regularDropdowns.forEach(dropdown => dropdown.style.display = 'none');
                if (mainContent) {
                    mainContent.classList.remove('sm:ml-64');
                    mainContent.classList.add('ml-16');
                    mainContent.style.marginLeft = '4rem';
                }
            } else {
                sidebar.classList.remove('w-16', 'sidebar-minimized');
                sidebar.classList.add('w-64');
                logo.style.display = 'block';
                spans.forEach(span => span.style.display = 'block');
                dropdownButtons.forEach(btn => {
                    btn.style.justifyContent = '';
                    const span = btn.querySelector('span');
                    const arrow = btn.querySelector('svg:last-child');
                    if (span) span.style.display = 'block';
                    if (arrow) arrow.style.display = 'block';
                });
                regularDropdowns.forEach(dropdown => {
                    dropdown.style.display = '';
                    dropdown.classList.add('hidden');
                });
                if (mainContent) {
                    mainContent.classList.remove('ml-16');
                    mainContent.classList.add('sm:ml-64');
                    mainContent.style.marginLeft = '';
                }
            }
        }

        // Global toggle function accessible from both buttons
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const isCurrentlyMinimized = sidebar.classList.contains('w-16');
            const newState = !isCurrentlyMinimized;

            // Save state to localStorage
            localStorage.setItem('sidebarMinimized', newState.toString());

            // Apply the new state
            applySidebarState(newState);
        }

        // Mobile responsive functions
        function handleMobileResize() {
            const isMobile = window.innerWidth < 640;
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.p-4');
            
            if (isMobile) {
                // Mobile adjustments
                if (mainContent) {
                    mainContent.classList.remove('sm:ml-64', 'ml-16');
                    mainContent.style.marginLeft = '0';
                }
                
                // Auto-hide sidebar on mobile when clicking outside
                document.addEventListener('click', function(e) {
                    if (isMobile && !sidebar.contains(e.target) && !e.target.closest('[data-drawer-toggle]')) {
                        sidebar.classList.add('-translate-x-full');
                    }
                });
            } else {
                // Desktop behavior
                const isMinimized = localStorage.getItem('sidebarMinimized') === 'true';
                applySidebarState(isMinimized);
            }
        }
        
        // Handle window resize
        window.addEventListener('resize', handleMobileResize);
        
        // Initialize mobile handling
        document.addEventListener('DOMContentLoaded', function() {
            handleMobileResize();
            
            // Load sidebar state on page load for desktop
            if (window.innerWidth >= 640) {
                const isMinimized = localStorage.getItem('sidebarMinimized') === 'true';
                if (isMinimized) {
                    applySidebarState(true);
                }
            }
            
            // Add ready class after a short delay to prevent tooltip flash
            setTimeout(() => {
                const sidebar = document.getElementById('sidebar');
                sidebar.classList.add('sidebar-ready');
            }, 100);
        });
        
        // Mobile menu drawer functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuTrigger = document.querySelector('[data-drawer-toggle="mobile-menu"]');
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileMenuClose = document.querySelectorAll('[data-drawer-hide="mobile-menu"]');
            
            if (mobileMenuTrigger && mobileMenu) {
                mobileMenuTrigger.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
                
                mobileMenuClose.forEach(closeBtn => {
                    closeBtn.addEventListener('click', function() {
                        mobileMenu.classList.add('hidden');
                    });
                });
            }
            
            // Tooltip positioning
            document.querySelectorAll('.sidebar-item').forEach(item => {
                const tooltip = item.querySelector('.sidebar-tooltip');
                if (tooltip) {
                    item.addEventListener('mouseenter', function() {
                        const rect = item.getBoundingClientRect();
                        tooltip.style.top = rect.top + rect.height / 2 + 'px';
                    });
                }
            });
            

        });
    </script>
    <style>
        .hover-dropdown {
            display: none;
        }

        .sidebar-minimized .group:hover .hover-dropdown {
            display: block;
        }

        /* Hide search bars on mobile */
        @media (max-width: 640px) {
            .relative form[method="GET"] input[name="search"] {
                display: none !important;
            }
            .relative form[method="GET"] {
                display: none !important;
            }
            
            /* Force table horizontal scroll on mobile */
            .overflow-x-auto {
                overflow-x: scroll !important;
                -webkit-overflow-scrolling: touch;
            }
            
            .overflow-x-auto::-webkit-scrollbar {
                height: 8px;
                background-color: #f1f5f9;
            }
            
            .overflow-x-auto::-webkit-scrollbar-thumb {
                background-color: #cbd5e1;
                border-radius: 4px;
            }
            
            .overflow-x-auto::-webkit-scrollbar-thumb:hover {
                background-color: #94a3b8;
            }
            
            table {
                min-width: 600px !important;
            }
        }
        


        /* Sidebar tooltip styles */
        .sidebar-tooltip {
            position: fixed;
            left: 70px;
            background: linear-gradient(135deg, #3b82f6 0%, #14b8a6 100%);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: none;
            pointer-events: none;
            transform: translateY(-50%);
            display: none;
        }

        .sidebar-tooltip::before {
            content: '';
            position: absolute;
            left: -5px;
            top: 50%;
            transform: translateY(-50%);
            border: 5px solid transparent;
            border-right-color: #3b82f6;
        }

        .w-16.sidebar-ready .sidebar-item:hover .sidebar-tooltip {
            display: block;
            opacity: 1;
            visibility: visible;
            transition: opacity 0.2s, visibility 0.2s;
        }
        
        .sidebar-minimized.sidebar-ready .sidebar-item:hover .sidebar-tooltip {
            display: block;
            opacity: 1;
            visibility: visible;
            transition: opacity 0.2s, visibility 0.2s;
        }
    </style>
    @stack('scripts')
</body>

</html>
