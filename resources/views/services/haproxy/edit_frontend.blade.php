<x-app-layout>
    <x-slot name="header">
        <x-firewall-header title="{{ __('Edit HAProxy Frontend') }}" :firewall="$firewall" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form
                    action="{{ isset($frontend) ? route('services.haproxy.frontends.update', ['firewall' => $firewall, 'id' => $id]) : route('services.haproxy.frontends.store', $firewall) }}"
                    method="POST">
                    @csrf
                    @if(isset($frontend))
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 gap-6">

                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                :value="$frontend['name'] ?? ''" required />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <x-text-input id="description" class="block mt-1 w-full" type="text" name="description"
                                :value="$frontend['description'] ?? ''" />
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <x-select-input id="status" name="status" class="block mt-1 w-full">
                                <option value="active" {{ ($frontend['status'] ?? '') === 'active' ? 'selected' : '' }}>
                                    Active</option>
                                <option value="disabled" {{ ($frontend['status'] ?? '') === 'disabled' ? 'selected' : '' }}>Disabled</option>
                            </x-select-input>
                        </div>

                        <div>
                            <x-input-label for="type" :value="__('Type')" />
                            <x-select-input id="type" name="type" class="block mt-1 w-full">
                                <option value="http" {{ ($frontend['type'] ?? '') === 'http' ? 'selected' : '' }}>HTTP /
                                    HTTPS (Offloading)</option>
                                <option value="tcp" {{ ($frontend['type'] ?? '') === 'tcp' ? 'selected' : '' }}>TCP
                                </option>
                                <option value="health" {{ ($frontend['type'] ?? '') === 'health' ? 'selected' : '' }}>
                                    Health</option>
                            </x-select-input>
                        </div>

                        <div>
                            <x-input-label for="default_backend" :value="__('Default Backend')" />
                            <x-select-input id="default_backend" name="default_backend" class="block mt-1 w-full">
                                <option value="">None</option>
                                @foreach($backends as $backend)
                                    <option value="{{ $backend['name'] }}" {{ ($frontend['default_backend'] ?? '') == $backend['name'] ? 'selected' : '' }}>
                                        {{ $backend['name'] }}
                                    </option>
                                @endforeach
                            </x-select-input>
                        </div>

                        {{-- Add more fields like Listen Address, Port, SSL, ACLs later --}}
                        <div class="text-sm text-gray-500">More advanced settings (ACLs, Actions) coming soon.</div>

                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('services.haproxy.frontends.index', $firewall) }}"
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
