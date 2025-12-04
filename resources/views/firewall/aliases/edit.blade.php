<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ isset($alias['id']) ? 'Edit' : 'Add' }} Firewall Alias - {{ $firewall->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <form
                    action="{{ isset($alias['id']) ? route('firewall.aliases.update', [$firewall, $alias['id']]) : route('firewall.aliases.store', $firewall) }}"
                    method="POST"
                    x-data="aliasForm({{ json_encode($alias['address'] ?? ['']) }}, {{ json_encode($alias['detail'] ?? ['']) }})">
                    @csrf
                    @if(isset($alias['id']))
                        @method('PUT')
                    @endif

                    <div class="p-6 space-y-6">
                        {{-- Alias Properties --}}
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Alias Properties</h3>

                            {{-- Name --}}
                            <div class="mb-4">
                                <label for="name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                                    Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" value="{{ old('name', $alias['name'] ?? '') }}"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-900 dark:border-gray-600 dark:text-gray-300"
                                    required pattern="[a-zA-Z0-9_]+" maxlength="255"
                                    placeholder="Alphanumeric and underscore only">
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">The name may only consist of
                                    letters, numbers, and underscores.</p>
                            </div>

                            {{-- Description --}}
                            <div class="mb-4">
                                <label for="descr"
                                    class="block font-medium text-sm text-gray-700 dark:text-gray-300">Description</label>
                                <input type="text" name="descr" id="descr"
                                    value="{{ old('descr', $alias['descr'] ?? '') }}"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-900 dark:border-gray-600 dark:text-gray-300"
                                    placeholder="Description for this alias">
                            </div>

                            {{-- Type --}}
                            <div class="mb-4">
                                <label for="type" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                                    Type <span class="text-red-500">*</span>
                                </label>
                                <select name="type" id="type"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-900 dark:border-gray-600 dark:text-gray-300"
                                    required>
                                    <option value="host" {{ old('type', $alias['type'] ?? 'host') === 'host' ? 'selected' : '' }}>Host(s)</option>
                                    <option value="network" {{ old('type', $alias['type'] ?? '') === 'network' ? 'selected' : '' }}>Network(s)</option>
                                    <option value="port" {{ old('type', $alias['type'] ?? '') === 'port' ? 'selected' : '' }}>Port(s)</option>
                                    <option value="url" {{ old('type', $alias['type'] ?? '') === 'url' ? 'selected' : '' }}>URL (IPs)</option>
                                    <option value="urltable" {{ old('type', $alias['type'] ?? '') === 'urltable' ? 'selected' : '' }}>URL Table (IPs)</option>
                                </select>
                            </div>
                        </div>

                        {{-- Host(s) / Network(s) / Port(s) --}}
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Entries</h3>

                            <template x-for="(entry, index) in entries" :key="index">
                                <div class="flex gap-2 mb-2">
                                    <div class="flex-grow">
                                        <input type="text" :name="'address[' + index + ']'"
                                            x-model="entries[index].address"
                                            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-900 dark:border-gray-600 dark:text-gray-300"
                                            placeholder="IP Address, CIDR, Port, or URL" required>
                                    </div>
                                    <div class="flex-grow">
                                        <input type="text" :name="'detail[' + index + ']'"
                                            x-model="entries[index].detail"
                                            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-900 dark:border-gray-600 dark:text-gray-300"
                                            placeholder="Description (optional)">
                                    </div>
                                    <button type="button" @click="removeEntry(index)" x-show="entries.length > 1"
                                        class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </template>

                            <button type="button" @click="addEntry()"
                                class="mt-2 inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Entry
                            </button>
                        </div>

                        {{-- Actions --}}
                        <div
                            class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('firewall.aliases.index', $firewall) }}"
                                class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Save
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function aliasForm(addresses, details) {
            // Ensure arrays are same length
            const maxLength = Math.max(addresses.length, details.length, 1);
            const paddedAddresses = [...addresses, ...Array(maxLength - addresses.length).fill('')];
            const paddedDetails = [...details, ...Array(maxLength - details.length).fill('')];

            return {
                entries: paddedAddresses.map((addr, idx) => ({
                    address: addr,
                    detail: paddedDetails[idx] || ''
                })),

                addEntry() {
                    this.entries.push({ address: '', detail: '' });
                },

                removeEntry(index) {
                    if (this.entries.length > 1) {
                        this.entries.splice(index, 1);
                    }
                }
            }
        }
    </script>
</x-app-layout>