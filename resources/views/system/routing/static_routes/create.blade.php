<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System: Routing: Static Routes: Add') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('system.routing.static_routes.store', $firewall->id) }}">
                        @csrf

                        <!-- Network -->
                        <div class="mb-4">
                            <x-input-label for="network" :value="__('Destination Network')" />
                            <x-text-input id="network" class="block mt-1 w-full" type="text" name="network"
                                :value="old('network')" required autofocus placeholder="e.g., 192.168.10.0/24" />
                            <x-input-error :messages="$errors->get('network')" class="mt-2" />
                        </div>

                        <!-- Gateway -->
                        <div class="mb-4">
                            <x-input-label for="gateway" :value="__('Gateway')" />
                            <select id="gateway" name="gateway"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach($gateways as $gw)
                                    <option value="{{ $gw['name'] }}">{{ $gw['name'] }} ({{ $gw['gateway'] }})</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('gateway')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <x-input-label for="descr" :value="__('Description')" />
                            <x-text-input id="descr" class="block mt-1 w-full" type="text" name="descr"
                                :value="old('descr')" />
                            <x-input-error :messages="$errors->get('descr')" class="mt-2" />
                        </div>

                        <!-- Disabled -->
                        <div class="flex items-center mb-4">
                            <input id="disabled" name="disabled" type="checkbox"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('disabled') ? 'checked' : '' }}>
                            <label for="disabled"
                                class="ml-2 block text-sm text-gray-900">{{ __('Disable this static route') }}</label>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('system.routing.index', ['firewall' => $firewall->id, 'tab' => 'static_routes']) }}"
                                class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
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