<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add OpenVPN Server') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('firewall.vpn.openvpn.server.store', $firewall) }}">
                        @csrf

                        <div class="mb-3">
                            <label for="mode" class="form-label">Server Mode</label>
                            <select class="form-control" id="mode" name="mode" required>
                                <option value="server_tls">Remote Access (SSL/TLS)</option>
                                <option value="server_user">Remote Access (User Auth)</option>
                                <option value="server_tls_user">Remote Access (SSL/TLS + User Auth)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="protocol" class="form-label">Protocol</label>
                            <select class="form-control" id="protocol" name="protocol" required>
                                <option value="UDP4">UDP4</option>
                                <option value="TCP4">TCP4</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="dev_mode" class="form-label">Device Mode</label>
                            <select class="form-control" id="dev_mode" name="dev_mode" required>
                                <option value="tun">tun (Layer 3 Tunnel Mode)</option>
                                <option value="tap">tap (Layer 2 Tap Mode)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="interface" class="form-label">Interface</label>
                            <select class="form-control" id="interface" name="interface" required>
                                @foreach($interfaces as $iface)
                                    <option value="{{ $iface['if'] ?? 'wan' }}">
                                        {{ $iface['descr'] ?? $iface['if'] ?? 'WAN' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="local_port" class="form-label">Local Port</label>
                            <input type="number" class="form-control" id="local_port" name="local_port" value="1194"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="caref" class="form-label">Peer Certificate Authority</label>
                            <select class="form-control" id="caref" name="caref" required>
                                @foreach($cas as $ca)
                                    <option value="{{ $ca['refid'] }}">{{ $ca['descr'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="certref" class="form-label">Server Certificate</label>
                            <select class="form-control" id="certref" name="certref" required>
                                @foreach($certs as $cert)
                                    <option value="{{ $cert['refid'] }}">{{ $cert['descr'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="dh_length" class="form-label">DH Parameter Length</label>
                            <select class="form-control" id="dh_length" name="dh_length" required>
                                <option value="2048">2048 bit</option>
                                <option value="4096">4096 bit</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="ecdh_curve" class="form-label">ECDH Curve</label>
                            <select class="form-control" id="ecdh_curve" name="ecdh_curve" required>
                                <option value="none">Use Default (None)</option>
                                <option value="prime256v1">prime256v1</option>
                                <option value="secp384r1">secp384r1</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="data_ciphers" class="form-label">Data Encryption Algorithms</label>
                            <select class="form-control" id="data_ciphers" name="data_ciphers[]" multiple required>
                                <option value="AES-256-GCM" selected>AES-256-GCM</option>
                                <option value="AES-128-GCM">AES-128-GCM</option>
                                <option value="CHACHA20-POLY1305">CHACHA20-POLY1305</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="data_ciphers_fallback" class="form-label">Fallback Data Encryption
                                Algorithm</label>
                            <select class="form-control" id="data_ciphers_fallback" name="data_ciphers_fallback"
                                required>
                                <option value="AES-256-GCM">AES-256-GCM</option>
                                <option value="AES-128-GCM">AES-128-GCM</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="digest" class="form-label">Auth Digest Algorithm</label>
                            <select class="form-control" id="digest" name="digest" required>
                                <option value="SHA256" selected>SHA256 (256-bit)</option>
                                <option value="SHA1">SHA1 (160-bit)</option>
                                <option value="SHA512">SHA512 (512-bit)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="tunnel_network" class="form-label">Tunnel Network</label>
                            <input type="text" class="form-control" id="tunnel_network" name="tunnel_network"
                                placeholder="10.0.8.0/24" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="description" name="description">
                        </div>

                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>