<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('System Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <form method="POST" action="{{ route('system.settings.update') }}" enctype="multipart/form-data" 
                  x-data="{ 
                      sidebarBg: '{{ $settings['sidebar_bg'] ?? '#1f2937' }}', 
                      sidebarText: '{{ $settings['sidebar_text'] ?? '#d1d5db' }}',
                      recommendedText() {
                        const r = parseInt(this.sidebarBg.substr(1,2),16);
                        const g = parseInt(this.sidebarBg.substr(3,2),16);
                        const b = parseInt(this.sidebarBg.substr(5,2),16);
                        const yiq = ((r*299)+(g*587)+(b*114))/1000;
                        return (yiq >= 128) ? '#1f2937' : '#ffffff';
                      }
                  }">
                @csrf
                @method('POST')

                <!-- Section 1: Branding & Identity -->
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden mb-6">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Branding & Identity</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">Customize the look and feel of your application.</p>
                    </div>
                    <div class="px-4 py-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Logo Upload -->
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Custom Logo (Top Left)</label>
                            <div class="flex items-center gap-4 p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                @if(isset($settings['logo_path']))
                                    <div class="shrink-0 relative group">
                                        <img src="{{ $settings['logo_path'] }}" alt="Logo" class="h-12 w-auto object-contain p-1 rounded bg-gray-200 dark:bg-gray-700" :style="'background-color: ' + sidebarBg">
                                        <button type="button" onclick="restoreDefault('logo')" class="absolute -top-2 -right-2 bg-red-100 text-red-600 rounded-full p-1 shadow-sm opacity-0 group-hover:opacity-100 transition-opacity" title="Restore Default">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                @else
                                    <div class="h-12 w-12 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center text-gray-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                                <div class="flex-1" x-data="{ fileName: null }">
                                    <input type="file" name="logo" id="logo" class="hidden" x-ref="logoInput" accept="image/*"
                                           @change="fileName = $refs.logoInput.files[0] ? $refs.logoInput.files[0].name : null">
                                    <button type="button" @click="$refs.logoInput.click()" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Change Logo
                                    </button>
                                    <p x-show="fileName" x-text="fileName" class="mt-1 text-xs text-indigo-600 dark:text-indigo-400 truncate"></p>
                                    <p x-show="!fileName" class="mt-1 text-xs text-gray-500">PNG, JPG, SVG up to 2MB</p>
                                </div>
                            </div>
                        </div>

                        <!-- Favicon Upload -->
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Custom Favicon</label>
                            <div class="flex items-center gap-4 p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                @if(isset($settings['favicon_path']))
                                    <div class="shrink-0 relative group">
                                        <img src="{{ $settings['favicon_path'] }}" alt="Favicon" class="h-10 w-10 object-contain p-1 rounded bg-white border border-gray-200">
                                        <button type="button" onclick="restoreDefault('favicon')" class="absolute -top-2 -right-2 bg-red-100 text-red-600 rounded-full p-1 shadow-sm opacity-0 group-hover:opacity-100 transition-opacity" title="Restore Default">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                @else
                                    <div class="h-10 w-10 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center text-gray-400">
                                       <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                    </div>
                                @endif
                                <div class="flex-1" x-data="{ fileName: null }">
                                    <input type="file" name="favicon" id="favicon" class="hidden" x-ref="favInput" accept=".ico,.png"
                                           @change="fileName = $refs.favInput.files[0] ? $refs.favInput.files[0].name : null">
                                    <button type="button" @click="$refs.favInput.click()" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Change Favicon
                                    </button>
                                     <p x-show="fileName" x-text="fileName" class="mt-1 text-xs text-indigo-600 dark:text-indigo-400 truncate"></p>
                                    <p x-show="!fileName" class="mt-1 text-xs text-gray-500">ICO, PNG up to 1MB</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Section 2: Interface Customization -->
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden mb-6">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Interface Customization</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">Manage themes and white-labeling colors.</p>
                    </div>
                    <div class="px-4 py-5 sm:p-6 space-y-8">
                        
                        <!-- Theme Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">System Theme</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-md">
                                <!-- Light Mode -->
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="theme" value="light" class="peer sr-only" {{ ($settings['theme'] ?? 'light') === 'light' ? 'checked' : '' }}>
                                    <div class="p-4 rounded-lg border-2 border-gray-200 dark:border-gray-700 bg-white hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 transition-all peer-checked:[&_.radio-circle]:bg-indigo-600 peer-checked:[&_.radio-circle]:border-indigo-600 peer-checked:[&_.radio-dot]:opacity-100">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div class="p-2 rounded-full bg-gray-100 text-yellow-500">
                                                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                                </div>
                                                <span class="font-medium text-gray-900">Light Mode</span>
                                            </div>
                                            <!-- Radio Circle -->
                                            <div class="radio-circle w-5 h-5 rounded-full border border-gray-300 bg-white flex items-center justify-center transition-all">
                                                <div class="radio-dot w-2 h-2 rounded-full bg-white opacity-0 transition-opacity"></div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                                <!-- Dark Mode -->
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="theme" value="dark" class="peer sr-only" {{ ($settings['theme'] ?? 'light') === 'dark' ? 'checked' : '' }}>
                                    <div class="p-4 rounded-lg border-2 border-gray-200 dark:border-gray-700 bg-gray-800 hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-gray-800 transition-all peer-checked:[&_.radio-circle]:bg-indigo-600 peer-checked:[&_.radio-circle]:border-indigo-600 peer-checked:[&_.radio-dot]:opacity-100">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div class="p-2 rounded-full bg-gray-700 text-blue-300">
                                                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                                                </div>
                                                <span class="font-medium text-white">Dark Mode</span>
                                            </div>
                                            <!-- Radio Circle -->
                                            <div class="radio-circle w-5 h-5 rounded-full border border-gray-300 bg-white flex items-center justify-center transition-all">
                                                 <div class="radio-dot w-2 h-2 rounded-full bg-white opacity-0 transition-opacity"></div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Sidebar Customization -->
                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                             <h4 class="text-base font-medium text-gray-900 dark:text-gray-100 mb-4">Sidebar Appearance (White Labeling)</h4>
                             
                             <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                                 <div class="space-y-4">
                                     <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Background Color</label>
                                     <div class="flex items-center gap-3">
                                         <input type="color" x-model="sidebarBg" name="sidebar_bg" class="h-10 w-16 p-0 border-0 rounded cursor-pointer shadow-sm shrink-0">
                                         <input type="text" x-model="sidebarBg" class="uppercase flex-1 rounded-md border-gray-300 shadow-sm sm:text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                     </div>
                                 </div>
                                 <div class="space-y-4">
                                    <label for="sidebar_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Text Color</label>
                                    <div class="flex items-center gap-3">
                                        <!-- Color Swatch -->
                                        <input type="color" x-model="sidebarText" name="sidebar_text" id="sidebar_text" 
                                               class="h-10 w-16 p-0 border-0 rounded cursor-pointer shadow-sm shrink-0">
                                        
                                        <!-- Hex Input -->
                                        <input type="text" x-model="sidebarText" class="uppercase flex-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        
                                        <!-- Auto-Detect Button -->
                                        <button type="button" @click="sidebarText = recommendedText()" 
                                                class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                title="Auto-detect best contrast color">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-indigo-500">
                                                <path d="M15.98 1.804a1 1 0 00-1.96 0l-.24 1.192a1 1 0 01-.784.785l-1.192.238a1 1 0 000 1.96l1.192.238a1 1 0 01.785.785l.238 1.192a1 1 0 001.96 0l.238-1.192a1 1 0 01.785-.785l1.192-.238a1 1 0 000-1.96l-1.192-.238a1 1 0 01-.785-.785l-.238-1.192zM6.949 5.684a1 1 0 00-1.898 0l-.683 2.051a1 1 0 01-.633.633l-2.051.683a1 1 0 000 1.898l2.051.683a1 1 0 01.633.633l.683 2.051a1 1 0 001.898 0l.683-2.051a1 1 0 01.633-.633l2.051-.683a1 1 0 000-1.898l-2.051-.683a1 1 0 01-.633-.633L6.95 5.684zM13.949 13.684a1 1 0 00-1.898 0l-.184.551a1 1 0 01-.632.633l-.551.183a1 1 0 000 1.898l.551.183a1 1 0 01.633.633l.183.551a1 1 0 001.898 0l.184-.551a1 1 0 01.632-.633l.551-.183a1 1 0 000-1.898l-.551-.184a1 1 0 01-.633-.632l-.183-.551z" />
                                            </svg>
                                            <span class="hidden xl:inline">Auto</span>
                                        </button>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Click Auto to pick the best text color for contrast.</p>
                                </div>
                                 
                                 <!-- Live Preview -->
                                 <div class="lg:col-span-1">
                                     <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Live Preview</label>
                                     <div class="h-24 w-full rounded-lg shadow-inner flex flex-col justify-center px-4 transition-colors duration-200 border border-gray-200 dark:border-gray-700" 
                                          :style="'background-color: ' + sidebarBg">
                                         <div class="flex items-center gap-2 mb-2" :style="'color: ' + sidebarText">
                                             <div class="w-2 h-2 rounded-full" :style="'background-color: ' + sidebarText"></div>
                                             <span class="text-sm font-medium opacity-100">Dashboard</span>
                                         </div>
                                         <div class="flex items-center gap-2" :style="'color: ' + sidebarText">
                                             <div class="w-2 h-2 rounded-full opacity-50" :style="'background-color: ' + sidebarText"></div>
                                             <span class="text-sm font-medium opacity-75">Firewalls</span>
                                         </div>
                                     </div>
                                      <button type="button" @click="sidebarBg = '#1f2937'; sidebarText = '#d1d5db'" class="mt-2 text-xs text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">Reset to Default</button>
                                 </div>
                             </div>
                        </div>

                    </div>
                </div>

                <!-- Section 3: Performance -->
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden mb-6">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">System Performance</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">Fine-tune refresh rates and caching strategies.</p>
                    </div>
                    <div class="px-4 py-5 sm:p-6 space-y-6">
                        
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                             <!-- Real-time -->
                             <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Websocket Refresh (Seconds)</label>
                                <div class="mt-1">
                                    <input type="number" name="realtime_interval" min="2" max="300" value="{{ $settings['realtime_interval'] ?? 10 }}"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <p class="mt-1 text-xs text-gray-500">Update frequency when connected to WebSocket.</p>
                                </div>
                             </div>
                             <!-- Fallback -->
                             <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Polling Refresh (Seconds)</label>
                                <div class="mt-1">
                                    <input type="number" name="fallback_interval" min="5" max="600" value="{{ $settings['fallback_interval'] ?? 30 }}"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <p class="mt-1 text-xs text-gray-500">Update frequency when WebSockets are disconnected.</p>
                                </div>
                             </div>

                             <!-- System Status Check (Locked) -->
                             <div x-data="{ 
                                    locked: true, 
                                    showModal: false,
                                    unlock() { this.locked = false; this.showModal = false; $nextTick(() => $refs.intervalInput.focus()); } 
                                }">
                                 <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">System Status Check Interval</label>
                                 <div class="mt-1 relative rounded-md shadow-sm w-full">
                                     <input type="number" name="status_check_interval" x-ref="intervalInput" min="5" max="300" value="{{ $settings['status_check_interval'] ?? 5 }}"
                                            :readonly="locked"
                                            :class="locked ? 'bg-gray-100 text-gray-500 cursor-not-allowed dark:bg-gray-800' : 'bg-white text-gray-900 dark:bg-gray-700 dark:text-white'"
                                            class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:border-gray-600">
                                     
                                     <div x-show="locked" class="absolute inset-y-0 right-0 flex items-center pr-2">
                                         <button type="button" @click="showModal = true" class="bg-gray-200 dark:bg-gray-700 px-2 py-1 text-xs rounded border border-gray-300 dark:border-gray-600 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300">Unlock</button>
                                     </div>
                                 </div>
                                 
                                 <!-- Warning Modal -->
                                 <div x-show="showModal" style="display: none;" class="relative z-50">
                                    <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm" @click="showModal = false"></div>
                                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                        <div class="flex min-h-full items-center justify-center p-4">
                                            <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:max-w-sm w-full p-6 border border-gray-200 dark:border-gray-700">
                                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/20 mb-4">
                                                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                                </div>
                                                <div class="text-center">
                                                    <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Caution Recommended</h3>
                                                    <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">Changing this value too low might cause high server load. Are you sure?</div>
                                                </div>
                                                <div class="mt-5 sm:mt-6 grid grid-cols-2 gap-3">
                                                    <button type="button" @click="showModal = false" class="inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Cancel</button>
                                                    <button type="button" @click="unlock()" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">Unlock</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                 </div>
                             </div>

                         </div>
                         
                         <!-- Status Cache (MOVED TO BOTTOM) -->
                         <div class="pt-6 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Enable Status Caching</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">If disabled, status shows "Unknown" until fresh check.</span>
                            </div>
                            <button type="button" x-data="{ on: {{ ($settings['enable_status_cache'] ?? 1) ? 'true' : 'false' }} }" 
                                    @click="on = !on; $refs.cacheInput.value = on ? '1' : '0'"
                                    :class="on ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700'"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
                                <span aria-hidden="true" 
                                      :class="on ? 'translate-x-5' : 'translate-x-0'"
                                      class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                <input type="hidden" name="enable_status_cache" x-ref="cacheInput" :value="on ? '1' : '0'">
                            </button>
                         </div>

                    </div>
                </div>

                <!-- Footer / Save -->
                <div class="flex items-center justify-end gap-x-6 pb-12">
                     @if (session('success'))
                        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm font-medium text-green-600 dark:text-green-400">
                            {{ session('success') }}
                        </div>
                    @endif
                    <button type="submit" class="rounded-md bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors">
                        Save System Settings
                    </button>
                </div>

            </form>
        </div>
    </div>
    
    <script>
        function restoreDefault(type) {
             if (confirm(`Restore default ${type}? This cannot be undone.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("system.settings.restore") }}';
                form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="type" value="${type}">`;
                document.body.appendChild(form);
                form.submit();
             }
        }
    </script>
</x-app-layout>