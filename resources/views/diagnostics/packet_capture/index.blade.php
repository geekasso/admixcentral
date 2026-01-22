{{-- 
    View: Packet Capture Index
    Purpose: Allow users to start and stop packet captures on a specific interface.
    Features:
    - Display running status.
    - Form to start capture with parameters (Interface, Count, Filter).
    - Form to stop capture if currently running.
--}}
<x-app-layout>
    <x-slot name="header">
        <x-firewall-header title="{{ __('Packet Capture') }}" :firewall="$firewall" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Display Session Flash Messages for Success or Error --}}
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <strong class="font-bold">Success!</strong>
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <strong class="font-bold">Error:</strong>
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    {{-- Display Generic Error if passed from Controller --}}
                    @if(isset($error))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <strong class="font-bold">Error!</strong>
                            <span class="block sm:inline">{{ $error }}</span>
                        </div>
                    @endif

                    <!-- Capture Status -->
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-md">
                        <h3 class="text-lg font-medium mb-2">Capture Status</h3>
                        {{-- Check if capture is currently running --}}
                        <p class="text-sm">
                            @if(isset($status) && ($status['running'] ?? false))
                                <span
                                    class="bg-green-100 text-green-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-green-200 dark:text-green-900">RUNNING</span>
                                Capture is currently running.
                            @else
                                <span
                                    class="bg-gray-100 text-gray-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-gray-600 dark:text-gray-300">STOPPED</span>
                                No capture running.
                            @endif
                        </p>
                    </div>

                    {{-- Conditional Rendering: Show Start Form if stopped, Stop Form if running --}}
                    @if(!(isset($status) && ($status['running'] ?? false)))
                        <!-- Start Capture Form -->
                        <form action="{{ route('diagnostics.packet_capture.start', $firewall) }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                                <!-- Interface -->
                                <div>
                                    <x-input-label for="interface" :value="__('Interface')" />
                                    <select id="interface" name="interface"
                                        class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="wan">WAN</option>
                                        <option value="lan">LAN</option>
                                        @foreach($interfaces as $iface => $details)
                                            @if(!in_array($iface, ['wan', 'lan']))
                                                <option value="{{ $iface }}">{{ $details['descr'] ?? $iface }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Count -->
                                <div>
                                    <x-input-label for="count" :value="__('Packet Count')" />
                                    <x-text-input id="count" class="block mt-1 w-full" type="number" name="count"
                                        :value="100" min="1" max="10000" />
                                </div>

                                <!-- Filter (Simple) -->
                                <div class="md:col-span-2">
                                    <x-input-label for="filter" :value="__('Filter (Optional) - NOT VALIDATED')" />
                                    <x-text-input id="filter" class="block mt-1 w-full" type="text" name="filter"
                                        placeholder="e.g. host 192.168.1.1 and port 80" />
                                    <p class="text-xs text-gray-500 mt-1">Standard tcpdump filter syntax.</p>
                                </div>

                            </div>

                            <div class="flex justify-end">
                                <x-primary-button>
                                    {{ __('Start Capture') }}
                                </x-primary-button>
                            </div>
                        </form>
                    @else
                        <!-- Stop Capture Form -->
                        {{-- Form to stop the ongoing packet capture --}}
                        <form action="{{ route('diagnostics.packet_capture.stop', $firewall) }}" method="POST">
                            @csrf
                            <div class="flex justify-end">
                                <x-primary-button class="bg-red-600 hover:bg-red-700 focus:bg-red-700">
                                    {{ __('Stop Capture') }}
                                </x-primary-button>
                            </div>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
