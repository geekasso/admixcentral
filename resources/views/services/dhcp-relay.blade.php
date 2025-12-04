<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('DHCP Relay') }} - {{ $firewall->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="mb-4 font-medium text-sm text-red-600 dark:text-red-400">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('services.dhcp-relay.update', $firewall) }}">
                        @csrf

                        <!-- Enable -->
                        <div class="mb-4">
                            <label for="enable" class="inline-flex items-center">
                                <input id="enable" type="checkbox"
                                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                    name="enable" value="yes" {{ isset($config['enable']) ? 'checked' : '' }}>
                                <span
                                    class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Enable DHCP Relay') }}</span>
                            </label>
                        </div>

                        <!-- Interface -->
                        <div class="mb-4">
                            <x-input-label for="interface" :value="__('Interface(s)')" />
                            <select id="interface" name="interface[]" multiple
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                size="5">
                                <option value="lan" {{ in_array('lan', $config['interface'] ?? []) ? 'selected' : '' }}>
                                    LAN</option>
                                <option value="wan" {{ in_array('wan', $config['interface'] ?? []) ? 'selected' : '' }}>
                                    WAN</option>
                                <!-- Add logic to list all interfaces dynamically if available -->
                            </select>
                            <p class="mt-1 text-sm text-gray-500">Select interfaces to relay. Ctrl+Click to select
                                multiple.</p>
                        </div>

                        <!-- Destination Server -->
                        <div class="mb-4">
                            <x-input-label for="server" :value="__('Destination Server(s)')" />
                            <x-text-input id="server" class="block mt-1 w-full" type="text" name="server"
                                :value="is_array($config['server'] ?? '') ? implode(',', $config['server']) : ($config['server'] ?? '')" />
                            <p class="mt-1 text-sm text-gray-500">IP address(es) of the DHCP server. Separate multiple
                                servers with commas.</p>
                        </div>

                        <!-- Append Circuit ID -->
                        <div class="mb-4">
                            <label for="agentoption" class="inline-flex items-center">
                                <input id="agentoption" type="checkbox"
                                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                    name="agentoption" value="yes" {{ isset($config['agentoption']) ? 'checked' : '' }}>
                                <span
                                    class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Append Circuit ID and Agent ID to requests') }}</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Save') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>