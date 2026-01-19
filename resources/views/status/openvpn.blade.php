{{--
    View: OpenVPN Status
    Purpose: Displays a list of OpenVPN servers and their current status.
    Data Source: $status array provided by the controller.
--}}
<x-app-layout>
    <x-slot name="header">
        <x-firewall-header title="{{ __('OpenVPN Status') }}" :firewall="$firewall" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">Server Status</h3>
                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="py-3 px-6">Name</th>
                                    <th scope="col" class="py-3 px-6">Status</th>
                                    <th scope="col" class="py-3 px-6">Connected To</th>
                                    <th scope="col" class="py-3 px-6">Virtual Address</th>
                                    <th scope="col" class="py-3 px-6">Bytes Sent/Received</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Loop through each server in the status array --}}
                                @forelse ($status as $server)
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="py-4 px-6">{{ $server['name'] ?? 'N/A' }}</td>
                                        {{-- Display Status Badge (Green for Up, Red for Down) --}}
                                        <td class="py-4 px-6">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ ($server['status'] ?? '') === 'up' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($server['status'] ?? 'Down') }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6">{{ $server['remote_host'] ?? '-' }}</td>
                                        <td class="py-4 px-6">{{ $server['virtual_addr'] ?? '-' }}</td>
                                        <td class="py-4 px-6">{{ $server['bytes_sent'] ?? 0 }} /
                                            {{ $server['bytes_recv'] ?? 0 }}</td>
                                    </tr>
                                @empty
                                    {{-- Fallback row if no servers are found --}}
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td colspan="5" class="py-4 px-6 text-center">No OpenVPN servers found.</td>
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