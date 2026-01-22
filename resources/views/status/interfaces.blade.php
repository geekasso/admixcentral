<x-app-layout>
    <x-slot name="header">
        <x-firewall-header title="{{ __('Interfaces Status') }}" :firewall="$firewall" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse ($interfaces as $iface)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow">
                                <h3 class="text-lg font-semibold mb-2">{{ $iface['descr'] ?? $iface['if'] }}</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">Status:</span>
                                        <span
                                            class="font-medium {{ ($iface['status'] ?? '') === 'up' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ ucfirst($iface['status'] ?? 'Down') }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">MAC:</span>
                                        <span class="font-medium">{{ $iface['mac'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">IP:</span>
                                        <span class="font-medium">{{ $iface['ipaddr'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">Subnet:</span>
                                        <span class="font-medium">{{ $iface['subnet'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">In/Out Packets:</span>
                                        <span class="font-medium">{{ $iface['inpkts'] ?? 0 }} /
                                            {{ $iface['outpkts'] ?? 0 }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">In/Out Bytes:</span>
                                        <span class="font-medium">{{ $iface['inbytes'] ?? 0 }} /
                                            {{ $iface['outbytes'] ?? 0 }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">Errors:</span>
                                        <span class="font-medium text-red-600">{{ $iface['inerrs'] ?? 0 }} /
                                            {{ $iface['outerrs'] ?? 0 }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">Collisions:</span>
                                        <span class="font-medium text-yellow-600">{{ $iface['collisions'] ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-4">No interfaces found.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
