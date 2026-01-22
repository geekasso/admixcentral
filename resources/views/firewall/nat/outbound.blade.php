<x-app-layout :firewall="$firewall">
    <x-slot name="header">
        <x-firewall-header title="{{ __('Firewall NAT: Outbound') }}" :firewall="$firewall" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <x-card>

                    {{-- Tabs --}}
                    @include('firewall.nat.tabs', ['active' => 'outbound'])

                    {{-- Mode Selection --}}
                    <div class="mb-8 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <form action="{{ route('firewall.nat.outbound.mode', $firewall) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Outbound NAT Mode</h3>

                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input id="mode_automatic" name="mode" type="radio" value="automatic"
                                        {{ ($mode ?? 'automatic') === 'automatic' ? 'checked' : '' }}
                                        class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                    <label for="mode_automatic"
                                        class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Automatic Outbound NAT rule generation
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="mode_hybrid" name="mode" type="radio" value="hybrid"
                                        {{ ($mode ?? 'automatic') === 'hybrid' ? 'checked' : '' }}
                                        class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                    <label for="mode_hybrid"
                                        class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Hybrid Outbound NAT rule generation
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="mode_advanced" name="mode" type="radio" value="advanced"
                                        {{ ($mode ?? 'automatic') === 'advanced' ? 'checked' : '' }}
                                        class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                    <label for="mode_advanced"
                                        class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Manual Outbound NAT rule generation
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="mode_disabled" name="mode" type="radio" value="disabled"
                                        {{ ($mode ?? 'automatic') === 'disabled' ? 'checked' : '' }}
                                        class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                    <label for="mode_disabled"
                                        class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Disable Outbound NAT rule generation
                                    </label>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn-primary">
                                    Save Mode
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Rules Table --}}
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Mappings</h3>
                        <x-link-button-add href="{{ route('firewall.nat.outbound.create', $firewall) }}">
                            Add Mapping
                        </x-link-button-add>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Interface</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Source</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Source Port</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Destination</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Dest. Port</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">NAT Address</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">NAT Port</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Static Port</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($rules as $rule)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ strtoupper($rule['interface'] ?? '') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ is_array($rule['source']) ? ($rule['source']['network'] ?? 'any') : $rule['source'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ is_array($rule['source']) && isset($rule['source']['port']) ? $rule['source']['port'] : '*' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ is_array($rule['destination']) ? ($rule['destination']['network'] ?? 'any') : $rule['destination'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ is_array($rule['destination']) && isset($rule['destination']['port']) ? $rule['destination']['port'] : '*' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $rule['target'] ?? '' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $rule['local-port'] ?? '' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ isset($rule['static-port']) ? 'Yes' : 'No' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $rule['descr'] ?? '' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            No outbound rules defined.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    </div>
            </x-card>
        </div>
</x-app-layout>
