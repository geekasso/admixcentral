<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('DHCP Server') }} - {{ $firewall->name }}
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
                    
                    {{-- Interface Tabs --}}
                    <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                        <nav class="-mb-px flex space-x-8 overflow-x-auto">
                            @foreach($interfaces as $iface)
                                <a href="{{ route('services.dhcp.index', ['firewall' => $firewall, 'interface' => $iface['id']]) }}"
                                   class="{{ $interface === $iface['id'] 
                                        ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' 
                                        : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300' }} 
                                        whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    {{ strtoupper($iface['descr'] ?? $iface['id']) }}
                                </a>
                            @endforeach
                        </nav>
                    </div>

                    <form action="{{ route('services.dhcp.update', ['firewall' => $firewall, 'interface' => $interface]) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="pf-form-container">
                            {{-- Enable --}}
                            <div class="mb-6">
                                <div class="flex items-center">
                                    <input type="checkbox" name="enable" id="enable" 
                                           class="pf-checkbox"
                                           {{ !empty($selectedConfig['enable']) ? 'checked' : '' }}>
                                    <label for="enable" class="ml-2 block text-sm font-medium text-gray-900 dark:text-gray-100">
                                        Enable DHCP server on {{ strtoupper($interface) }} interface
                                    </label>
                                </div>
                            </div>

                            <div class="pf-form-grid">
                                {{-- Range --}}
                                <div class="pf-form-field-full">
                                    <h3 class="pf-section-header">Subnet / Range</h3>
                                </div>

                                <div>
                                    <label for="range_from" class="pf-label">Range From</label>
                                    <input type="text" name="range_from" id="range_from" 
                                           value="{{ $selectedConfig['range']['from'] ?? '' }}"
                                           class="pf-input">
                                </div>

                                <div>
                                    <label for="range_to" class="pf-label">Range To</label>
                                    <input type="text" name="range_to" id="range_to" 
                                           value="{{ $selectedConfig['range']['to'] ?? '' }}"
                                           class="pf-input">
                                </div>

                                {{-- Servers --}}
                                <div class="pf-form-field-full">
                                    <h3 class="pf-section-header">Servers</h3>
                                </div>

                                <div>
                                    <label class="pf-label">DNS Servers</label>
                                    <div class="space-y-2">
                                        <input type="text" name="dns1" value="{{ $selectedConfig['dnsserver'][0] ?? '' }}" class="pf-input" placeholder="DNS 1">
                                        <input type="text" name="dns2" value="{{ $selectedConfig['dnsserver'][1] ?? '' }}" class="pf-input" placeholder="DNS 2">
                                        <input type="text" name="dns3" value="{{ $selectedConfig['dnsserver'][2] ?? '' }}" class="pf-input" placeholder="DNS 3">
                                        <input type="text" name="dns4" value="{{ $selectedConfig['dnsserver'][3] ?? '' }}" class="pf-input" placeholder="DNS 4">
                                    </div>
                                </div>

                                <div>
                                    <label class="pf-label">Gateway</label>
                                    <input type="text" name="gateway" value="{{ $selectedConfig['gateway'] ?? '' }}" class="pf-input" placeholder="Gateway IP">
                                    <p class="pf-help-text">Leave blank to use the interface IP.</p>
                                    
                                    <label class="pf-label mt-4">Domain Name</label>
                                    <input type="text" name="domain" value="{{ $selectedConfig['domain'] ?? '' }}" class="pf-input">
                                </div>

                                {{-- Lease Time --}}
                                <div class="pf-form-field-full">
                                    <h3 class="pf-section-header">Lease Time</h3>
                                </div>

                                <div>
                                    <label class="pf-label">Default Lease Time</label>
                                    <input type="number" name="defaultleasetime" value="{{ $selectedConfig['defaultleasetime'] ?? 7200 }}" class="pf-input">
                                    <p class="pf-help-text">Seconds (Default: 7200)</p>
                                </div>

                                <div>
                                    <label class="pf-label">Maximum Lease Time</label>
                                    <input type="number" name="maxleasetime" value="{{ $selectedConfig['maxleasetime'] ?? 86400 }}" class="pf-input">
                                    <p class="pf-help-text">Seconds (Default: 86400)</p>
                                </div>

                                {{-- Network Booting --}}
                                <div class="pf-form-field-full">
                                    <h3 class="pf-section-header">Network Booting</h3>
                                </div>

                                <div>
                                    <label class="pf-label">Next Server</label>
                                    <input type="text" name="next_server" value="{{ $selectedConfig['nextserver'] ?? '' }}" class="pf-input" placeholder="IP Address">
                                </div>

                                <div>
                                    <label class="pf-label">TFTP Server</label>
                                    <input type="text" name="tftp" value="{{ $selectedConfig['tftp'] ?? '' }}" class="pf-input" placeholder="Hostname or IP">
                                </div>
                            </div>

                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <button type="submit" class="pf-btn pf-btn-primary">
                                    Save
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
