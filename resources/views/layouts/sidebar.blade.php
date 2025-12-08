<div x-data="{ collapsed: false }" :class="collapsed ? 'w-16' : 'w-64'"
    class="flex flex-col bg-gray-800 border-r border-gray-700 transition-all duration-300 ease-in-out">

    <!-- Logo / Toggle -->
    <div class="flex items-center justify-between h-16 shrink-0 bg-gray-900 px-4">
        <a href="{{ route('dashboard') }}" x-show="!collapsed" class="transition-opacity duration-300">
            @if(isset($settings['logo_path']))
                <img src="{{ $settings['logo_path'] }}" class="block h-9 w-auto" alt="Logo">
            @else
                <x-application-logo class="block h-9 w-auto fill-current text-gray-200" />
            @endif
        </a>
        <button @click="collapsed = !collapsed" class="text-gray-400 hover:text-white focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <!-- Navigation Links -->
    <div class="flex flex-col flex-1 overflow-y-auto">
        <nav class="flex-1 px-2 py-4 space-y-2">
            <a href="{{ route('dashboard') }}"
                class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('dashboard') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                <svg class="mr-3 h-6 w-6 flex-shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-gray-300' }}"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span x-show="!collapsed" class="transition-opacity duration-300">{{ __('Dashboard') }}</span>
            </a>

            <a href="{{ route('firewalls.index') }}"
                class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('firewalls.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                <svg class="mr-3 h-6 w-6 flex-shrink-0 {{ request()->routeIs('firewalls.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-300' }}"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <span x-show="!collapsed" class="transition-opacity duration-300">{{ __('Firewalls') }}</span>
            </a>

            @if(Auth::user()->isAdmin())
                <a href="{{ route('companies.index') }}"
                    class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('companies.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="mr-3 h-6 w-6 flex-shrink-0 {{ request()->routeIs('companies.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-300' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span x-show="!collapsed" class="transition-opacity duration-300">{{ __('Companies') }}</span>
                </a>
                <a href="{{ route('users.index') }}"
                    class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('users.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="mr-3 h-6 w-6 flex-shrink-0 {{ request()->routeIs('users.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-300' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span x-show="!collapsed" class="transition-opacity duration-300">{{ __('Users') }}</span>
                </a>
                <a href="{{ route('system.customization.index') }}"
                    class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('system.customization.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="mr-3 h-6 w-6 flex-shrink-0 {{ request()->routeIs('system.customization.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-300' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    <span x-show="!collapsed" class="transition-opacity duration-300">{{ __('Customization') }}</span>
                </a>
            @endif

            <!-- User Dropdown (Moved) -->
            <div class="relative" x-data="{ userOpen: false }">
                <button @click="userOpen = !userOpen"
                    class="group flex w-full items-center px-2 py-2 text-base font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white focus:outline-none">
                    <svg class="mr-3 h-6 w-6 flex-shrink-0 text-gray-400 group-hover:text-gray-300" fill="currentColor"
                        viewBox="0 0 24 24">
                        <path
                            d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span x-show="!collapsed"
                        class="flex-1 text-left transition-opacity duration-300">{{ Auth::user()->name }}</span>
                    <svg x-show="!collapsed" class="ml-auto h-5 w-5 transform transition-transform duration-200"
                        :class="{ 'rotate-180': userOpen }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="userOpen" @click.away="userOpen = false"
                    class="absolute left-0 w-56 mt-1 bg-gray-800 rounded-md shadow-lg border border-gray-700 py-1 z-50"
                    style="display: none;" x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95">
                    <a href="{{ route('profile.edit') }}"
                        class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">
                        {{ __('Profile') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"
                            class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">
                            {{ __('Log Out') }}
                        </a>
                    </form>
                </div>
            </div>
        </nav>

    </div>
</div>