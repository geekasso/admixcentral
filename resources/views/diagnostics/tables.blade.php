<x-app-layout>
    <x-slot name="header">
        <x-firewall-header title="{{ __('Tables') }}" :firewall="$firewall" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Firewall Tables</h3>

                    <form method="GET" action="{{ route('diagnostics.tables', $firewall) }}" class="mb-6">
                        <div class="flex gap-4">
                            <select name="table"
                                class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                                onchange="this.form.submit()">
                                <option value="">Select a table...</option>
                                @foreach($tables as $table)
                                    <option value="{{ $table['id'] }}" {{ $selectedTable === $table['id'] ? 'selected' : '' }}>
                                        {{ $table['id'] }}</option>
                                @endforeach
                            </select>
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                View
                            </button>
                        </div>
                    </form>

                    @if($selectedTable && isset($tableContent['entries']))
                        <div class="mt-6">
                            <h4 class="text-md font-semibold mb-2">Table Content: {{ $selectedTable }}</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Entry</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @forelse($tableContent['entries'] as $entry)
                                            <tr>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                    {{ is_array($entry) ? json_encode($entry) : $entry }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    No entries found in this table.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
