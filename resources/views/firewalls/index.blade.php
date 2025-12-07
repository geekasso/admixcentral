<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Firewalls') }}
            </h2>
            <a href="{{ route('firewalls.create') }}"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add Firewall
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form id="bulkForm" action="{{ route('firewalls.bulk.action') }}" method="POST">
                        @csrf
                        {{-- Hidden form, inputs will reference it by ID --}}
                    </form>

                    <div class="mb-4 flex gap-4 items-end">
                        <div class="w-1/3">
                            <label for="bulkStatus"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bulk
                                Actions</label>
                            <select id="bulkActionSelect" name="action" form="bulkForm"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                                <option value="">Select Action...</option>
                                <optgroup label="System">
                                    <option value="reboot">Reboot Selected</option>
                                    <option value="update">System Update (pfSense-upgrade)</option>
                                    <option value="update_rest_api">Update REST API (New)</option>
                                </optgroup>
                                <optgroup label="Configuration (Add to All)">
                                    <option value="create_alias">Add Alias</option>
                                    <option value="create_nat">Add NAT 1:1 / Port Forward</option>
                                    <option value="create_rule">Add Firewall Rule</option>
                                    <option value="create_ipsec">Add IPSec Tunnel</option>
                                </optgroup>
                            </select>
                        </div>
                        <button type="button" onclick="submitBulkAction()"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Apply
                        </button>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left">
                                    <input type="checkbox" id="selectAll" onclick="toggleSelectAll()">
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Name
                                </th>
                                @if(auth()->user()->isGlobalAdmin())
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Company
                                    </th>
                                @endif
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    URL
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($firewalls as $firewall)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" name="firewall_ids[]" value="{{ $firewall->id }}"
                                            form="bulkForm" class="firewall-checkbox">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $firewall->name }}
                                    </td>
                                    @if(auth()->user()->isGlobalAdmin())
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $firewall->company->name }}
                                        </td>
                                    @endif
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $firewall->url }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('firewall.dashboard', $firewall) }}"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
                                            Manage
                                        </a>
                                        <a href="{{ route('firewalls.edit', $firewall) }}"
                                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                                            Edit
                                        </a>
                                        {{-- Delete Form is SAFE here because it's not nested inside bulkForm anymore --}}
                                        <form action="{{ route('firewalls.destroy', $firewall) }}" method="POST"
                                            class="inline"
                                            onsubmit="return confirm('Are you sure you want to delete this firewall?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No firewalls found. <a href="{{ route('firewalls.create') }}"
                                            class="text-blue-600 hover:underline">Add one now</a>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Re-implement Delete Buttons properly since they were inside the form --}}
                    {{-- Actually, delete forms need to be outside. --}}
                    {{-- I will fix the wrapping in the next step or adjust now. --}}
                    {{-- Strategy: Use JS for Bulk submit, don't wrap table in form. --}}

                </div>
            </div>
        </div>
    </div>

    <script>
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
                if (!confirm('Are you sure you want to perform this action on selected firewalls?')) {
                    return;
                }
                const form = document.getElementById('bulkForm');
                form.submit();
            }
        }
    </script>
</x-app-layout>