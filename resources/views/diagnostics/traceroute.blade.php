<x-app-layout>
    <x-slot name="header">
        <x-firewall-header title="{{ __('Traceroute') }}" :firewall="$firewall" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Traceroute Host</h3>
                    <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">Trace the route packets take to a network
                        host.</p>

                    <form method="POST" action="{{ route('diagnostics.traceroute', $firewall) }}" class="mb-6">
                        @csrf
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-4 border p-4 rounded-md border-gray-200 dark:border-gray-700">
                            <div>
                                <label for="host"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Host / IP
                                    Address</label>
                                <input type="text" name="host" id="host" value="{{ old('host') }}"
                                    placeholder="google.com"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    required>
                            </div>

                            <div class="flex items-end">
                                <button type="submit"
                                    class="w-full inline-flex justify-center btn-primary">
                                    Trace Route
                                </button>
                            </div>
                        </div>
                    </form>

                    @if(isset($output))
                        <div class="mt-6">
                            <h4 class="text-md font-semibold mb-2">Traceroute Output:</h4>
                            <div class="bg-gray-100 dark:bg-gray-900 p-4 rounded-md overflow-x-auto">
                                @if(isset($output['error']))
                                    <div class="text-red-500">{{ $output['error'] }}</div>
                                @elseif(is_array($output))
                                    <pre
                                        class="text-sm font-mono text-gray-800 dark:text-gray-200">{{ implode("\n", $output) }}</pre>
                                @else
                                    <pre class="text-sm font-mono text-gray-800 dark:text-gray-200">{{ $output }}</pre>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
