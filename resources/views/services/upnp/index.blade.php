<x-app-layout>
    <x-slot name="header">
        <x-firewall-header title="{{ __('UPnP & NAT-PMP') }}" :firewall="$firewall" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <strong class="font-bold">Success!</strong>
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <strong class="font-bold">Error:</strong>
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    @if(isset($error))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <strong class="font-bold">Error!</strong>
                            <span class="block sm:inline">{{ $error }}</span>
                        </div>
                    @endif

                    <form action="{{ route('services.upnp.update', $firewall) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Enable UPnP -->
                        <div class="mb-6">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="enable" value="yes"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    {{ ($upnp['enable'] ?? false) ? 'checked' : '' }}>
                                <span
                                    class="ml-2 text-gray-700 dark:text-gray-300">{{ __('Enable UPnP & NAT-PMP') }}</span>
                            </label>
                        </div>

                        <!-- Enable NAT-PMP -->
                        <div class="mb-6">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="enable_natpmp" value="yes"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    {{ ($upnp['enable_natpmp'] ?? false) ? 'checked' : '' }}>
                                <span class="ml-2 text-gray-700 dark:text-gray-300">{{ __('Enable NAT-PMP') }}</span>
                            </label>
                        </div>

                        <!-- Interfaces (Internal) -->
                        <div class="grid grid-cols-1 gap-6 mb-6">
                            <div>
                                <x-input-label for="iface_acl" :value="__('Interfaces (Internal)')" />
                                <select id="iface_acl" name="iface_acl[]" multiple
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm h-32">
                                    <option value="lan" {{ in_array('lan', $upnp['iface_acl'] ?? []) ? 'selected' : '' }}>LAN</option>
                                    @foreach($upnp['available_interfaces'] ?? [] as $iface => $details)
                                        <option value="{{ $iface }}" {{ in_array($iface, $upnp['iface_acl'] ?? []) ? 'selected' : '' }}>{{ $details['descr'] ?? $iface }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Interfaces to listen on.</p>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <x-primary-button>
                                {{ __('Save') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
