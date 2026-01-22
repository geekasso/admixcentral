<x-app-layout>
    <x-slot name="header">
        <x-firewall-header title="{{ __('Create Wireless Interface') }}" :firewall="$firewall" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <strong class="font-bold">Error:</strong>
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('interfaces.wireless.store', $firewall) }}">
                        @csrf

                        <div class="grid grid-cols-1 gap-6">

                            <!-- Parent Interface -->
                            <div>
                                <x-input-label for="if" :value="__('Parent Interface')" />
                                <select id="if" name="if"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">Select an Interface (Must be wireless capable)</option>
                                    @foreach($interfaces as $iface => $details)
                                        <option value="{{ $iface }}">{{ $details['descr'] ?? $iface }}
                                            ({{ $details['mac'] ?? '' }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Mode -->
                            <div>
                                <x-input-label for="mode" :value="__('Mode')" />
                                <select id="mode" name="mode"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="bss">Infrastructure (BSS)</option>
                                    <option value="hostap">Access Point</option>
                                    <option value="ibss">Ad-hoc (IBSS)</option>
                                </select>
                            </div>

                            <!-- SSID -->
                            <div>
                                <x-input-label for="ssid" :value="__('SSID')" />
                                <x-text-input id="ssid" class="block mt-1 w-full" type="text" name="ssid"
                                    :value="old('ssid')" required />
                            </div>

                            <!-- Description -->
                            <div>
                                <x-input-label for="descr" :value="__('Description')" />
                                <x-text-input id="descr" class="block mt-1 w-full" type="text" name="descr"
                                    :value="old('descr')" />
                            </div>
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
