<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $firewall->name }} - Add Firewall Rule
            </h2>
            <a href="{{ route('firewall.rules.index', $firewall) }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Cancel
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <strong class="font-bold">Success!</strong>
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <strong class="font-bold">Error!</strong>
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <strong class="font-bold">Whoops!</strong>
                            <span class="block sm:inline">There were some problems with your input.</span>
                            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('firewall.rules.store', $firewall) }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="type" class="block text-sm font-medium mb-2">Action</label>
                                <select name="type" id="type"
                                    class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                    <option value="pass">Pass</option>
                                    <option value="block">Block</option>
                                    <option value="reject">Reject</option>
                                </select>
                            </div>
                            <div>
                                <label for="interface" class="block text-sm font-medium mb-2">Interface</label>
                                <select name="interface" id="interface"
                                    class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                    @foreach($interfaces['data'] ?? [] as $iface)
                                        <option value="{{ $iface['id'] ?? $iface['if'] }}">
                                            {{ $iface['descr'] ?? $iface['if'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="ipprotocol" class="block text-sm font-medium mb-2">IP Family</label>
                                <select name="ipprotocol" id="ipprotocol"
                                    class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                    <option value="inet">IPv4</option>
                                    <option value="inet6">IPv6</option>
                                </select>
                            </div>
                            <div>
                                <label for="protocol" class="block text-sm font-medium mb-2">Protocol</label>
                                <select name="protocol" id="protocol"
                                    class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                    <option value="tcp">TCP</option>
                                    <option value="udp">UDP</option>
                                    <option value="tcp/udp">TCP/UDP</option>
                                    <option value="icmp">ICMP</option>
                                    <option value="any">Any</option>
                                </select>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mb-4">
                            <h4 class="text-lg font-medium mb-2">Source</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="src" class="block text-sm font-medium mb-2">Source Address</label>
                                    <input type="text" name="src" id="src" value="any"
                                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                        placeholder="IP, CIDR, or 'any'">
                                </div>
                                <div>
                                    <label for="srcport" class="block text-sm font-medium mb-2">Source Port</label>
                                    <input type="text" name="srcport" id="srcport"
                                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                        placeholder="Port or range (leave empty for any)">
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mb-4">
                            <h4 class="text-lg font-medium mb-2">Destination</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="dst" class="block text-sm font-medium mb-2">Destination Address</label>
                                    <input type="text" name="dst" id="dst" value="any"
                                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                        placeholder="IP, CIDR, or 'any'">
                                </div>
                                <div>
                                    <label for="dstport" class="block text-sm font-medium mb-2">Destination Port</label>
                                    <input type="text" name="dstport" id="dstport"
                                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                        placeholder="Port or range (leave empty for any)">
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="descr" class="block text-sm font-medium mb-2">Description</label>
                            <input type="text" name="descr" id="descr"
                                class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        </div>

                        <div class="flex gap-3">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Create Rule
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>