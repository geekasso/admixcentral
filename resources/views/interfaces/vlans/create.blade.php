<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create VLAN') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST" action="{{ route('interfaces.vlans.store', $firewall) }}">
                        @csrf

                        <!-- Parent Interface -->
                        <div class="mb-4">
                            <x-input-label for="if" :value="__('Parent Interface')" />
                            <select id="if" name="if"
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                @foreach($interfaces as $port => $info)
                                    <option value="{{ $port }}">{{ $port }} ({{ $info['mac'] ?? '' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- VLAN Tag -->
                        <div class="mb-4">
                            <x-input-label for="tag" :value="__('VLAN Tag (1-4094)')" />
                            <x-text-input id="tag" class="block mt-1 w-full" type="number" name="tag"
                                :value="old('tag')" min="1" max="4094" required />
                            <x-input-error :messages="$errors->get('tag')" class="mt-2" />
                        </div>

                        <!-- PCP -->
                        <div class="mb-4">
                            <x-input-label for="pcp" :value="__('Priority (PCP)')" />
                            <select id="pcp" name="pcp"
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="0">0 (Best Effort)</option>
                                <option value="1">1 (Background)</option>
                                <option value="2">2 (Excellent Effort)</option>
                                <option value="3">3 (Critical Applications)</option>
                                <option value="4">4 (Video, < 100ms latency)</option>
                                <option value="5">5 (Voice, < 10ms latency)</option>
                                <option value="6">6 (Internetwork Control)</option>
                                <option value="7">7 (Network Control)</option>
                            </select>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <x-input-label for="descr" :value="__('Description')" />
                            <x-text-input id="descr" class="block mt-1 w-full" type="text" name="descr"
                                :value="old('descr')" />
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