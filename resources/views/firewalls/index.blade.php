<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Firewalls') }}
            </h2>
            <div class="mt-1 flex flex-col sm:flex-row sm:items-center">
                <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center">
                     <a href="{{ route('dashboard') }}" class="font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors duration-200">
                        {{ __('Dashboard') }}
                     </a>
                     <span class="mx-2 text-gray-300 dark:text-gray-600">/</span>
                     <span class="font-medium">
                        {{ __('Firewalls') }}
                     </span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-500 text-white p-4 rounded-lg mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('bulk_results'))
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4" role="alert">
                    <p class="font-bold">Bulk Action Results:</p>
                    <ul class="list-disc pl-5">
                        @foreach(session('bulk_results') as $result)
                            <li>{{ $result }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Widgets Grid -->

            <div x-data="firewallStats({{ $firewalls->pluck('id') }})" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Total Firewalls -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-500 dark:text-blue-300 mr-4">
                        <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Firewalls</p>
                        <p class="text-4xl font-bold text-gray-900 dark:text-gray-100" x-text="totalCount">{{ $totalFirewalls }}</p>
                    </div>
                </div>

                <!-- Offline Firewalls -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center">
                    <div class="p-3 rounded-full bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300 mr-4">
                        <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Offline Firewalls</p>
                        <template x-if="!hasData">
                            <div class="h-8 w-12 bg-gray-200 dark:bg-gray-700 rounded animate-pulse mt-1"></div>
                        </template>
                        <template x-if="hasData">
                            <p class="text-4xl font-bold text-gray-900 dark:text-gray-100" x-text="offlineCount"></p>
                        </template>
                    </div>
                </div>

                <!-- System Updates -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center">
                    <div class="p-3 rounded-full bg-orange-100 dark:bg-orange-900 text-orange-600 dark:text-orange-300 mr-4">
                        <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">System Upgrades</p>
                        <template x-if="!hasData">
                            <div class="h-8 w-12 bg-gray-200 dark:bg-gray-700 rounded animate-pulse mt-1"></div>
                        </template>
                        <template x-if="hasData">
                            <p class="text-4xl font-bold text-gray-900 dark:text-gray-100" x-text="sysCount"></p>
                        </template>
                    </div>
                </div>

                <!-- REST API Updates -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-300 mr-4">
                        <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">API Updates</p>
                        <template x-if="!hasData">
                            <div class="h-8 w-12 bg-gray-200 dark:bg-gray-700 rounded animate-pulse mt-1"></div>
                        </template>
                        <template x-if="hasData">
                            <p class="text-4xl font-bold text-gray-900 dark:text-gray-100" x-text="apiCount"></p>
                        </template>
                    </div>
                </div>
            </div>

            @php
                 $uniqueCustomers = $firewalls->pluck('company.name')->unique()->sort()->values();
            @endphp
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg" x-data="{
                // Spread in the filterable mixin
                ...window.filterableMixin({{ $firewalls->map(fn($f) => ['id' => $f->id, 'searchData' => strtolower($f->name . ' ' . $f->company->name . ' ' . $f->url . ' ' . $f->hostname), 'companyName' => $f->company->name])->values()->toJson() }}, 'firewall-updated'),
                
                // Page-specific properties
                deleteModalOpen: false,
                deleteAction: '',
                firewallName: '',
                confirmEmail: '',
                customers: {{ json_encode($uniqueCustomers) }},
                
                init() {
                    // Initialize filterable functionality
                    this.initFilterable();
                },
                
                openDeleteModal(action, name) {
                    this.deleteAction = action;
                    this.firewallName = name;
                    this.confirmEmail = '';
                    $dispatch('open-modal', 'delete-firewall-modal');
                },

                get filteredCustomers() {
                    if (this.search === '') return this.customers;
                    return this.customers.filter(c => c.toLowerCase().includes(this.search.toLowerCase()));
                }
            }">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-col space-y-4 mb-6">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Managed Firewalls') }}
                            </h3>
                            <a href="{{ route('firewalls.create') }}"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-medium text-sm text-white hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                {{ __('Add Firewall') }}
                            </a>
                        </div>

                        <!-- Toolbar -->
                        <div class="flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg mb-6">
                            
                            <!-- Left Side: Filters & Search (Flex Grow) -->
                            <div class="flex flex-col sm:flex-row gap-4 w-full lg:flex-1">
                                <!-- Search (Expandable) -->
                                <div class="relative w-full sm:w-64 lg:w-auto lg:flex-1">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input 
                                        type="text" 
                                        x-model="search"
                                        placeholder="Search firewalls..." 
                                        class="pl-10 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                    >
                                    <button 
                                        x-show="search.length > 0" 
                                        @click="search = ''" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none"
                                        style="display: none;"
                                    >
                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Filters Group -->
                                <div class="flex gap-2 w-full sm:w-auto shrink-0">
                                    <!-- Customer Filter -->
                                    <div class="relative w-1/2 sm:w-auto min-w-[160px]" x-data="{ open: false, filter: '' }">
                                        <button @click="open = !open" type="button" 
                                            class="flex items-center justify-between w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                            <span x-text="customerFilter === 'all' ? 'All Customers' : customerFilter" class="truncate block text-left"></span>
                                            <svg class="h-4 w-4 ml-2 text-gray-500 transform transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                        <div x-show="open" @click.outside="open = false" x-transition class="absolute z-10 mt-1 w-full sm:w-[200px] bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-xl max-h-60 overflow-y-auto">
                                            <div class="p-2 border-b dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800">
                                                <input x-model="filter" type="text" placeholder="Search..." class="w-full text-xs rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                                            </div>
                                            <div @click="customerFilter = 'all'; open = false" class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer text-sm">All Customers</div>
                                            <template x-for="c in customers.filter(x => x.toLowerCase().includes(filter.toLowerCase()))" :key="c">
                                                <div @click="customerFilter = c; open = false" class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer text-sm truncate" x-text="c"></div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Status Filter -->
                                    <select x-model="statusFilter" class="w-1/2 sm:w-auto rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="all">All Status</option>
                                        <option value="online">Online</option>
                                        <option value="offline">Offline</option>
                                    </select>
                                    
                                    <span class="text-xs text-gray-500 font-normal self-center whitespace-nowrap sm:ml-2 hidden sm:block" x-text="'Showing ' + filteredCount + ' of ' + items.length + ' firewalls'"></span>
                                </div>
                            </div>

                            <!-- Right Side: Bulk Actions -->
                            <div class="flex gap-2 w-full lg:w-auto items-center border-t lg:border-t-0 lg:border-l lg:pl-4 pt-4 lg:pt-0 border-gray-200 dark:border-gray-600">
                                <span class="text-sm text-gray-500 whitespace-nowrap hidden xl:inline">With selected:</span>
                                <div class="flex gap-2 w-full">
                                    <select id="bulkActionSelect" name="action" form="bulkForm"
                                        class="block w-full lg:w-48 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 sm:text-sm">
                                        <option value="">Bulk Actions...</option>
                                        <optgroup label="System">
                                            <option value="reboot">Reboot</option>
                                            <option value="update">System Update</option>
                                            <option value="update_rest_api">Update REST API</option>
                                            <option value="create_package">Install Package</option>
                                        </optgroup>
                                        <optgroup label="Configuration (Add to All)">
                                            <option value="create_alias">Add Alias</option>
                                            <option value="create_nat">Add NAT 1:1 / Port Forward</option>
                                            <option value="create_rule">Add Firewall Rule</option>
                                            <option value="create_ipsec">Add IPSec Tunnel</option>
                                        </optgroup>
                                    </select>
                                    <x-secondary-button type="button" onclick="submitBulkAction()" class="rounded-lg">
                                        Apply
                                    </x-secondary-button>
                                </div>
                            </div>
                        </div>

                        <form id="bulkForm" action="{{ route('firewalls.bulk.action') }}" method="POST">
                            @csrf
                            {{-- Hidden form, inputs will reference it by ID --}}
                        </form>

                    <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left">
                                    <input type="checkbox" id="selectAll" onclick="toggleSelectAll()">
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Name
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Host
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    System
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    API
                                </th>
                                @if(auth()->user()->isGlobalAdmin())
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Company
                                    </th>
                                @endif
                                <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($firewalls as $firewall)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition" 
                                    x-data="firewallRow(
                                        {{ $firewall->id }}, 
                                        null, 
                                        '{{ route('firewall.check-status', $firewall) }}',
                                        '{{ addslashes(strtolower($firewall->name . ' ' . $firewall->company->name . ' ' . $firewall->url . ' ' . $firewall->hostname)) }}',
                                        '{{ addslashes($firewall->company->name) }}'
                                    )"
                                    x-show="checkVisibility(search, statusFilter, customerFilter)"
                                    style="">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" name="firewall_ids[]" value="{{ $firewall->id }}"
                                            form="bulkForm" class="firewall-checkbox">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $firewall->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <!-- Skeleton placeholder while loading -->
                                        <div x-show="loading" class="h-5 w-16 bg-gray-200 dark:bg-gray-700 rounded-full animate-pulse"></div>
                                        
                                        <!-- Status badge when loaded -->
                                        <span x-show="!loading" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                              :class="{
                                                  'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': isOnline,
                                                  'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': !isOnline
                                              }">
                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2" 
                                                 :class="isOnline ? 'text-green-400' : 'text-red-400'" 
                                                 fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            <span x-text="isOnline ? 'Online' : 'Offline'"></span>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <a href="{{ \Illuminate\Support\Str::startsWith($firewall->url, ['http://', 'https://']) ? $firewall->url : 'https://' . $firewall->url }}" 
                                           target="_blank" 
                                           rel="noopener noreferrer"
                                           class="text-indigo-600 hover:text-indigo-900 hover:underline dark:text-indigo-400 dark:hover:text-indigo-300">
                                            {{ preg_replace('#^https?://#', '', $firewall->url) }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <template x-if="loading">
                                            <div class="h-4 w-24 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                        </template>
                                        <template x-if="!loading">
                                            <div class="flex items-center space-x-2">
                                                <span x-text="sysVersion"></span>
                                                <template x-if="sysUpdateAvailable">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200" title="Update Available">
                                                        Update
                                                    </span>
                                                </template>
                                                <template x-if="!sysUpdateAvailable && sysVersion !== '-'">
                                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" title="Up to Date">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </template>
                                            </div>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <template x-if="loading">
                                            <div class="h-4 w-12 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                        </template>
                                        <template x-if="!loading">
                                            <div class="flex items-center space-x-2">
                                                <span x-text="apiVersion"></span>
                                                <template x-if="apiUpdateAvailable">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200" title="Update Available">
                                                        Update
                                                    </span>
                                                </template>
                                                <template x-if="!apiUpdateAvailable && apiVersion !== '-' && apiVersion !== 'N/A'">
                                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" title="Up to Date">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </template>
                                            </div>
                                        </template>
                                    </td>
                                    @if(auth()->user()->isGlobalAdmin())
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <a href="{{ route('companies.show', $firewall->company) }}" class="text-indigo-600 hover:text-indigo-900 hover:underline dark:text-indigo-400 dark:hover:text-indigo-300">
                                                {{ $firewall->company->name }}
                                            </a>
                                        </td>
                                    @endif
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-3">
                                            <a href="{{ route('firewall.dashboard', $firewall) }}"
                                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                Manage
                                            </a>
                                            <a href="{{ route('firewalls.edit', $firewall) }}"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                Edit
                                            </a>
                                            <button type="button"
                                                @click="openDeleteModal('{{ route('firewalls.destroy', $firewall) }}', '{{ addslashes($firewall->name) }}')"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->isGlobalAdmin() ? '8' : '7' }}" class="px-6 py-4 text-center text-gray-500">
                                        No firewalls found. <a href="{{ route('firewalls.create') }}"
                                            class="text-blue-600 hover:underline">Add one now</a>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <x-modal name="delete-firewall-modal" :show="false" focusable>
                        <div class="p-6">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Delete Firewall') }}
                            </h2>
            
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Are you sure you want to delete the firewall ') }} <span class="font-bold" x-text="firewallName"></span>? {{ __('This action cannot be undone.') }}
                            </p>
                            <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Please type your email address to confirm:') }} <span class="font-mono font-bold">{{ auth()->user()->email }}</span>
                            </p>
            
                            <form :action="deleteAction" method="POST" id="delete-firewall-form">
                                @csrf
                                @method('DELETE')
                                <div class="mt-6">
                                    <x-input-label for="confirm_email" value="{{ __('Email Address') }}" class="sr-only" />
            
                                    <x-text-input
                                        id="confirm_email"
                                        name="confirm_email"
                                        type="email"
                                        class="mt-1 block w-3/4"
                                        placeholder="{{ __('Email Address') }}"
                                        x-model="confirmEmail"
                                        @keyup.enter="if(confirmEmail === '{{ auth()->user()->email }}') document.getElementById('delete-firewall-form').submit()"
                                    />
                                </div>
            
                                <div class="mt-6 flex justify-end">
                                    <x-secondary-button @click="$dispatch('close')">
                                        {{ __('Cancel') }}
                                    </x-secondary-button>
            
                                    <x-danger-button class="ml-3"
                                        x-bind:disabled="confirmEmail !== '{{ auth()->user()->email }}'"
                                        x-bind:class="{ 'opacity-50 cursor-not-allowed': confirmEmail !== '{{ auth()->user()->email }}' }"
                                        @click="document.getElementById('delete-firewall-form').submit()">
                                        {{ __('Delete Firewall') }}
                                    </x-danger-button>
                                </div>
                            </form>
                        </div>
                    </x-modal>

                    {{-- Re-implement Delete Buttons properly since they were inside the form --}}
                    {{-- Actually, delete forms need to be outside. --}}
                    {{-- I will fix the wrapping in the next step or adjust now. --}}
                    {{-- Strategy: Use JS for Bulk submit, don't wrap table in form. --}}

                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('firewallStats', (allIds) => ({
                stats: {},
                loading: true,
                search: '',
                statusFilter: 'all',
                customerFilter: 'all',
                get totalCount() { return allIds.length; },
                get offlineCount() { return Object.values(this.stats).filter(s => !s.online).length; },
                get sysCount() { return Object.values(this.stats).filter(s => s.sys).length; },
                get apiCount() { return Object.values(this.stats).filter(s => s.api).length; },
                get hasData() { return Object.keys(this.stats).length > 0; },
                
                init() {
                    // Smart Trigger: Wait for WS connection to avoid "Simultaneous Batch" (Sync Mode) 
                    // and ensure we don't miss events (Race Condition).
                    const checkAndTrigger = () => {
                        if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
                            const state = window.Echo.connector.pusher.connection.state;
                            
                            // If connected, go!
                            if (state === 'connected') {
                                this.triggerUpdate();
                                return;
                            }
                            
                            // If connecting, wait for it...
                            if (state === 'connecting' || state === 'initialized') {
                                const onConnect = () => {
                                    this.triggerUpdate();
                                    window.Echo.connector.pusher.connection.unbind('connected', onConnect);
                                };
                                window.Echo.connector.pusher.connection.bind('connected', onConnect);
                                // Fallback if connection takes too long
                                setTimeout(() => { 
                                    if (this.loading) this.triggerUpdate(); // Will force sync if still not connected
                                }, 3000); 
                                return;
                            }
                        }
                        
                        // If disconnected or no Echo, just trigger (will use Sync Fallback)
                        this.triggerUpdate();
                    };

                    checkAndTrigger();
                    
                    // 2. Poll every X seconds to keep data fresh via Queue Workers
                    const intervalMs = {{ ($settings['status_check_interval'] ?? 30) * 1000 }};
                    setInterval(() => { this.triggerUpdate(); }, intervalMs);

                    // 3. Listen for WebSocket updates from the jobs
                    window.addEventListener('firewall-updated', (e) => {
                        const { id, online, sys, api } = e.detail;
                        this.stats[id] = { online, sys, api };
                        if (Object.keys(this.stats).length >= this.totalCount) {
                            this.loading = false;
                        }
                    });
                    
                    // Safety fallback
                    setTimeout(() => { this.loading = false; }, 10000);
                },

                async triggerUpdate() {
                    const tokenEl = document.querySelector('meta[name="csrf-token"]');
                    const token = tokenEl ? tokenEl.getAttribute('content') : '';
                    
                    // Intelligent Failover:
                    // If WebSocket is not connected, we force a synchronous check so the UI updates via HTTP response
                    let url = '{{ route("firewalls.refresh-all") }}';
                    let isWsConnected = false;
                    
                    if (window.Echo && window.Echo.connector && window.Echo.connector.pusher && window.Echo.connector.pusher.connection) {
                        isWsConnected = (window.Echo.connector.pusher.connection.state === 'connected');
                    }
                    
                    if (!isWsConnected) {
                        console.warn('WebSocket disconnected. Forcing synchronous update.');
                        url += '?sync=true';
                    }

                    try {
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify({ ids: allIds })
                        });
                        const data = await res.json();
                        
                        // If we get immediate results (Sync Mode), use them to update UI
                        if (data.results) {
                             Object.entries(data.results).forEach(([id, r]) => {
                                 // r is the Wrapper: { online: bool, data: ServiceResult, api_version: ... }
                                 let serviceResult = r.data || {};
                                 let realData = (serviceResult.data && typeof serviceResult.data === 'object') ? serviceResult.data : serviceResult;
                                 
                                 const sysUp = (realData.update_available === true);
                                 const apiUp = (realData.api_update_available === true);
                                 
                                 window.dispatchEvent(new CustomEvent('firewall-updated', {
                                         detail: { 
                                             id: parseInt(id), 
                                             online: r.online, 
                                             sys: sysUp, 
                                             api: apiUp, 
                                             data: serviceResult,
                                             apiVersion: r.api_version || realData.api_version || '-'
                                         }
                                 }));
                             });
                        }
                    } catch (err) {
                        console.error('Update trigger failed:', err);
                    }
                }
            }));

            Alpine.data('firewallRow', (id, statusData, checkPath, searchData, companyName) => ({
                id: id,
                // statusData is the Wrapper { online: bool, data: ServiceResult }
                isOnline: statusData && statusData.online,
                
                // Helper to extract nested version
                sysVersion: (function() {
                     if (!statusData || !statusData.data || !statusData.data.data) return '-';
                     return statusData.data.data.product_version || statusData.data.data.version || '-';
                })(),
                
                apiVersion: (statusData && statusData.api_version) || 
                            (statusData && statusData.data && statusData.data.api_version) || 
                            '-',
                            
                sysUpdateAvailable: (statusData && statusData.data && statusData.data.data && statusData.data.data.update_available === true),
                apiUpdateAvailable: (statusData && statusData.data && statusData.data.data && statusData.data.data.api_update_available === true),
                
                // Only show loading skeleton if we have NO data at all
                loading: !statusData, 
                searchData: searchData,
                companyName: companyName,

                checkVisibility(search, statusFilter, customerFilter) {
                   search = (search || '').toLowerCase();
                   const matchesSearch = search === '' || this.searchData.includes(search);
                   
                   const matchesStatus = statusFilter === 'all' 
                        || (statusFilter === 'online' && this.isOnline)
                         || (statusFilter === 'offline' && !this.isOnline);

                   const matchesCustomer = customerFilter === 'all' || this.companyName === customerFilter;

                   return matchesSearch && matchesStatus && matchesCustomer;
                },

                init() {
                    // 1. Universal Update Listener (Handles both HTTP/Sync and internal events)
                    // This allows the row to update when the parent 'triggerUpdate' fetches data via HTTP
                    window.addEventListener('firewall-updated', (e) => {
                        if (e.detail.id === this.id) {
                            this.isOnline = e.detail.online;
                            this.loading = false;
                            
                            // Extract data for versions
                            let serviceData = e.detail.data || {};
                            let realData = (serviceData.data && typeof serviceData.data === 'object') ? serviceData.data : serviceData;

                            this.sysVersion = realData.product_version || realData.version || '-';
                            this.apiVersion = e.detail.apiVersion || '-';
                            
                            this.sysUpdateAvailable = e.detail.sys;
                            this.apiUpdateAvailable = e.detail.api;
                        }
                    });

                    // 2. WebSocket Listener
                    if (window.Echo) {
                        window.Echo.private('firewall.' + this.id)
                            .listen('.firewall.status.update', (e) => {
                                // e.status is the Wrapper
                                this.isOnline = (e.status && e.status.online !== undefined) ? e.status.online : true; 
                                this.loading = false;
                                
                                let s = e.status || {};
                                let d = s.data || {};
                                let rd = (d.data && typeof d.data === 'object') ? d.data : d;
                                
                                this.sysVersion = rd.product_version || rd.version || '-';
                                this.apiVersion = s.api_version || rd.api_version || '-';
                                this.sysUpdateAvailable = (rd.update_available === true);
                                this.apiUpdateAvailable = (rd.api_update_available === true);

                                // 3. Dispatch to Parent (firewallStats)
                                window.dispatchEvent(new CustomEvent('firewall-updated', {
                                    detail: {
                                        id: this.id,
                                        online: this.isOnline,
                                        sys: this.sysUpdateAvailable,
                                        api: this.apiUpdateAvailable,
                                        data: s,
                                        apiVersion: this.apiVersion
                                    }
                                }));
                            });
                    }

                    // 4. Fallback Watchdog
                    setTimeout(() => {
                         if (this.loading) {
                             // console.warn('Firewall ' + this.id + ' stuck loading.');
                         }
                    }, 15000);
                },

                checkStatus(url) {
                    // Manual refresh fallback
                    
                    fetch(url + '?t=' + Date.now())
                        .then(res => res.json())
                        .then(data => {
                            this.isOnline = data.online;
                            // .. logic here is similar but likely unused if we rely on triggerUpdate()
                            // keeping minimal fallback
                            this.loading = false; 
                        })
                        .catch(() => { this.loading = false; });
                }
            }));
        });

        function toggleSelectAll() {
            const checkboxes = document.querySelectorAll('.firewall-checkbox');
            const selectAll = document.getElementById('selectAll');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        }

        function submitBulkAction() {
            const action = document.getElementById('bulkActionSelect').value;
            if (!action) {
                alert('Please select an action.');
                return;
            }

            const checkboxes = document.querySelectorAll('.firewall-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.value);

            if (ids.length === 0) {
                alert('Please select at least one firewall.');
                return;
            }

            if (action.startsWith('create_')) {
                // Redirect to create page
                const type = action.replace('create_', '');
                const url = `{{ url('/firewalls/bulk/create') }}/${type}`;
                // Append IDs
                const queryString = ids.map(id => `firewall_ids[]=${id}`).join('&');
                window.location.href = `${url}?${queryString}`;
            } else {
                // POST action (reboot/update)
                if (action === 'reboot') {
                    if (!confirm('WARNING: Are you sure you want to REBOOT the selected firewalls? usage of this command will cause network downtime.')) {
                        return;
                    }
                } else if (!confirm('Are you sure you want to perform this action on selected firewalls?')) {
                    return;
                }
                const form = document.getElementById('bulkForm');
                form.submit();
            }
        }
    </script>
</x-app-layout>
