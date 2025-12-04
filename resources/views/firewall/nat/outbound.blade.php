<x-app-layout :firewall="$firewall">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Firewall NAT: Outbound') }} - {{ $firewall->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="pf-alert pf-alert-success mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="pf-alert pf-alert-error mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

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
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                    <label for="mode_automatic"
                                        class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Automatic Outbound NAT rule generation
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="mode_hybrid" name="mode" type="radio" value="hybrid"
                                        {{ ($mode ?? 'automatic') === 'hybrid' ? 'checked' : '' }}
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                    <label for="mode_hybrid"
                                        class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Hybrid Outbound NAT rule generation
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="mode_advanced" name="mode" type="radio" value="advanced"
                                        {{ ($mode ?? 'automatic') === 'advanced' ? 'checked' : '' }}
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                    <label for="mode_advanced"
                                        class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Manual Outbound NAT rule generation
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="mode_disabled" name="mode" type="radio" value="disabled"
                                        {{ ($mode ?? 'automatic') === 'disabled' ? 'checked' : '' }}
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                    <label for="mode_disabled"
                                        class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Disable Outbound NAT rule generation
                                    </label>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="pf-btn pf-btn-primary">
                                    Save Mode
                                </button>
                            </div>
                        </form>
                    </div>
                        {{-- Rules Table --}}
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Mappings</h3>
                        <div class="pf-table-container">
                            <table class="pf-table">
                                <thead>
                                    <tr>
                                        <th>Interface</th>
                                        <th>Source</th>
                                        <th>Source Port</th>
                                        <th>Destination</th>
                                        <th>Dest. Port</th>
                                        <th>NAT Address</th>
                                        <th>NAT Port</th>
                                        <th>Static Port</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rules as $rule)
                                        <tr>
                                            <td data-label="Interface">{{ strtoupper($rule['interface'] ?? '') }}</td>
                                            <td data-label="Source">
                                                {{ is_array($rule['source']) ? ($rule['source']['network'] ?? 'any') : $rule['source'] }}
                                            </td>
                                            <td data-label="Source Port">
                                                {{ is_array($rule['source']) && isset($rule['source']['port']) ? $rule['source']['port'] : '*' }}
                                            </td>
                                            <td data-label="Destination">
                                                {{ is_array($rule['destination']) ? ($rule['destination']['network'] ?? 'any') : $rule['destination'] }}
                                            </td>
                                            <td data-label="Dest. Port">
                                                {{ is_array($rule['destination']) && isset($rule['destination']['port']) ? $rule['destination']['port'] : '*' }}
                                            </td>
                                            <td data-label="NAT Address">{{ $rule['target'] ?? '' }}</td>
                                            <td data-label="NAT Port">{{ $rule['local-port'] ?? '' }}</td>
                                            <td data-label="Static Port">{{ isset($rule['static-port']) ? 'Yes' : 'No' }}
                                            </td>
                                            <td data-label="Description">{{ $rule['descr'] ?? '' }}</td>
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
                </div>
            </div>
        </div>
</x-app-layout>