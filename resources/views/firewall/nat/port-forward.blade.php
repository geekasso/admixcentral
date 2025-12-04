<x-app-layout :firewall="$firewall">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Firewall NAT: Port Forward') }} - {{ $firewall->name }}
            </h2>
            <div x-data>
                <button @click="$dispatch('open-create-modal')" class="pf-btn pf-btn-primary">
                    <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Rule
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ 
        showModal: false, 
        showDeleteModal: false,
        deleteId: null,
        isEdit: false, 
        form: {
            id: '',
            interface: '{{ $interfaces[0]['if'] ?? 'wan' }}',
            protocol: 'tcp',
            src: '',
            srcport: '',
            dst: '',
            dstport: '',
            target: '',
            local_port: '',
            descr: '',
            disabled: false,
            natreflection: 'enable',
            associated_rule_id: 'pass'
        },
        resetForm() {
            this.form = {
                id: '',
                interface: '{{ $interfaces[0]['if'] ?? 'wan' }}',
                protocol: 'tcp',
                src: '',
                srcport: '',
                dst: '',
                dstport: '',
                target: '',
                local_port: '',
                descr: '',
                disabled: false,
                natreflection: 'enable',
                associated_rule_id: 'pass'
            };
            this.isEdit = false;
        },
        editRule(rule, index) {
            this.isEdit = true;
            this.form.id = index; // Use index as ID for now since API uses array index
            this.form.interface = rule.interface || 'wan';
            this.form.protocol = rule.protocol || 'tcp';
            
            // Handle source
            if (typeof rule.source === 'object') {
                this.form.src = rule.source.address || (rule.source.network ? 'network' : 'any');
                this.form.srcport = rule.source.port || '';
            } else {
                this.form.src = rule.source || 'any';
            }

            // Handle dest
            if (typeof rule.destination === 'object') {
                this.form.dst = rule.destination.address || (rule.destination.network ? 'network' : 'any');
                this.form.dstport = rule.destination.port || '';
            } else {
                this.form.dst = rule.destination || 'any';
            }

            this.form.target = rule.target || '';
            this.form.local_port = rule['local-port'] || '';
            this.form.descr = rule.descr || '';
            this.form.disabled = !!rule.disabled;
            this.form.natreflection = rule.natreflection || 'enable';
            this.form.associated_rule_id = rule['associated-rule-id'] || 'pass';
            
            this.showModal = true;
        },
        confirmDelete(index) {
            this.deleteId = index;
            this.showDeleteModal = true;
        }
    }" @open-create-modal.window="resetForm(); showModal = true">

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

                    {{-- Tabs --}}
                    @include('firewall.nat.tabs', ['active' => 'port-forward'])

                    <div class="pf-table-container">
                        <table class="pf-table">
                            <thead>
                                <tr>
                                    <th>Interface</th>
                                    <th>Protocol</th>
                                    <th>Source Address</th>
                                    <th>Source Ports</th>
                                    <th>Dest. Address</th>
                                    <th>Dest. Ports</th>
                                    <th>NAT IP</th>
                                    <th>NAT Ports</th>
                                    <th>Description</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rules as $index => $rule)
                                    <tr
                                        class="{{ isset($rule['disabled']) ? 'opacity-50 bg-gray-50 dark:bg-gray-700' : '' }}">
                                        <td data-label="Interface">
                                            {{ strtoupper($rule['interface'] ?? '') }}
                                        </td>
                                        <td data-label="Protocol">
                                            {{ strtoupper($rule['protocol'] ?? '') }}
                                        </td>
                                        <td data-label="Source Address">
                                            {{ is_array($rule['source']) ? ($rule['source']['any'] ? '*' : ($rule['source']['address'] ?? ($rule['source']['network'] ?? ''))) : $rule['source'] }}
                                        </td>
                                        <td data-label="Source Ports">
                                            {{ is_array($rule['source']) && isset($rule['source']['port']) ? $rule['source']['port'] : '*' }}
                                        </td>
                                        <td data-label="Dest. Address">
                                            {{ is_array($rule['destination']) ? ($rule['destination']['any'] ? '*' : ($rule['destination']['address'] ?? ($rule['destination']['network'] ?? ''))) : $rule['destination'] }}
                                        </td>
                                        <td data-label="Dest. Ports">
                                            {{ is_array($rule['destination']) && isset($rule['destination']['port']) ? $rule['destination']['port'] : '*' }}
                                        </td>
                                        <td data-label="NAT IP">
                                            {{ $rule['target'] ?? '' }}
                                        </td>
                                        <td data-label="NAT Ports">
                                            {{ $rule['local-port'] ?? '' }}
                                        </td>
                                        <td data-label="Description">
                                            {{ $rule['descr'] ?? '' }}
                                        </td>
                                        <td data-label="Actions" class="text-right">
                                            <button @click="editRule({{ json_encode($rule) }}, {{ $index }})"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 mr-2">Edit</button>
                                            <button @click="confirmDelete({{ $index }})"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400">Delete</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            No port forward rules found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Redundant Apply Changes button removed --}}
                </div>
            </div>
        </div>

        {{-- Modal --}}
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showModal" class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showModal"
                    class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <form
                        :action="isEdit ? '{{ url('firewall/' . $firewall->id . '/nat/port-forward') }}/' + form.id : '{{ route('firewall.nat.port-forward.store', $firewall) }}'"
                        method="POST">
                        @csrf
                        <template x-if="isEdit">
                            @method('PUT')
                        </template>

                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100"
                                x-text="isEdit ? 'Edit Port Forward Rule' : 'Add Port Forward Rule'"></h3>

                            <div class="mt-4 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                {{-- Disabled --}}
                                <div class="sm:col-span-6">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="disabled" name="disabled" type="checkbox" x-model="form.disabled"
                                                class="pf-checkbox">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="disabled"
                                                class="font-medium text-gray-700 dark:text-gray-300">Disable this
                                                rule</label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Interface --}}
                                <div class="sm:col-span-3">
                                    <label for="interface"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Interface</label>
                                    <select id="interface" name="interface" x-model="form.interface"
                                        class="pf-input mt-1 block w-full">
                                        @foreach($interfaces as $iface)
                                            <option value="{{ $iface['if'] ?? $iface['descr'] }}">
                                                {{ $iface['descr'] ?? strtoupper($iface['if']) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Protocol --}}
                                <div class="sm:col-span-3">
                                    <label for="protocol"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Protocol</label>
                                    <select id="protocol" name="protocol" x-model="form.protocol"
                                        class="pf-input mt-1 block w-full">
                                        <option value="tcp">TCP</option>
                                        <option value="udp">UDP</option>
                                        <option value="tcp/udp">TCP/UDP</option>
                                        <option value="icmp">ICMP</option>
                                        <option value="esp">ESP</option>
                                        <option value="ah">AH</option>
                                        <option value="gre">GRE</option>
                                        <option value="ipv6">IPv6</option>
                                        <option value="igmp">IGMP</option>
                                        <option value="pim">PIM</option>
                                        <option value="ospf">OSPF</option>
                                        <option value="sctp">SCTP</option>
                                        <option value="any">Any</option>
                                    </select>
                                </div>

                                {{-- Destination Port --}}
                                <div class="sm:col-span-3">
                                    <label for="dstport"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dest. Port
                                        Range</label>
                                    <input type="text" name="dstport" id="dstport" x-model="form.dstport"
                                        class="pf-input mt-1 block w-full" placeholder="80 or 80-90">
                                </div>

                                {{-- Target IP --}}
                                <div class="sm:col-span-3">
                                    <label for="target"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Redirect
                                        Target IP</label>
                                    <input type="text" name="target" id="target" x-model="form.target"
                                        class="pf-input mt-1 block w-full" placeholder="192.168.1.x">
                                </div>

                                {{-- Target Port --}}
                                <div class="sm:col-span-3">
                                    <label for="local_port"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Redirect
                                        Target Port</label>
                                    <input type="text" name="local_port" id="local_port" x-model="form.local_port"
                                        class="pf-input mt-1 block w-full" placeholder="80">
                                </div>

                                {{-- Description --}}
                                <div class="sm:col-span-6">
                                    <label for="descr"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                    <input type="text" name="descr" id="descr" x-model="form.descr"
                                        class="pf-input mt-1 block w-full">
                                </div>
                            </div>

                            {{-- Filter Rule Association --}}
                            <div class="mt-6 sm:col-span-6">
                                <label for="associated_rule_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Filter rule
                                    association</label>
                                <select id="associated_rule_id" name="associated_rule_id"
                                    x-model="form.associated_rule_id" class="pf-input mt-1 block w-full">
                                    <option value="pass">Add associated filter rule</option>
                                    <option value="block">Block</option>
                                    <option value="reject">Reject</option>
                                    <option value="none">None</option>
                                </select>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="pf-btn pf-btn-primary w-full sm:w-auto sm:ml-3">
                                Save
                            </button>
                            <button type="button" @click="showModal = false"
                                class="mt-3 w-full sm:mt-0 sm:ml-3 pf-btn pf-btn-secondary sm:w-auto">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Delete Modal --}}
        <div x-show="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showDeleteModal" class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showDeleteModal"
                    class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                                    Delete Rule
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Are you sure you want to delete this rule? This action cannot be undone.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <form :action="'{{ url('firewall/' . $firewall->id . '/nat/port-forward') }}/' + deleteId"
                            method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Delete
                            </button>
                        </form>
                        <button type="button" @click="showDeleteModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>