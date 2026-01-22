<x-app-layout>
    <x-slot name="header">
        <x-firewall-header title="{{ __('DHCP Status') }}" :firewall="$firewall" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="py-3 px-6">IP Address</th>
                                    <th scope="col" class="py-3 px-6">MAC Address</th>
                                    <th scope="col" class="py-3 px-6">Hostname</th>
                                    <th scope="col" class="py-3 px-6">Start</th>
                                    <th scope="col" class="py-3 px-6">End</th>
                                    <th scope="col" class="py-3 px-6">Online</th>
                                    <th scope="col" class="py-3 px-6">Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($leases as $lease)
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="py-4 px-6">{{ $lease['ip'] ?? '' }}</td>
                                        <td class="py-4 px-6">{{ $lease['mac'] ?? '' }}</td>
                                        <td class="py-4 px-6">{{ $lease['hostname'] ?? '' }}</td>
                                        <td class="py-4 px-6">{{ $lease['start'] ?? '' }}</td>
                                        <td class="py-4 px-6">{{ $lease['end'] ?? '' }}</td>
                                        <td class="py-4 px-6">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ ($lease['online'] ?? '') === 'online' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($lease['online'] ?? 'offline') }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6">{{ $lease['act'] ?? 'static' }}</td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td colspan="7" class="py-4 px-6 text-center">No DHCP leases found.</td>
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
