<x-app-layout>
    <x-slot name="header">
        <x-firewall-header title="{{ __('NTP') }}" :firewall="$firewall" />
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

                    <form action="{{ route('services.ntp.update', $firewall) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <h3 class="text-lg font-medium mb-4">NTP Time Servers</h3>

                        <div class="grid grid-cols-1 gap-6 mb-6">

                            <!-- Interface -->
                            <div>
                                <x-input-label for="interface" :value="__('Interface')" />
                                <!-- Simplified for now, usually multiple selection -->
                                <select id="interface" name="interface[]" multiple
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="wan" {{ in_array('wan', $ntp['interface'] ?? []) ? 'selected' : '' }}>WAN</option>
                                    <option value="lan" {{ in_array('lan', $ntp['interface'] ?? []) ? 'selected' : '' }}>LAN</option>
                                    @foreach($ntp['available_interfaces'] ?? [] as $iface => $details)
                                        <option value="{{ $iface }}" {{ in_array($iface, $ntp['interface'] ?? []) ? 'selected' : '' }}>{{ $details['descr'] ?? $iface }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Hold Ctrl (Cmd) to select multiple.</p>
                            </div>

                            <!-- Time Servers -->
                            <div>
                                <x-input-label for="timeservers" :value="__('Timeservers')" />
                                <x-text-input id="timeservers" class="block mt-1 w-full" type="text" name="timeservers"
                                    :value="is_array($ntp['timeservers'] ?? '') ? implode(' ', $ntp['timeservers']) : ($ntp['timeservers'] ?? '')" placeholder="e.g. 0.pfsense.pool.ntp.org" />
                                <p class="text-xs text-gray-500 mt-1">Space separated list of NTP servers.</p>
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
