<x-app-layout>
    <x-slot name="header">
        <x-firewall-header title="{{ __('Edit HAProxy Backend') }}" :firewall="$firewall" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form
                    action="{{ isset($backend) ? route('services.haproxy.backends.update', ['firewall' => $firewall, 'id' => $id]) : route('services.haproxy.backends.store', $firewall) }}"
                    method="POST">
                    @csrf
                    @if(isset($backend))
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 gap-6">

                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                :value="$backend['name'] ?? ''" required />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <x-text-input id="description" class="block mt-1 w-full" type="text" name="description"
                                :value="$backend['description'] ?? ''" />
                        </div>

                        <div>
                            <x-input-label for="mode" :value="__('Mode')" />
                            <x-select-input id="mode" name="mode" class="block mt-1 w-full">
                                <option value="http" {{ ($backend['mode'] ?? '') === 'http' ? 'selected' : '' }}>HTTP
                                    (Layer 7)</option>
                                <option value="tcp" {{ ($backend['mode'] ?? '') === 'tcp' ? 'selected' : '' }}>TCP (Layer
                                    4)</option>
                            </x-select-input>
                        </div>

                        <div>
                            <x-input-label for="balance" :value="__('Balance Algorithm')" />
                            <x-select-input id="balance" name="balance" class="block mt-1 w-full">
                                <option value="roundrobin" {{ ($backend['balance'] ?? '') === 'roundrobin' ? 'selected' : '' }}>Round Robin</option>
                                <option value="leastconn" {{ ($backend['balance'] ?? '') === 'leastconn' ? 'selected' : '' }}>Least Connections</option>
                                <option value="source" {{ ($backend['balance'] ?? '') === 'source' ? 'selected' : '' }}>
                                    Source</option>
                            </x-select-input>
                        </div>

                        {{-- Server List (Simplified for MVP, usually this is a dynamic list of rows) --}}
                        <div class="border rounded p-4 border-gray-200 dark:border-gray-700">
                            <h4 class="font-medium mb-2">Server List (Simple Text Area)</h4>
                            <textarea name="server_list"
                                class="w-full border-gray-300 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                rows="5"
                                placeholder="name address:port ssl check inter 1000">{{ $backend['server_list'] ?? '' }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">Enter raw server config lines or a comma-separated
                                list for now.</p>
                        </div>

                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('services.haproxy.backends.index', $firewall) }}"
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
