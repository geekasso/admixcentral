<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $firewall->name }} - Dashboard
            </h2>
            <a href="{{ route('firewalls.edit', $firewall) }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Edit Settings
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(isset($apiError) && $apiError)
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
                    <p class="font-bold">Warning</p>
                    <p>Some data could not be retrieved: {{ $apiError }}</p>
                </div>
            @endif

            {{-- System Status --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">System Information</h3>
                    @if(isset($systemStatus['connected']) && $systemStatus['connected'])
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                                <p class="text-lg font-bold text-green-600 dark:text-green-400">Online</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Platform</p>
                                <p class="text-lg font-bold">{{ $systemStatus['data']['platform'] ?? 'Unknown' }}</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Version</p>
                                <p class="text-lg font-bold">{{ $systemStatus['data']['version'] ?? 'Unknown' }}</p>
                            </div>
                        </div>
                    @else
                        <div class="bg-red-50 dark:bg-red-900 p-4 rounded-lg">
                            <p class="text-red-600 dark:text-red-400 font-semibold">Unable to connect to firewall</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Nav --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Management</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="{{ route('firewall.interfaces.index', $firewall) }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white p-4 rounded-lg text-center transition">
                            <p class="font-bold">Interfaces</p>
                            <p class="text-sm">{{ isset($interfaces['data']) ? count($interfaces['data']) : 0 }}</p>
                        </a>
                        <a href="{{ route('firewall.rules.index', $firewall) }}"
                            class="bg-purple-500 hover:bg-purple-700 text-white p-4 rounded-lg text-center transition">
                            <p class="font-bold">Firewall Rules</p>
                            <p class="text-sm">{{ isset($firewallRules['data']) ? count($firewallRules['data']) : 0 }}
                            </p>
                        </a>
                        <a href="#"
                            class="bg-green-500 hover:bg-green-700 text-white p-4 rounded-lg text-center transition">
                            <p class="font-bold">Services</p>
                            <p class="text-sm">Manage</p>
                        </a>
                        <a href="#"
                            class="bg-orange-500 hover:bg-orange-700 text-white p-4 rounded-lg text-center transition">
                            <p class="font-bold">VPN</p>
                            <p class="text-sm">Configure</p>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Interfaces Summary --}}
            @if(isset($interfaces['data']) && count($interfaces['data']) > 0)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">Interfaces</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            Interface</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            Description</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            IP Address</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($interfaces['data'] as $interface)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap font-mono">
                                                {{ $interface['id'] ?? $interface['if'] ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $interface['descr'] ?? $interface['description'] ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if(isset($interface['enable']) && $interface['enable'])
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                                        Enabled
                                                    </span>
                                                @else
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                                        Disabled
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap font-mono text-sm">
                                                {{ $interface['ipaddr'] ?? $interface['ipaddrv4'] ?? 'N/A' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Gateways Summary --}}
            @if(isset($gateways) && count($gateways) > 0)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">Gateways</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            Name</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            Interface</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            Gateway</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            Monitor IP</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($gateways as $gateway)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap font-bold">
                                                {{ $gateway['name'] ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap uppercase">
                                                {{ $gateway['interface'] ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap font-mono text-sm">
                                                {{ $gateway['gateway'] ?? 'Dynamic' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap font-mono text-sm">
                                                {{ $gateway['monitor'] ?? 'N/A' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>