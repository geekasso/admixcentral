{{--
    View: Services Status
    Purpose: Lists all services on the firewall and indicates whether they are running or stopped.
    Note: 'Actions' column is currently a placeholder for future functionality (Start/Stop/Restart).
--}}
<x-app-layout>
    <x-slot name="header">
        <x-firewall-header title="{{ __('Services Status') }}" :firewall="$firewall" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="py-3 px-6">Service</th>
                                    <th scope="col" class="py-3 px-6">Description</th>
                                    <th scope="col" class="py-3 px-6">Status</th>
                                    <th scope="col" class="py-3 px-6">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($services as $service)
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="py-4 px-6 font-medium text-gray-900 dark:text-white">
                                            {{ $service['name'] ?? '' }}
                                        </td>
                                        <td class="py-4 px-6">{{ $service['description'] ?? '' }}</td>
                                        <td class="py-4 px-6">
                                            @php
                                                // Determine status: API may return '1'/ 'running' or other indicators.
                                                $rawStatus = $service['status'] ?? '';
                                                $isRunning = ($rawStatus == '1' || $rawStatus === 'running');
                                            @endphp
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $isRunning ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $isRunning ? 'Running' : 'Stopped' }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6">
                                            {{-- Actions like Start/Stop/Restart would go here, but API might not support
                                            them directly via this endpoint --}}
                                            <span class="text-gray-400">Managed via pfSense</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td colspan="4" class="py-4 px-6 text-center">No services found.</td>
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