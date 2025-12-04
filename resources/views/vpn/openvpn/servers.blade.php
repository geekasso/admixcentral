<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('OpenVPN Servers') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="mb-3">
                        <a href="{{ route('firewall.vpn.openvpn.server.create', $firewall) }}" class="btn btn-primary">Add
                            Server</a>
                    </div>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Protocol</th>
                                <th>Interface</th>
                                <th>Port</th>
                                <th>Tunnel Network</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($servers as $server)
                                <tr>
                                    <td>{{ $server['protocol'] ?? '' }}</td>
                                    <td>{{ $server['interface'] ?? '' }}</td>
                                    <td>{{ $server['local_port'] ?? '' }}</td>
                                    <td>{{ $server['tunnel_network'] ?? '' }}</td>
                                    <td>{{ $server['description'] ?? '' }}</td>
                                    <td>
                                        <!-- Actions -->
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">No OpenVPN servers found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>