<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('System Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('system.settings.update') }}" enctype="multipart/form-data" class="space-y-6"
                          x-data="{ 
                                 sidebarBg: '{{ $settings['sidebar_bg'] ?? '#1f2937' }}', 
                                 sidebarText: '{{ $settings['sidebar_text'] ?? '#d1d5db' }}',
                                 recommendedText() {
                                    // Calculate contrast (YIQ)
                                    const r = parseInt(this.sidebarBg.substr(1,2),16);
                                    const g = parseInt(this.sidebarBg.substr(3,2),16);
                                    const b = parseInt(this.sidebarBg.substr(5,2),16);
                                    const yiq = ((r*299)+(g*587)+(b*114))/1000;
                                    return (yiq >= 128) ? '#1f2937' : '#ffffff';
                                 }
                             }">
                        @csrf
                        @method('POST')

                        <!-- Logo Upload -->
                        <div>
                            <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">Custom Logo (Top Left)</span>
                            <div class="mt-2 flex items-center gap-x-3">
                                @if(isset($settings['logo_path']))
                                    <img src="{{ $settings['logo_path'] }}" alt="Current Logo" class="h-12 w-auto object-contain p-1 rounded transition-colors duration-300"
                                         :style="'background-color: ' + sidebarBg">
                                @endif
                                <div x-data="{ fileName: null }" class="flex items-center gap-3">
                                    <input type="file" name="logo" id="logo" class="hidden" style="display:none" x-ref="logoInput"
                                           @change="fileName = $refs.logoInput.files[0] ? $refs.logoInput.files[0].name : null">
                                    <button type="button" @click="$refs.logoInput.click()" class="cursor-pointer py-2 px-4 rounded-full bg-indigo-50 text-indigo-700 text-sm font-semibold hover:bg-indigo-100 transition-colors">
                                        Choose File
                                    </button>
                                    <span x-text="fileName || 'No file chosen'" class="text-sm text-gray-500 dark:text-gray-400"></span>
                                </div>
                            </div>
                            <div class="mt-2 flex items-center gap-3">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Maximum size: 2MB. Formats: JPEG, PNG, SVG.</p>
                                @if(isset($settings['logo_path']))
                                    <button type="button" onclick="restoreDefault('logo')" class="text-xs text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 transition-colors flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                          <path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.43l.31.31a7 7 0 0011.712-3.138.75.75 0 00-1.449-.39zm1.23-3.723a.75.75 0 00.219-.53V2.929a.75.75 0 00-1.5 0v2.433l-.31-.31a7 7 0 00-11.712 3.138.75.75 0 001.449.39 5.5 5.5 0 019.201-2.466l.312.311H11.76a.75.75 0 000 1.5h4.243a.75.75 0 00.539-.219z" clip-rule="evenodd" />
                                        </svg>
                                        Restore Default
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Favicon Upload -->
                        <div>
                            <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">Custom Favicon</span>
                            <div class="mt-2 flex items-center gap-x-3">
                                @if(isset($settings['favicon_path']))
                                    <img src="{{ $settings['favicon_path'] }}" alt="Current Favicon" class="h-8 w-8 object-contain bg-gray-100 dark:bg-gray-700 p-1 rounded">
                                @endif
                                <div x-data="{ fileName: null }" class="flex items-center gap-3">
                                    <input type="file" name="favicon" id="favicon" class="hidden" style="display:none" x-ref="favInput"
                                           @change="fileName = $refs.favInput.files[0] ? $refs.favInput.files[0].name : null">
                                    <button type="button" @click="$refs.favInput.click()" class="cursor-pointer py-2 px-4 rounded-full bg-indigo-50 text-indigo-700 text-sm font-semibold hover:bg-indigo-100 transition-colors">
                                        Choose File
                                    </button>
                                    <span x-text="fileName || 'No file chosen'" class="text-sm text-gray-500 dark:text-gray-400"></span>
                                </div>
                            </div>
                            <div class="mt-2 flex items-center gap-3">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Maximum size: 1MB. Formats: ICO, PNG.</p>
                                @if(isset($settings['favicon_path']))
                                    <button type="button" onclick="restoreDefault('favicon')" class="text-xs text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 transition-colors flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                          <path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.43l.31.31a7 7 0 0011.712-3.138.75.75 0 00-1.449-.39zm1.23-3.723a.75.75 0 00.219-.53V2.929a.75.75 0 00-1.5 0v2.433l-.31-.31a7 7 0 00-11.712 3.138.75.75 0 001.449.39 5.5 5.5 0 019.201-2.466l.312.311H11.76a.75.75 0 000 1.5h4.243a.75.75 0 00.539-.219z" clip-rule="evenodd" />
                                        </svg>
                                        Restore Default
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Theme Toggle -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">System Theme</label>
                            <div class="mt-2 space-y-2">
                                <div class="flex items-center">
                                    <input id="theme_light" name="theme" type="radio" value="light" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                        {{ ($settings['theme'] ?? 'light') === 'light' ? 'checked' : '' }}>
                                    <label for="theme_light" class="ml-3 block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">Light</label>
                                </div>
                                <div class="flex items-center">
                                    <input id="theme_dark" name="theme" type="radio" value="dark" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                        {{ ($settings['theme'] ?? 'light') === 'dark' ? 'checked' : '' }}>
                                    <label for="theme_dark" class="ml-3 block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">Dark</label>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar Appearance -->
                        <div class="space-y-6 pt-4 border-t dark:border-gray-700">
                            
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Sidebar Appearance (White Labeling)</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <!-- Sidebar Background -->
                                <div>
                                    <label for="sidebar_bg" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sidebar Background Color</label>
                                    <div class="mt-2 flex items-center gap-3">
                                        <input type="color" x-model="sidebarBg" name="sidebar_bg" id="sidebar_bg" 
                                               class="h-10 w-20 p-1 block rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm cursor-pointer">
                                        <input type="text" x-model="sidebarBg" class="uppercase block w-24 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                    </div>
                                </div>

                                <!-- Sidebar Text -->
                                <div>
                                    <label for="sidebar_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sidebar Text Color</label>
                                    <div class="mt-2 flex items-center gap-3">
                                        <input type="color" x-model="sidebarText" name="sidebar_text" id="sidebar_text" 
                                               class="h-10 w-20 p-1 block rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm cursor-pointer">
                                        <button type="button" @click="sidebarText = recommendedText()" 
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-900/50 dark:text-indigo-300 dark:hover:bg-indigo-900 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                                                <path d="M15.98 1.804a1 1 0 00-1.96 0l-.24 1.192a1 1 0 01-.784.785l-1.192.238a1 1 0 000 1.96l1.192.238a1 1 0 01.785.785l.238 1.192a1 1 0 001.96 0l.238-1.192a1 1 0 01.785-.785l1.192-.238a1 1 0 000-1.96l-1.192-.238a1 1 0 01-.785-.785l-.238-1.192zM6.949 5.684a1 1 0 00-1.898 0l-.683 2.051a1 1 0 01-.633.633l-2.051.683a1 1 0 000 1.898l2.051.683a1 1 0 01.633.633l.683 2.051a1 1 0 001.898 0l.683-2.051a1 1 0 01.633-.633l2.051-.683a1 1 0 000-1.898l-2.051-.683a1 1 0 01-.633-.633L6.95 5.684zM13.949 13.684a1 1 0 00-1.898 0l-.184.551a1 1 0 01-.632.633l-.551.183a1 1 0 000 1.898l.551.183a1 1 0 01.633.633l.183.551a1 1 0 001.898 0l.184-.551a1 1 0 01.632-.633l.551-.183a1 1 0 000-1.898l-.551-.184a1 1 0 01-.633-.632l-.183-.551z" />
                                            </svg>
                                            Auto-Detect
                                        </button>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Click Auto-Detect to set best text color based on background.</p>
                                </div>
                                
                                <!-- Preview Box -->
                                <div>
                                    <div class="flex items-center justify-between">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Live Preview</label>
                                        <button type="button" @click="sidebarBg = '#1f2937'; sidebarText = '#d1d5db'" 
                                                class="text-xs text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 transition-colors flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                              <path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.43l.31.31a7 7 0 0011.712-3.138.75.75 0 00-1.449-.39zm1.23-3.723a.75.75 0 00.219-.53V2.929a.75.75 0 00-1.5 0v2.433l-.31-.31a7 7 0 00-11.712 3.138.75.75 0 001.449.39 5.5 5.5 0 019.201-2.466l.312.311H11.76a.75.75 0 000 1.5h4.243a.75.75 0 00.539-.219z" clip-rule="evenodd" />
                                            </svg>
                                            Reset Defaults
                                        </button>
                                    </div>
                                    <div class="mt-2 h-16 w-full rounded-md flex items-center px-4 transition-colors duration-200 border border-gray-200 dark:border-gray-700 shadow-sm"
                                         :style="'background-color: ' + sidebarBg">
                                         <span :style="'color: ' + sidebarText" class="font-medium animate-pulse">Preview Text</span>
                                    </div>
                                </div>
                            </div>
                        
                        <!-- Polling Intervals -->
                        <div class="space-y-6 pt-4 border-t dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Performance & Refresh Settings</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <!-- Real-time Interval -->
                                <div>
                                    <label for="realtime_interval" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Websocket Refresh Interval (Seconds)</label>
                                    <div class="mt-2">
                                        <input type="number" name="realtime_interval" id="realtime_interval" min="2" max="300"
                                            value="{{ $settings['realtime_interval'] ?? 10 }}"
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Interval for updating firewall status and metrics when WebSockets are <strong>Connected</strong>. Recommended: 5-15s.</p>
                                    </div>
                                </div>

                                <!-- Fallback Interval -->
                                <div>
                                    <label for="fallback_interval" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Polling Refresh Interval (Seconds)</label>
                                    <div class="mt-2">
                                        <input type="number" name="fallback_interval" id="fallback_interval" min="5" max="600"
                                            value="{{ $settings['fallback_interval'] ?? 30 }}"
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Interval for updating firewall status and metrics when WebSockets are <strong>Disconnected</strong> (Degraded). Recommended: 15-60s.</p>
                                    </div>
                                </div>


                            <!-- Protected System Status Check Interval -->
                            <div x-data="{ 
                                locked: true, 
                                showModal: false,
                                unlock() { this.locked = false; this.showModal = false; $nextTick(() => $refs.intervalInput.focus()); } 
                            }">
                                <label for="status_check_interval" class="block text-sm font-medium text-gray-700 dark:text-gray-300">System Status Check Interval (Seconds)</label>
                                <div class="mt-2 relative w-full">
                                    <input type="number" name="status_check_interval" id="status_check_interval" min="5" max="300" x-ref="intervalInput"
                                        value="{{ $settings['status_check_interval'] ?? 5 }}"
                                        :readonly="locked"
                                        :class="locked ? 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-500 pr-24' : 'bg-white text-gray-900 dark:bg-gray-700 dark:text-white'"
                                        class="block w-full rounded-md border-0 py-1.5 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 dark:ring-gray-600 transition-colors">
                                    
                                    <!-- Embedded Unlock Button -->
                                    <div x-show="locked" class="absolute inset-y-0 right-0 flex items-center pr-1.5">
                                        <button type="button" @click="showModal = true"
                                                class="flex items-center gap-1.5 rounded px-2 py-1 text-xs font-medium text-gray-600 hover:bg-white hover:shadow-sm hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white transition-all border border-transparent hover:border-gray-200 dark:hover:border-gray-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                                              <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                                            </svg>
                                            Unlock
                                        </button>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Health check interval for System Status indicator. Default: 5s (recommended).</p>

                                <!-- Warning Modal -->
                                <!-- Warning Modal -->
                                <div x-show="showModal" style="display: none;" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                    <!-- Backdrop -->
                                    <div x-show="showModal" 
                                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                         class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity"></div>

                                    <!-- Modal Panel -->
                                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                                            <div x-show="showModal" 
                                                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                 @click.away="showModal = false" 
                                                 class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm border border-gray-200 dark:border-gray-700">
                                        <div class="p-6">
                                            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full dark:bg-red-900/20 mb-4">
                                                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-bold text-center text-gray-900 dark:text-white mb-2">Caution Recommended</h3>
                                            <p class="text-sm text-center text-gray-600 dark:text-gray-300 mb-6">
                                                Changing this value is not recommended. An interval that is too low may cause performance degradation and high server load.
                                            </p>
                                            <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-center gap-3">
                                                <button type="button" @click="showModal = false" class="w-full inline-flex justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:w-auto dark:bg-gray-700 dark:text-gray-200 dark:ring-gray-600 dark:hover:bg-gray-600">
                                                    Cancel
                                                </button>
                                                <button type="button" @click="unlock()" class="w-full inline-flex justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:w-auto">
                                                    I Understand, Unlock
                                                </button>
                                            </div>
                                        </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit" class="inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                Save Changes
                            </button>

                            @if (session('success'))
                                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-green-600 dark:text-green-400">
                                    {{ session('success') }}
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function restoreDefault(type) {
            if (!confirm(`Are you sure you want to restore the default ${type}? This will remove your custom ${type}.`)) {
                return;
            }
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("system.settings.restore") }}';
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);
            
            const typeInput = document.createElement('input');
            typeInput.type = 'hidden';
            typeInput.name = 'type';
            typeInput.value = type;
            form.appendChild(typeInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</x-app-layout>
