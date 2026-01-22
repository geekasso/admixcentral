<x-app-layout>
    <x-slot name="header">
        <x-firewall-header title="{{ isset($id) ? __('Edit OpenVPN Server') : __('Add OpenVPN Server') }}" :firewall="$firewall" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('error'))
                        <div class="alert alert-danger mb-4" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST"
                        action="{{ isset($id) ? route('vpn.openvpn.server.update', [$firewall, $id]) : route('vpn.openvpn.server.store', $firewall) }}"
                        x-data="{ activeTab: 'general' }">
                        @csrf
                        @if(isset($id))
                            @method('PUT')
                        @endif

                        {{-- Tabs Navigation --}}
                        <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                            <nav class="-mb-px flex space-x-8 overflow-x-auto">
                                <button type="button" @click="activeTab = 'general'"
                                    :class="{'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'general', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300': activeTab !== 'general'}"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    General Information
                                </button>
                                <button type="button" @click="activeTab = 'crypto'"
                                    :class="{'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'crypto', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300': activeTab !== 'crypto'}"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    Cryptographic Settings
                                </button>
                                <button type="button" @click="activeTab = 'tunnel'"
                                    :class="{'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'tunnel', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300': activeTab !== 'tunnel'}"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    Tunnel Settings
                                </button>
                                <button type="button" @click="activeTab = 'client'"
                                    :class="{'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'client', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300': activeTab !== 'client'}"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    Client Settings
                                </button>
                                <button type="button" @click="activeTab = 'advanced'"
                                    :class="{'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'advanced', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300': activeTab !== 'advanced'}"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    Advanced
                                </button>
                            </nav>
                        </div>

                        {{-- General Information Tab --}}
                        <div x-show="activeTab === 'general'" class="space-y-6">
                            <div>
                                <label for="mode" class="pf-label">Server Mode</label>
                                <select class="pf-input" id="mode" name="mode" required>
                                    <option value="server_tls">Remote Access (SSL/TLS)</option>
                                    <option value="server_user">Remote Access (User Auth)</option>
                                    <option value="server_tls_user">Remote Access (SSL/TLS + User Auth)</option>
                                </select>
                            </div>

                            <div>
                                <label for="protocol" class="pf-label">Protocol</label>
                                <select class="pf-input" id="protocol" name="protocol" required>
                                    <option value="UDP4">UDP4</option>
                                    <option value="TCP4">TCP4</option>
                                    <option value="UDP6">UDP6</option>
                                    <option value="TCP6">TCP6</option>
                                </select>
                            </div>

                            <div>
                                <label for="dev_mode" class="pf-label">Device Mode</label>
                                <select class="pf-input" id="dev_mode" name="dev_mode" required>
                                    <option value="tun">tun (Layer 3 Tunnel Mode)</option>
                                    <option value="tap">tap (Layer 2 Tap Mode)</option>
                                </select>
                            </div>

                            <div>
                                <label for="interface" class="pf-label">Interface</label>
                                <select class="pf-input" id="interface" name="interface" required>
                                    @foreach($interfaces as $iface)
                                        <option value="{{ $iface['if'] ?? 'wan' }}">
                                            {{ $iface['descr'] ?? $iface['if'] ?? 'WAN' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="local_port" class="pf-label">Local Port</label>
                                <input type="number" class="pf-input" id="local_port" name="local_port" value="1194"
                                    required>
                            </div>

                            <div>
                                <label for="description" class="pf-label">Description</label>
                                <input type="text" class="pf-input" id="description" name="description">
                            </div>
                        </div>

                        {{-- Cryptographic Settings Tab --}}
                        <div x-show="activeTab === 'crypto'" class="space-y-6" style="display: none;">
                            <div>
                                <label for="caref" class="pf-label">Peer Certificate Authority</label>
                                <select class="pf-input" id="caref" name="caref" required>
                                    @foreach($cas as $ca)
                                        <option value="{{ $ca['refid'] }}">{{ $ca['descr'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="certref" class="pf-label">Server Certificate</label>
                                <select class="pf-input" id="certref" name="certref" required>
                                    @foreach($certs as $cert)
                                        <option value="{{ $cert['refid'] }}">{{ $cert['descr'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="dh_length" class="pf-label">DH Parameter Length</label>
                                <select class="pf-input" id="dh_length" name="dh_length" required>
                                    <option value="2048">2048 bit</option>
                                    <option value="4096">4096 bit</option>
                                </select>
                            </div>

                            <div>
                                <label for="ecdh_curve" class="pf-label">ECDH Curve</label>
                                <select class="pf-input" id="ecdh_curve" name="ecdh_curve" required>
                                    <option value="none">Use Default (None)</option>
                                    <option value="prime256v1">prime256v1</option>
                                    <option value="secp384r1">secp384r1</option>
                                </select>
                            </div>

                            <div>
                                <label for="data_ciphers" class="pf-label">Data Encryption Algorithms</label>
                                <select class="pf-input" id="data_ciphers" name="data_ciphers[]" multiple required
                                    size="5">
                                    <option value="AES-256-GCM" selected>AES-256-GCM</option>
                                    <option value="AES-128-GCM">AES-128-GCM</option>
                                    <option value="CHACHA20-POLY1305">CHACHA20-POLY1305</option>
                                    <option value="AES-256-CBC">AES-256-CBC</option>
                                    <option value="AES-128-CBC">AES-128-CBC</option>
                                </select>
                                <p class="pf-help-text mt-1">Hold Ctrl/Cmd to select multiple.</p>
                            </div>

                            <div>
                                <label for="data_ciphers_fallback" class="pf-label">Fallback Data Encryption
                                    Algorithm</label>
                                <select class="pf-input" id="data_ciphers_fallback" name="data_ciphers_fallback"
                                    required>
                                    <option value="AES-256-GCM">AES-256-GCM</option>
                                    <option value="AES-128-GCM">AES-128-GCM</option>
                                    <option value="AES-256-CBC">AES-256-CBC</option>
                                </select>
                            </div>

                            <div>
                                <label for="digest" class="pf-label">Auth Digest Algorithm</label>
                                <select class="pf-input" id="digest" name="digest" required>
                                    <option value="SHA256" selected>SHA256 (256-bit)</option>
                                    <option value="SHA1">SHA1 (160-bit)</option>
                                    <option value="SHA512">SHA512 (512-bit)</option>
                                </select>
                            </div>
                        </div>

                        {{-- Tunnel Settings Tab --}}
                        <div x-show="activeTab === 'tunnel'" class="space-y-6" style="display: none;">
                            <div>
                                <label for="tunnel_network" class="pf-label">IPv4 Tunnel Network</label>
                                <input type="text" class="pf-input" id="tunnel_network" name="tunnel_network"
                                    placeholder="10.0.8.0/24" required>
                            </div>

                            <div>
                                <label for="tunnel_networkv6" class="pf-label">IPv6 Tunnel Network</label>
                                <input type="text" class="pf-input" id="tunnel_networkv6" name="tunnel_networkv6"
                                    placeholder="2001:db8::/64">
                            </div>

                            <div>
                                <label class="pf-label">Redirect Gateway</label>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <input type="checkbox" id="gwredir" name="gwredir" class="pf-checkbox">
                                        <label for="gwredir" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Force
                                            all client-generated IPv4 traffic through the tunnel.</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" id="gwredir6" name="gwredir6" class="pf-checkbox">
                                        <label for="gwredir6"
                                            class="ml-2 text-sm text-gray-700 dark:text-gray-300">Force all
                                            client-generated IPv6 traffic through the tunnel.</label>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="local_network" class="pf-label">IPv4 Local Network(s)</label>
                                <input type="text" class="pf-input" id="local_network" name="local_network"
                                    placeholder="192.168.1.0/24">
                                <p class="pf-help-text">Comma-separated list of networks accessible from the remote
                                    endpoint.</p>
                            </div>

                            <div>
                                <label for="local_networkv6" class="pf-label">IPv6 Local Network(s)</label>
                                <input type="text" class="pf-input" id="local_networkv6" name="local_networkv6"
                                    placeholder="2001:db8:1::/64">
                            </div>

                            <div>
                                <label for="max_clients" class="pf-label">Concurrent Connections</label>
                                <input type="number" class="pf-input" id="max_clients" name="max_clients"
                                    placeholder="No Limit">
                            </div>

                            <div>
                                <label for="compression" class="pf-label">Compression</label>
                                <select class="pf-input" id="compression" name="compression">
                                    <option value="omit_preference">Omit Preference (Default)</option>
                                    <option value="adaptive">Adaptive LZO Compression</option>
                                    <option value="yes">LZO Compression</option>
                                    <option value="no">No Compression</option>
                                </select>
                            </div>
                        </div>

                        {{-- Client Settings Tab --}}
                        <div x-show="activeTab === 'client'" class="space-y-6" style="display: none;">
                            <div class="flex items-center">
                                <input type="checkbox" id="dynamic_ip" name="dynamic_ip" class="pf-checkbox">
                                <label for="dynamic_ip" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Allow
                                    Communication Between Clients</label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" id="topology_subnet" name="topology_subnet" class="pf-checkbox"
                                    checked>
                                <label for="topology_subnet"
                                    class="ml-2 text-sm text-gray-700 dark:text-gray-300">Allocate only one IP per
                                    client (topology subnet)</label>
                            </div>

                            <div>
                                <label for="dns_domain" class="pf-label">DNS Default Domain</label>
                                <input type="text" class="pf-input" id="dns_domain" name="dns_domain">
                            </div>

                            <div>
                                <label for="dns_server1" class="pf-label">DNS Server 1</label>
                                <input type="text" class="pf-input" id="dns_server1" name="dns_server1">
                            </div>

                            <div>
                                <label for="dns_server2" class="pf-label">DNS Server 2</label>
                                <input type="text" class="pf-input" id="dns_server2" name="dns_server2">
                            </div>
                        </div>

                        {{-- Advanced Configuration Tab --}}
                        <div x-show="activeTab === 'advanced'" class="space-y-6" style="display: none;">
                            <div>
                                <label for="verbosity_level" class="pf-label">Verbosity level</label>
                                <select class="pf-input" id="verbosity_level" name="verbosity_level">
                                    <option value="1">1 (default)</option>
                                    <option value="2">2</option>
                                    <option value="3">3 (recommended for troubleshooting)</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                </select>
                            </div>

                            <div>
                                <label for="custom_options" class="pf-label">Custom Options</label>
                                <textarea class="pf-input" id="custom_options" name="custom_options"
                                    rows="4"></textarea>
                                <p class="pf-help-text">Enter any additional OpenVPN options here, separated by
                                    semicolons.</p>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                            <button type="submit" class="pf-btn pf-btn-primary">
                                Save Server
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
