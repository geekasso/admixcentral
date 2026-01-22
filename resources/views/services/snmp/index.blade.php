<x-app-layout>
    <x-slot name="header">
        <x-firewall-header title="{{ __('SNMP') }}" :firewall="$firewall" />
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

                    <form action="{{ route('services.snmp.update', $firewall) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <h3 class="text-lg font-medium mb-4">SNMP Daemon</h3>

                        <div class="grid grid-cols-1 gap-6 mb-6">

                            <!-- Bind Interface -->
                            <div>
                                <x-input-label for="bind_interface" :value="__('Bind Interface')" />
                                <select id="bind_interface" name="bind_interface[]" multiple
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="wan" {{ in_array('wan', $snmp['bind_interface'] ?? []) ? 'selected' : '' }}>WAN</option>
                                    <option value="lan" {{ in_array('lan', $snmp['bind_interface'] ?? []) ? 'selected' : '' }}>LAN</option>
                                    @foreach($snmp['available_interfaces'] ?? [] as $iface => $details)
                                        <option value="{{ $iface }}" {{ in_array($iface, $snmp['bind_interface'] ?? []) ? 'selected' : '' }}>{{ $details['descr'] ?? $iface }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Hold Ctrl (Cmd) to select multiple.</p>
                            </div>

                            <!-- SysLocation -->
                            <div>
                                <x-input-label for="syslocation" :value="__('System Location')" />
                                <x-text-input id="syslocation" class="block mt-1 w-full" type="text" name="syslocation"
                                    :value="$snmp['syslocation'] ?? ''" />
                            </div>

                            <!-- SysContact -->
                            <div>
                                <x-input-label for="syscontact" :value="__('System Contact')" />
                                <x-text-input id="syscontact" class="block mt-1 w-full" type="text" name="syscontact"
                                    :value="$snmp['syscontact'] ?? ''" />
                            </div>

                            <!-- Read Community -->
                            <div>
                                <x-input-label for="rocommunity" :value="__('Read Community String')" />
                                <x-text-input id="rocommunity" class="block mt-1 w-full" type="text" name="rocommunity"
                                    :value="$snmp['rocommunity'] ?? 'public'" />
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
