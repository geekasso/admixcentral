<x-app-layout>
    <x-slot name="header">
        <x-firewall-header title="{{ __('Gateways Status') }}" :firewall="$firewall" />
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
                                    <th scope="col" class="py-3 px-6">Name</th>
                                    <th scope="col" class="py-3 px-6">Gateway IP</th>
                                    <th scope="col" class="py-3 px-6">Monitor IP</th>
                                    <th scope="col" class="py-3 px-6">RTT</th>
                                    <th scope="col" class="py-3 px-6">RTT SD</th>
                                    <th scope="col" class="py-3 px-6">Loss</th>
                                    <th scope="col" class="py-3 px-6">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($gateways as $gateway)
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="py-4 px-6">{{ $gateway['name'] ?? '' }}</td>
                                        <td class="py-4 px-6">{{ $gateway['address'] ?? '' }}</td>
                                        <td class="py-4 px-6">{{ $gateway['monitorip'] ?? '' }}</td>
                                        <td class="py-4 px-6">{{ $gateway['delay'] ?? '' }}</td>
                                        <td class="py-4 px-6">{{ $gateway['stddev'] ?? '' }}</td>
                                        <td class="py-4 px-6">{{ $gateway['loss'] ?? '' }}</td>
                                        <td class="py-4 px-6">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ ($gateway['status'] ?? '') === 'online' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($gateway['status'] ?? 'Unknown') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td colspan="7" class="py-4 px-6 text-center">No gateways found.</td>
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
