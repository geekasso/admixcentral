<!-- Mobile Backdrop -->
<div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 z-40 md:hidden" style="display: none;"></div>

<div :class="[collapsed ? 'md:w-16' : 'md:w-64', sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0']"
    class="fixed inset-y-0 left-0 z-50 flex flex-col h-full bg-gray-800 border-r border-gray-700 transition-all duration-300 ease-in-out md:relative shadow-xl md:shadow-none w-64">

    <!-- Logo / Toggle -->
    <div class="flex items-center justify-between h-16 shrink-0 bg-gray-900 px-4">
        <a href="{{ route('dashboard') }}" x-show="!collapsed" class="transition-opacity duration-300 md:block hidden">
            @if(isset($settings['logo_path']))
                <img src="{{ $settings['logo_path'] }}" class="block h-9 w-auto" alt="Logo">
            @else
                <img src="{{ asset('images/logo.png') }}" class="block h-9 w-auto" alt="Logo">
            @endif
        </a>
        <!-- Mobile: Show Logo always in drawer -->
        <a href="{{ route('dashboard') }}" class="md:hidden block">
             @if(isset($settings['logo_path']))
                <img src="{{ $settings['logo_path'] }}" class="block h-8 w-auto" alt="Logo">
            @else
                <img src="{{ asset('images/logo.png') }}" class="block h-8 w-auto" alt="Logo">
            @endif
        </a>

        <!-- Desktop Toggle -->
        <button @click="collapsed = !collapsed" class="hidden md:block text-gray-400 hover:text-white focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <!-- Mobile Close Button -->
        <button @click="sidebarOpen = false" class="md:hidden text-gray-400 hover:text-white focus:outline-none">
             <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
                <span :class="collapsed ? 'md:hidden' : 'block'" class="transition-opacity duration-300">{{ __('Dashboard') }}</span>
            </a>

            <a href="{{ route('firewalls.index') }}"
                class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('firewalls.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                <svg class="mr-3 h-6 w-6 flex-shrink-0 {{ request()->routeIs('firewalls.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-300' }}"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <span :class="collapsed ? 'md:hidden' : 'block'" class="transition-opacity duration-300">{{ __('Firewalls') }}</span>
            </a>

            @if(Auth::user()->isAdmin())
                <a href="{{ route('companies.index') }}"
                    class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('companies.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="mr-3 h-6 w-6 flex-shrink-0 {{ request()->routeIs('companies.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-300' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span :class="collapsed ? 'md:hidden' : 'block'" class="transition-opacity duration-300">{{ __('Companies') }}</span>
                </a>
                <a href="{{ route('users.index') }}"
                    class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('users.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="mr-3 h-6 w-6 flex-shrink-0 {{ request()->routeIs('users.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-300' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span :class="collapsed ? 'md:hidden' : 'block'" class="transition-opacity duration-300">{{ __('Users') }}</span>
                </a>
                <a href="{{ route('system.settings.index') }}"
                    class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('system.settings.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="mr-3 h-6 w-6 flex-shrink-0 {{ request()->routeIs('system.settings.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-300' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    <span :class="collapsed ? 'md:hidden' : 'block'" class="transition-opacity duration-300">{{ __('Settings') }}</span>
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
                    <span :class="collapsed ? 'md:hidden' : 'block'"
                        class="flex-1 text-left transition-opacity duration-300 truncate">{{ Auth::user()->name }}</span>
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
                    class="absolute left-0 bottom-full mb-1 w-56 bg-gray-800 rounded-md shadow-lg border border-gray-700 py-1 z-50"
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

    <div x-data="{ 
            wsStatus: 'checking', 
            systemStatus: { queue: true, database: true },
            backendChecked: false,
            wsChecked: false,
            _loading: true,

            get loading() {
                return this._loading;
            },
            
            get isCritical() {
                return !this.systemStatus.database || !this.systemStatus.queue;
            },

            get isDegraded() {
                // Only degraded if we are confirmed disconnected/unavailable
                return !this.isCritical && (this.wsStatus === 'disconnected' || this.wsStatus === 'unavailable' || this.wsStatus === 'failed');
            },

            get isHealthy() {
                return !this.isCritical && !this.isDegraded && this.wsStatus === 'connected';
            },

            get statusText() {
                if (this.loading || this.wsStatus === 'checking') return 'Checking...';
                
                if (this.isCritical) {
                    if (!this.systemStatus.database) return 'Database Error';
                    return 'Queue Error';
                }
                
                if (this.wsStatus === 'connecting') return 'Connecting...';
                if (this.isHealthy) return 'System Online';
                return 'System Degraded';
            },
            
            tryFinishLoading() {
                // Only stop loading if both checks have reported in at least once
                if (this.backendChecked && this.wsChecked) {
                    // unexpected aesthetic delay to prevent flash if it happens too fast (optional, but requested)
                    // actually user wants skeleton, so immediate is fine if accurate.
                    this._loading = false;
                }
            },

            checkBackend() {
                fetch('{{ route('system.status') }}')
                    .then(res => res.json())
                    .then(data => {
                        this.systemStatus = data;
                        this.backendChecked = true;
                        this.tryFinishLoading();
                    })
                    .catch(() => {
                        this.systemStatus = { queue: false, database: false };
                        this.backendChecked = true;
                        this.tryFinishLoading();
                    });
            },

            init() {
                const intervalSeconds = {{ $settings['status_check_interval'] ?? 30 }};
                const intervalMs = intervalSeconds * 1000;
                
                // 1. Start Backend Check
                this.checkBackend();
                setInterval(() => { this.checkBackend() }, 1000);

                // 2. Real-time Push Update (Instant Feedback)
                window.updateSystemStatus = (status) => {
                    this.wsStatus = status;
                    this.wsChecked = true;
                    this.tryFinishLoading();
                };

                // 2. Start WebSocket Check
                const bindEcho = () => {
                    if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
                        const connection = window.Echo.connector.pusher.connection;
                        let rawState = connection.state;
                        this.wsStatus = (rawState === 'unavailable' || rawState === 'failed') ? 'disconnected' : rawState;
                        
                        // Mark WS as checked if it's in a definitive state or if it's connected
                        this.wsChecked = true; 
                        this.tryFinishLoading();

                        // Aggressive Reconnection Logic:
                        // If we are stuck in 'disconnected' or 'unavailable', force a reconnection attempt.
                        // This overrides Pusher's default backoff strategy to ensure we pick up the server ASAP.
                        if (this.wsStatus === 'disconnected' || this.wsStatus === 'unavailable') {
                            // Only force if we haven't just tried (simple throttle handled by the interval)
                             connection.connect();
                        }
                    }
                };

                // Poll WS state
                setInterval(() => { bindEcho(); }, 1000);
                
                // Initial quick check for WS (give it 500ms to initialize Echo)
                setTimeout(() => { bindEcho(); }, 500);

                // 3. Safety Timeout (max 4s)
                setTimeout(() => { 
                    this.backendChecked = true;
                    this.wsChecked = true;
                    this._loading = false; 
                }, 4000);
            }
         }" class="p-4 border-t border-gray-700 bg-gray-900 shadow-inner group relative">
        
        <!-- Tooltip -->
        <div x-show="!loading && !isHealthy && !collapsed" 
             style="display: none;"
             class="absolute bottom-full left-0 w-full bg-gray-800 p-2 text-[10px] text-gray-400 border-t border-gray-700 space-y-1">
             <div class="flex justify-between">
                 <span>Websocket:</span> 
                 <span class="uppercase" 
                       :class="{
                           'text-green-400': wsStatus === 'connected',
                           'text-gray-200': wsStatus === 'connecting',
                           'text-yellow-400': wsStatus !== 'connected' && wsStatus !== 'connecting'
                       }" 
                       x-text="wsStatus"></span>
             </div>
             <div class="flex justify-between"><span>Database:</span> <span :class="systemStatus.database ? 'text-green-400' : 'text-red-400'" x-text="systemStatus.database ? 'OK' : 'Error'"></span></div>
             <div class="flex justify-between"><span>Queue:</span> <span :class="systemStatus.queue ? 'text-green-400' : 'text-red-400'" x-text="systemStatus.queue ? 'OK' : 'Error'"></span></div>
        </div>

        <div class="flex items-center" :class="collapsed ? 'justify-center' : 'justify-start'">
            
            <!-- Skeleton Loader -->
            <template x-if="loading">
                <div class="flex items-center w-full">
                    <div class="h-3 w-3 bg-gray-600 rounded-full animate-pulse"></div>
                    <div :class="collapsed ? 'md:hidden' : 'block'" class="ml-3 flex flex-col space-y-1 w-28">
                        <div class="h-2 bg-gray-600 rounded animate-pulse w-full"></div>
                    </div>
                </div>
            </template>

            <!-- Actual Content -->
            <template x-if="!loading">
                <div class="flex items-center">
                    <!-- Icon -->
                    <div class="relative flex h-3 w-3">
                        <span x-show="isHealthy"
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span x-show="isDegraded"
                            class="animate-pulse absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                        <span x-show="wsStatus === 'connecting'"
                            class="animate-pulse absolute inline-flex h-full w-full rounded-full bg-gray-200 opacity-75"></span>
                        <span x-show="isCritical"
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>

                        <span class="relative inline-flex rounded-full h-3 w-3 transition-colors duration-300" :class="{
                                'bg-green-500': isHealthy,
                                'bg-red-500': isCritical,
                                'bg-yellow-500': isDegraded,
                                'bg-gray-200': wsStatus === 'connecting'
                            }"></span>
                    </div>

                    <!-- Text -->
                    <div :class="collapsed ? 'md:hidden' : 'block'" class="ml-3 flex flex-col">
                        <span class="text-xs font-medium uppercase tracking-wider transition-colors duration-300"
                            :class="{
                                'text-green-400': isHealthy,
                                'text-red-400': isCritical,
                                'text-yellow-400': isDegraded && wsStatus !== 'connecting',
                                'text-gray-200': wsStatus === 'connecting'
                            }"
                            x-text="statusText"></span>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
