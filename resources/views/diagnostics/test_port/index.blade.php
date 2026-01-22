<x-app-layout>
    <x-slot name="header">
        <x-firewall-header title="{{ __('Test Port') }}" :firewall="$firewall" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    @if(isset($result))
                        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-md border-l-4 {{ str_contains($result, 'Success') ? 'border-green-500' : 'border-red-500' }}">
                            <h3 class="text-lg font-medium mb-2">Result</h3>
                            <pre class="whitespace-pre-wrap text-sm font-mono">{{ is_array($result) ? json_encode($result, JSON_PRETTY_PRINT) : $result }}</pre>
                        </div>
                    @endif

                    <form action="{{ route('diagnostics.test_port.test', $firewall) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            
                            <!-- Host -->
                            <div>
                                <x-input-label for="host" :value="__('Hostname or IP')" />
                                <x-text-input id="host" class="block mt-1 w-full" type="text" name="host" :value="old('host')" required placeholder="e.g. google.com" />
                            </div>

                            <!-- Port -->
                            <div>
                                <x-input-label for="port" :value="__('Port')" />
                                <x-text-input id="port" class="block mt-1 w-full" type="number" name="port" :value="old('port', 80)" required min="1" max="65535" />
                            </div>

                            <!-- Protocol -->
                            <div>
                                <x-input-label for="proto" :value="__('Protocol')" />
                                <select id="proto" name="proto" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="tcp">TCP</option>
                                    <option value="udp">UDP</option>
                                </select>
                            </div>

                        </div>

                        <div class="flex justify-end">
                            <x-primary-button>
                                {{ __('Test') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
