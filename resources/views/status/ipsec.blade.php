<x-app-layout>
    <x-slot name="header">
        <x-firewall-header title="{{ __('IPsec Status') }}" :firewall="$firewall" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">Security Associations (SAs)</h3>
                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="py-3 px-6">Description</th>
                                    <th scope="col" class="py-3 px-6">Local ID</th>
                                    <th scope="col" class="py-3 px-6">Remote ID</th>
                                    <th scope="col" class="py-3 px-6">Status</th>
                                    <th scope="col" class="py-3 px-6">Connected</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($status as $sa)
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="py-4 px-6">{{ $sa['descr'] ?? 'N/A' }}</td>
                                        <td class="py-4 px-6">{{ $sa['localid'] ?? '' }}</td>
                                        <td class="py-4 px-6">{{ $sa['remoteid'] ?? '' }}</td>
                                        <td class="py-4 px-6">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ ($sa['status'] ?? '') === 'established' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($sa['status'] ?? 'Unknown') }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6">{{ $sa['connected'] ?? 'No' }}</td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td colspan="5" class="py-4 px-6 text-center">No IPsec SAs found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
