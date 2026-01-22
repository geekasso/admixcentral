<x-app-layout>
    <x-slot name="header">
        <x-firewall-header title="{{ __('Edit Schedule') }}" :firewall="$firewall" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('firewall.schedules.update', [$firewall, $schedule['id']]) }}">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                :value="old('name', $schedule['name'])" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            <p class="mt-1 text-sm text-gray-500">
                                The name of the schedule. Must be alphanumeric and underscores only.
                            </p>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <x-input-label for="descr" :value="__('Description')" />
                            <x-text-input id="descr" class="block mt-1 w-full" type="text" name="descr"
                                :value="old('descr', $schedule['descr'] ?? '')" />
                            <x-input-error :messages="$errors->get('descr')" class="mt-2" />
                        </div>

                        <!-- Time Ranges (Placeholder) -->
                        <div class="mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Time Ranges</h3>

                            @if(isset($schedule['timerange']) && is_array($schedule['timerange']) && count($schedule['timerange']) > 0)
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Existing Time Ranges:</p>
                                    <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-400">
                                        @foreach($schedule['timerange'] as $range)
                                            <li>{{ json_encode($range) }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd"
                                                d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            Time Range editing is not yet supported in this version. Please use the
                                            pfSense GUI for complex time ranges.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('firewall.schedules.index', $firewall) }}"
                                class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mr-4">Cancel</a>
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
