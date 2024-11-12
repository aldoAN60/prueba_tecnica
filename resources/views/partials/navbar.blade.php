<nav class="bg-gradient-to-r from-slate-700 to-slate-600 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center">
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="{{ route('index') }}" class="text-gray-300 hover:bg-slate-600 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">Productos más vendidos</a>
                        <a href="{{ route('orders.filter') }}" class="text-gray-300 hover:bg-slate-600 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">Órdenes con productos</a>
                        <a href="{{ route('orders.record') }}" class="text-gray-300 hover:bg-slate-600 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">Historial de órdenes</a>
                    </div>
                </div>
            </div>
            <div class="-mr-2 flex md:hidden">
                <button id="menuButton" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-700 focus:ring-white">
                    <span class="sr-only">Open main menu</span>
                    <svg id="menuIcon" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg id="closeIcon" class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div id="mobileMenu" class="hidden md:hidden">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
            <a href="{{ route('index') }}" class="text-gray-300 hover:bg-slate-600 hover:text-white block px-3 py-2 rounded-md text-base font-medium transition-colors duration-200">Productos más vendidos</a>
            <a href="{{ route('orders.filter') }}" class="text-gray-300 hover:bg-slate-600 hover:text-white block px-3 py-2 rounded-md text-base font-medium transition-colors duration-200">Órdenes con productos</a>
            <a href="{{ route('orders.record') }}" class="text-gray-300 hover:bg-slate-600 hover:text-white block px-3 py-2 rounded-md text-base font-medium transition-colors duration-200">Historial de órdenes</a>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuButton = document.getElementById('menuButton');
        const menuIcon = document.getElementById('menuIcon');
        const closeIcon = document.getElementById('closeIcon');
        const mobileMenu = document.getElementById('mobileMenu');

        menuButton.addEventListener('click', function () {
            if (mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.remove('hidden');
                menuIcon.classList.add('hidden');
                closeIcon.classList.remove('hidden');
            } else {
                mobileMenu.classList.add('hidden');
                menuIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
            }
        });
    });
</script>
