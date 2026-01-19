<x-app-layout>
    <x-slot name="header">
        <x-firewall-header title="{{ __('DHCP Leases') }}" :firewall="$firewall" />
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
                                    <th scope="col" class="py-3 px-6">IP Address</th>
                                    <th scope="col" class="py-3 px-6">MAC Address</th>
                                    <th scope="col" class="py-3 px-6">Hostname</th>
                                    <th scope="col" class="py-3 px-6">Start</th>
                                    <th scope="col" class="py-3 px-6">End</th>
                                    <th scope="col" class="py-3 px-6">Online Status</th>
                                    <th scope="col" class="py-3 px-6">Lease Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($leases as $lease)
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="py-4 px-6 font-medium text-gray-900 dark:text-white">{{ $lease['ip'] ?? '' }}</td>
                                        <td class="py-4 px-6 font-mono text-xs">{{ $lease['mac'] ?? '' }}</td>
                                        <td class="py-4 px-6">{{ $lease['hostname'] ?? '' }}</td>
                                        <td class="py-4 px-6 text-xs text-gray-500">{{ $lease['starts'] ?? $lease['start'] ?? '' }}</td>
                                        <td class="py-4 px-6 text-xs text-gray-500">{{ $lease['ends'] ?? $lease['end'] ?? '' }}</td>
                                        
                                        {{-- Column: Online Status --}}
                                        <td class="py-4 px-6">
                                            @php
                                                $onlineRaw = $lease['online_status'] ?? $lease['online'] ?? $lease['status'] ?? 'offline';
                                                $statusStr = strtolower((string)$onlineRaw);
                                                // Updated to include 'active/online' which is returned by the API
                                                $isOnline = in_array($statusStr, ['online', 'active', 'true', '1', 'active/online']);
                                                
                                                $onlineColor = $isOnline ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                                $onlineLabel = $isOnline ? 'Online' : 'Offline';
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $onlineColor }}">
                                                <span class="w-2 h-2 mr-1 rounded-full {{ $isOnline ? 'bg-green-400' : 'bg-gray-400' }} self-center"></span>
                                                {{ $onlineLabel }}
                                            </span>
                                        </td>

                                        {{-- Column: Lease Type (Active Status) --}}
                                        <td class="py-4 px-6">
                                            @php
                                                $actRaw = $lease['active_status'] ?? $lease['act'] ?? 'static';
                                                $actStr = strtolower((string)$actRaw);
                                                
                                                // Style: Outlined Tags with Icons
                                                // Colors: highly distinct to separate Static vs Active vs Online.
                                                // cursor-default ensures it feels informational, not interactive.
                                                $baseClasses = "inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium border cursor-default";
                                                $icon = '';
                                                
                                                switch ($actStr) {
                                                    case 'active':
                                                        // Active: Blue + Clock Icon
                                                        $colorClasses = "bg-blue-50 border-blue-200 text-blue-700 dark:bg-blue-900/30 dark:border-blue-700 dark:text-blue-300";
                                                        $icon = '<svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                                                        break;
                                                    case 'static':
                                                        // Static: Fuchsia/Pink + Plug Icon (Distinct from Blue)
                                                        $colorClasses = "bg-fuchsia-50 border-fuchsia-200 text-fuchsia-700 dark:bg-fuchsia-900/30 dark:border-fuchsia-700 dark:text-fuchsia-300";
                                                        $icon = '<svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>';
                                                        break;
                                                    case 'expired':
                                                        // Expired: Red + Alert Icon
                                                        $colorClasses = "bg-red-50 border-red-200 text-red-700 dark:bg-red-900/30 dark:border-red-700 dark:text-red-300";
                                                        $icon = '<svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                                                        break;
                                                    default:
                                                        $colorClasses = "bg-gray-50 border-gray-200 text-gray-600 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300";
                                                        $icon = '';
                                                }
                                            @endphp
                                            <span class="{{ $baseClasses }} {{ $colorClasses }}" title="Lease Type: {{ ucfirst($actStr) }}">
                                                {!! $icon !!}
                                                {{ ucfirst($actStr) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td colspan="7" class="py-4 px-6 text-center text-gray-500">No DHCP leases found.</td>
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