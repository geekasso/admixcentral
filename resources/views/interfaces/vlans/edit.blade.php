<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit VLAN') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST"
                        action="{{ route('interfaces.vlans.update', ['firewall' => $firewall, 'id' => $id]) }}">
                        @csrf
                        @method('PATCH')

                        <!-- Parent Interface -->
                        <div class="mb-4">
                            <x-input-label for="if" :value="__('Parent Interface')" />
                            <select id="if" name="if"
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                @foreach($interfaces as $port => $info)
                                    <option value="{{ $port }}" {{ ($vlan['if'] ?? '') == $port ? 'selected' : '' }}>
                                        {{ $port }} ({{ $info['mac'] ?? '' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- VLAN Tag -->
                        <div class="mb-4">
                            <x-input-label for="tag" :value="__('VLAN Tag (1-4094)')" />
                            <x-text-input id="tag" class="block mt-1 w-full" type="number" name="tag" :value="old('tag', $vlan['tag'] ?? '')" min="1" max="4094" required />
                            <x-input-error :messages="$errors->get('tag')" class="mt-2" />
                        </div>

                        <!-- PCP -->
                        <div class="mb-4">
                            <x-input-label for="pcp" :value="__('Priority (PCP)')" />
                            <select id="pcp" name="pcp"
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                @foreach([0, 1, 2, 3, 4, 5, 6, 7] as $p)
                                    <option value="{{ $p }}" {{ ($vlan['pcp'] ?? 0) == $p ? 'selected' : '' }}>{{ $p }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <x-input-label for="descr" :value="__('Description')" />
                            <x-text-input id="descr" class="block mt-1 w-full" type="text" name="descr"
                                :value="old('descr', $vlan['descr'] ?? '')" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Update') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>