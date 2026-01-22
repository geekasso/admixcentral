<x-app-layout>
    <x-slot name="header">
        <x-firewall-header title="{{ __('System: Advanced') }}" :firewall="$firewall" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200" x-data="{
                    showModal: false,
                    editing: false,
                    form: { id: '', tunable: '', value: '', descr: '' },
                    deleteTunable(id) {
                            let form = document.getElementById('delete-tunable-form');
                            form.action = " {{ route('system.advanced.tunables.destroy', ['firewall' => $firewall, 'id' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', id); form.submit(); } } }"
                @open-tunable-modal.window="showModal = true; editing = false; form = { id: '', tunable: '', value: '', descr: '' }">

                    <!-- Tabs -->
                    <div class="mb-6 border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            @foreach(['admin' => 'Admin Access', 'firewall' => 'Firewall & NAT', 'networking' => 'Networking', 'miscellaneous' => 'Miscellaneous', 'tunables' => 'System Tunables', 'notifications' => 'Notifications'] as $key => $label)
                                <a href="{{ route('system.advanced', ['firewall' => $firewall, 'tab' => $key]) }}"
                                    class="{{ $tab === $key ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </nav>
                    </div>

                    <!-- Form -->
                    <form method="POST" action="{{ route('system.advanced.update', ['firewall' => $firewall]) }}">
                        @csrf
                        <!-- Add tab as hidden input -->
                        <input type="hidden" name="tab" value="{{ $tab }}">

                        @if($tab === 'admin')
                            <!-- Admin Access Tab -->
                            <h3 class="text-lg font-medium text-gray-900 mb-4">webConfigurator</h3>
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <x-input-label for="protocol" :value="__('Protocol')" />
                                    <select id="protocol" name="protocol"
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="http" {{ ($data['webgui']['protocol'] ?? '') === 'http' ? 'selected' : '' }}>HTTP</option>
                                        <option value="https" {{ ($data['webgui']['protocol'] ?? '') === 'https' ? 'selected' : '' }}>HTTPS</option>
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="port" :value="__('TCP Port')" />
                                    <x-text-input id="port" class="block mt-1 w-full" type="number" name="port"
                                        :value="$data['webgui']['port'] ?? ''" />
                                </div>
                            </div>

                            <h3 class="text-lg font-medium text-gray-900 mt-8 mb-4">Secure Shell (SSH)</h3>
                            <div class="grid grid-cols-1 gap-6">
                                <div class="flex items-center">
                                    <input id="ssh_enable" name="ssh_enable" type="checkbox"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ ($data['ssh']['enable'] ?? '') ? 'checked' : '' }}>
                                    <label for="ssh_enable"
                                        class="ml-2 block text-sm text-gray-900">{{ __('Enable Secure Shell') }}</label>
                                </div>
                                <div>
                                    <x-input-label for="ssh_port" :value="__('SSH Port')" />
                                    <x-text-input id="ssh_port" class="block mt-1 w-full" type="number" name="ssh_port"
                                        :value="$data['ssh']['port'] ?? ''" />
                                </div>
                            </div>

                            <h3 class="text-lg font-medium text-gray-900 mt-8 mb-4">Console Options</h3>
                            <div class="grid grid-cols-1 gap-6">
                                <div class="flex items-center">
                                    <input id="passwd_protect_console" name="passwd_protect_console" type="checkbox"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ ($data['console']['passwd_protect_console'] ?? '') ? 'checked' : '' }}>
                                    <label for="passwd_protect_console"
                                        class="ml-2 block text-sm text-gray-900">{{ __('Password protect the console menu') }}</label>
                                </div>
                            </div>

                        @elseif($tab === 'firewall')
                            <!-- Firewall & NAT Tab -->
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Firewall Advanced</h3>
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <x-input-label for="aliasesresolveinterval" :value="__('Aliases Resolve Interval')" />
                                    <x-text-input id="aliasesresolveinterval" class="block mt-1 w-full" type="number"
                                        name="aliasesresolveinterval" :value="$data['firewall']['aliasesresolveinterval'] ?? ''" />
                                    <p class="mt-1 text-sm text-gray-500">Interval, in seconds, that will be used to resolve
                                        hostnames configured on aliases.</p>
                                </div>
                                <div class="flex items-center">
                                    <input id="checkaliasesurlcert" name="checkaliasesurlcert" type="checkbox"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ ($data['firewall']['checkaliasesurlcert'] ?? '') ? 'checked' : '' }}>
                                    <label for="checkaliasesurlcert"
                                        class="ml-2 block text-sm text-gray-900">{{ __('Check Certificate of Aliases URLs') }}</label>
                                </div>
                            </div>

                        @elseif($tab === 'networking' || $tab === 'miscellaneous')
                            <!-- Networking & Miscellaneous Tabs -->
                            @php
                                $featureName = $tab === 'networking' ? 'Networking' : 'Miscellaneous';
                                $suffix = $tab === 'networking' ? 'system_advanced_network.php' : 'system_advanced_misc.php';
                            @endphp
                            <x-api-not-supported :firewall="$firewall" :urlSuffix="$suffix" :featureName="$featureName" />

                        @elseif($tab === 'notifications')
                            <!-- Notifications Tab -->
                            <h3 class="text-lg font-medium text-gray-900 mb-4">E-Mail</h3>
                            <div class="grid grid-cols-1 gap-6">
                                <div class="flex items-center">
                                    <input id="disable" name="disable" type="checkbox"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ ($data['notifications']['disable'] ?? '') ? 'checked' : '' }}>
                                    <label for="disable"
                                        class="ml-2 block text-sm text-gray-900">{{ __('Disable SMTP Notifications') }}</label>
                                </div>
                                <div>
                                    <x-input-label for="ipaddress" :value="__('E-Mail Server')" />
                                    <x-text-input id="ipaddress" class="block mt-1 w-full" type="text" name="ipaddress"
                                        :value="$data['notifications']['ipaddress'] ?? ''" />
                                </div>
                                <div>
                                    <x-input-label for="port" :value="__('SMTP Port')" />
                                    <x-text-input id="port" class="block mt-1 w-full" type="number" name="port"
                                        :value="$data['notifications']['port'] ?? ''" />
                                </div>
                                <div>
                                    <x-input-label for="timeout" :value="__('Connection Timeout')" />
                                    <x-text-input id="timeout" class="block mt-1 w-full" type="number" name="timeout"
                                        :value="$data['notifications']['timeout'] ?? ''" />
                                </div>
                                <div class="flex items-center">
                                    <input id="ssl" name="ssl" type="checkbox"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ ($data['notifications']['ssl'] ?? '') ? 'checked' : '' }}>
                                    <label for="ssl"
                                        class="ml-2 block text-sm text-gray-900">{{ __('Enable SSL/TLS') }}</label>
                                </div>
                                <div class="flex items-center">
                                    <input id="sslvalidate" name="sslvalidate" type="checkbox"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ ($data['notifications']['sslvalidate'] ?? '') ? 'checked' : '' }}>
                                    <label for="sslvalidate"
                                        class="ml-2 block text-sm text-gray-900">{{ __('Validate SSL/TLS Certificate') }}</label>
                                </div>
                                <div>
                                    <x-input-label for="fromaddress" :value="__('From E-Mail Address')" />
                                    <x-text-input id="fromaddress" class="block mt-1 w-full" type="email" name="fromaddress"
                                        :value="$data['notifications']['fromaddress'] ?? ''" />
                                </div>
                                <div>
                                    <x-input-label for="notifyemailaddress" :value="__('Notification E-Mail Address')" />
                                    <x-text-input id="notifyemailaddress" class="block mt-1 w-full" type="email"
                                        name="notifyemailaddress" :value="$data['notifications']['notifyemailaddress'] ?? ''" />
                                </div>
                            </div>

                        @elseif($tab === 'tunables')
                            <!-- System Tunables Tab -->
                            <div>
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">System Tunables</h3>
                                    <x-button-add @click="showModal = true; editing = false; form = { id: '', tunable: '', value: '', descr: '' }">
                                        {{ __('Add Tunable') }}
                                    </x-button-add>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Tunable Name</th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Description</th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Value</th>
                                                <th scope="col" class="relative px-6 py-3">
                                                    <span class="sr-only">Actions</span>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse($data['tunables'] as $tunable)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        {{ $tunable['tunable'] ?? 'N/A' }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $tunable['descr'] ?? '' }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $tunable['value'] ?? '' }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <button type="button"
                                                            @click="showModal = true; editing = true; form = { id: '{{ $tunable['id'] ?? $tunable['tunable'] ?? '' }}', tunable: '{{ $tunable['tunable'] ?? '' }}', value: '{{ $tunable['value'] ?? '' }}', descr: '{{ $tunable['descr'] ?? '' }}' }"
                                                            class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</button>
                                                        <button type="button"
                                                            @click="deleteTunable('{{ $tunable['id'] ?? $tunable['tunable'] ?? '' }}')"
                                                            class="text-red-600 hover:text-red-900">Delete</button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4"
                                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No
                                                        tunables found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        @if($tab !== 'tunables')
                            <div class="flex items-center justify-end mt-4">
                                <x-primary-button class="ml-4">
                                    {{ __('Save') }}
                                </x-primary-button>
                            </div>
                        @endif
                    </form>

                    <!-- Modal -->
                    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                        <div
                            class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div x-show="showModal" class="fixed inset-0 transition-opacity" aria-hidden="true">
                                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                            </div>

                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                                aria-hidden="true">&#8203;</span>

                            <div x-show="showModal"
                                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                <form
                                    :action="editing ? '{{ route('system.advanced.tunables.update', ['firewall' => $firewall, 'id' => 'PLACEHOLDER']) }}'.replace('PLACEHOLDER', form.id) : '{{ route('system.advanced.tunables.store', ['firewall' => $firewall]) }}'"
                                    method="POST">
                                    @csrf
                                    <template x-if="editing">
                                        <input type="hidden" name="_method" value="PATCH">
                                    </template>

                                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                        <div class="sm:flex sm:items-start">
                                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                <h3 class="text-lg leading-6 font-medium text-gray-900"
                                                    x-text="editing ? 'Edit Tunable' : 'Add Tunable'"></h3>
                                                <div class="mt-4 space-y-4">
                                                    <div>
                                                        <x-input-label for="tunable" :value="__('Tunable Name')" />
                                                        <x-text-input id="tunable" class="block mt-1 w-full" type="text"
                                                            name="tunable" x-model="form.tunable" required />
                                                    </div>
                                                    <div>
                                                        <x-input-label for="descr" :value="__('Description')" />
                                                        <x-text-input id="descr" class="block mt-1 w-full" type="text"
                                                            name="descr" x-model="form.descr" />
                                                    </div>
                                                    <div>
                                                        <x-input-label for="value" :value="__('Value')" />
                                                        <x-text-input id="value" class="block mt-1 w-full" type="text"
                                                            name="value" x-model="form.value" required />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                        <button type="submit"
                                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                            Save
                                        </button>
                                        <button type="button" @click="showModal = false"
                                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Delete Form -->
                    <form id="delete-tunable-form" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>


            </div>
        </div>
    </div>
    </div>
</x-app-layout>
