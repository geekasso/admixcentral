<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('OpenVPN Clients') }}
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

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Protocol</th>
                                <th>Interface</th>
                                <th>Port</th>
                                <th>Server Address</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($clients as $client)
                                <tr>
                                    <td>{{ $client['protocol'] ?? '' }}</td>
                                    <td>{{ $client['interface'] ?? '' }}</td>
                                    <td>{{ $client['local_port'] ?? '' }}</td>
                                    <td>{{ $client['server_addr'] ?? '' }}</td>
                                    <td>{{ $client['description'] ?? '' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">No OpenVPN clients found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>