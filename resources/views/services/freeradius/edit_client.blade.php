<x-app-layout>
    <x-slot name="header">
        <x-firewall-header title="{{ isset($client) ? __('Edit FreeRADIUS Client') : __('Add FreeRADIUS Client') }}" :firewall="$firewall" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form
                    action="{{ isset($client) ? route('services.freeradius.clients.update', ['firewall' => $firewall, 'id' => $id]) : route('services.freeradius.clients.store', $firewall) }}"
                    method="POST">
                    @csrf
                    @if(isset($client))
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 gap-6">

                        <div>
                            <x-input-label for="client" :value="__('Client IP / Hostname')" />
                            <x-text-input id="client" class="block mt-1 w-full" type="text" name="client"
                                :value="$client['client'] ?? ''" required />
                            <p class="text-sm text-gray-500 mt-1">IP address of the NAS or subnet.</p>
                        </div>

                        <div>
                            <x-input-label for="shortname" :value="__('Shortname')" />
                            <x-text-input id="shortname" class="block mt-1 w-full" type="text" name="shortname"
                                :value="$client['shortname'] ?? ''" />
                        </div>

                        <div>
                            <x-input-label for="secret" :value="__('Shared Secret')" />
                            <x-text-input id="secret" class="block mt-1 w-full" type="password" name="secret"
                                :value="$client['secret'] ?? ''" required />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <x-text-input id="description" class="block mt-1 w-full" type="text" name="description"
                                :value="$client['description'] ?? ''" />
                        </div>

                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('services.freeradius.clients.index', $firewall) }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:text-gray-800 active:bg-gray-50 transition ease-in-out duration-150">
                            Cancel
                        </a>
                        <x-primary-button>
                            {{ __('Save') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
