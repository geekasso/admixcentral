<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create GRE Tunnel') }} - {{ $firewall->name }}
        </h2>
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

                    <form method="POST" action="{{ route('interfaces.gre.store', $firewall) }}">
                        @csrf

                        <div class="grid grid-cols-1 gap-6">

                            <!-- Parent Interface -->
                            <div>
                                <x-input-label for="if" :value="__('Parent Interface')" />
                                <select id="if" name="if"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="wan">WAN</option>
                                    <option value="lan">LAN</option>
                                    @foreach($interfaces as $iface => $details)
                                        @if(!in_array($iface, ['wan', 'lan']))
                                            <option value="{{ $iface }}">{{ $details['descr'] ?? $iface }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <!-- Remote Address -->
                            <div>
                                <x-input-label for="remote-addr" :value="__('Remote Address')" />
                                <x-text-input id="remote-addr" class="block mt-1 w-full" type="text" name="remote-addr"
                                    :value="old('remote-addr')" required placeholder="e.g. 192.168.100.1" />
                            </div>

                            <!-- Tunnel Local Address -->
                            <div>
                                <x-input-label for="tunnel-local-addr" :value="__('Tunnel Local Address (Optional)')" />
                                <x-text-input id="tunnel-local-addr" class="block mt-1 w-full" type="text"
                                    name="tunnel-local-addr" :value="old('tunnel-local-addr')"
                                    placeholder="e.g. 10.0.0.1" />
                            </div>

                            <!-- Tunnel Remote Address -->
                            <div>
                                <x-input-label for="tunnel-remote-addr" :value="__('Tunnel Remote Address (Optional)')" />
                                <x-text-input id="tunnel-remote-addr" class="block mt-1 w-full" type="text"
                                    name="tunnel-remote-addr" :value="old('tunnel-remote-addr')"
                                    placeholder="e.g. 10.0.0.2" />
                            </div>

                            <!-- CIDR -->
                            <div>
                                <x-input-label for="tunnel-remote-net" :value="__('Tunnel Remote Netmask (CIDR)')" />
                                <select id="tunnel-remote-net" name="tunnel-remote-net"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    @for($i = 32; $i >= 1; $i--)
                                        <option value="{{ $i }}" {{ (old('tunnel-remote-net', 30) == $i) ? 'selected' : '' }}>
                                            /{{ $i }}</option>
                                    @endfor
                                </select>
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
