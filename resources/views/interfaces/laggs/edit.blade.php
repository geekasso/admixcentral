<x-app-layout>
    <x-slot name="header">
        <x-firewall-header title="{{ __('Edit LAGG') }}" :firewall="$firewall" />
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

                    <form method="POST" action="{{ route('interfaces.laggs.update', [$firewall, $id]) }}">
                        @csrf
                        @method('PATCH')

                        @php
                            $currentMembers = $lagg['members'] ?? [];
                            if (is_string($currentMembers)) {
                                $currentMembers = explode(',', $currentMembers);
                                $currentMembers = array_map('trim', $currentMembers);
                            }
                        @endphp

                        <div class="grid grid-cols-1 gap-6">

                            <!-- Protocol -->
                            <div>
                                <x-input-label for="proto" :value="__('LAGG Protocol')" />
                                <select id="proto" name="proto"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="lacp" {{ ($lagg['proto'] ?? '') == 'lacp' ? 'selected' : '' }}>LACP
                                        (802.3ad) - Load Balance</option>
                                    <option value="failover" {{ ($lagg['proto'] ?? '') == 'failover' ? 'selected' : '' }}>
                                        Failover</option>
                                    <option value="fec" {{ ($lagg['proto'] ?? '') == 'fec' ? 'selected' : '' }}>FEC (Fast
                                        EtherChannel)</option>
                                    <option value="loadbalance" {{ ($lagg['proto'] ?? '') == 'loadbalance' ? 'selected' : '' }}>Loadbalance</option>
                                    <option value="roundrobin" {{ ($lagg['proto'] ?? '') == 'roundrobin' ? 'selected' : '' }}>Roundrobin</option>
                                    <option value="none" {{ ($lagg['proto'] ?? '') == 'none' ? 'selected' : '' }}>None
                                    </option>
                                </select>
                            </div>

                            <!-- Member Interfaces -->
                            <div>
                                <x-input-label for="members" :value="__('Member Interfaces')" />
                                <select id="members" name="members[]" multiple
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm h-48">
                                    @foreach($interfaces as $iface => $details)
                                        <option value="{{ $iface }}" {{ in_array($iface, $currentMembers) ? 'selected' : '' }}>
                                            {{ $iface }} ({{ $details['mac'] ?? 'No MAC' }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-sm text-gray-500 mt-1">Hold Ctrl (Windows) or Cmd (Mac) to select
                                    multiple interfaces.</p>
                            </div>

                            <!-- Description -->
                            <div>
                                <x-input-label for="descr" :value="__('Description')" />
                                <x-text-input id="descr" class="block mt-1 w-full" type="text" name="descr"
                                    :value="old('descr', $lagg['descr'] ?? '')" />
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
