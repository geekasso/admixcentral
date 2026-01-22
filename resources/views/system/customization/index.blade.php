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
                    <form method="POST" action="{{ route('system.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('POST')

                        <!-- Logo Upload -->
                        <div>
                            <label for="logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Custom Logo (Top Left)</label>
                            <div class="mt-2 flex items-center gap-x-3">
                                @if(isset($settings['logo_path']))
                                    <img src="{{ $settings['logo_path'] }}" alt="Current Logo" class="h-12 w-auto object-contain bg-gray-100 dark:bg-gray-700 p-1 rounded">
                                @endif
                                <input type="file" name="logo" id="logo" class="block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-full file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-50 file:text-indigo-700
                                    hover:file:bg-indigo-100
                                    dark:file:bg-gray-700 dark:file:text-gray-300
                                ">
                            </div>
                            <div class="mt-2 flex items-center gap-3">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Maximum size: 2MB. Formats: JPEG, PNG, SVG.</p>
                                @if(isset($settings['logo_path']))
                                    <button type="button" onclick="restoreDefault('logo')" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium underline">
                                        Restore to Default
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Favicon Upload -->
                        <div>
                            <label for="favicon" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Custom Favicon</label>
                            <div class="mt-2 flex items-center gap-x-3">
                                @if(isset($settings['favicon_path']))
                                    <img src="{{ $settings['favicon_path'] }}" alt="Current Favicon" class="h-8 w-8 object-contain bg-gray-100 dark:bg-gray-700 p-1 rounded">
                                @endif
                                <input type="file" name="favicon" id="favicon" class="block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-full file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-50 file:text-indigo-700
                                    hover:file:bg-indigo-100
                                    dark:file:bg-gray-700 dark:file:text-gray-300
                                ">
                            </div>
                            <div class="mt-2 flex items-center gap-3">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Maximum size: 1MB. Formats: ICO, PNG.</p>
                                @if(isset($settings['favicon_path']))
                                    <button type="button" onclick="restoreDefault('favicon')" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium underline">
                                        Restore to Default
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
                        
                        <!-- Status Check Interval -->
                        <div>
                            <label for="status_check_interval" class="block text-sm font-medium text-gray-700 dark:text-gray-300">System Status Check Interval (Seconds)</label>
                            <div class="mt-2">
                                <input type="number" name="status_check_interval" id="status_check_interval" min="5" max="300"
                                    value="{{ $settings['status_check_interval'] ?? 30 }}"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">How often the sidebar checks for backend status (Queue/DB). Default: 30s.</p>
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
