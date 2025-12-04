<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Firewall Schedules') }} - {{ $firewall->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div x-data="scheduleHandler()" class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Schedules</h3>
                            <button @click="openModal()"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Add Schedule
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Name</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Description</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Time Ranges</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($schedules as $index => $schedule)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $schedule['name'] ?? '' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $schedule['descr'] ?? '' }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                                @if(isset($schedule['timerange']) && is_array($schedule['timerange']))
                                                    {{ count($schedule['timerange']) }} ranges
                                                @else
                                                    0 ranges
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button @click="editSchedule({{ $index }}, {{ json_encode($schedule) }})"
                                                    class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                                <form
                                                    action="{{ route('firewall.schedules.destroy', ['firewall' => $firewall->id, 'schedule' => $index]) }}"
                                                    method="POST" class="inline-block"
                                                    onsubmit="return confirm('Are you sure you want to delete this schedule?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-900">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4"
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                                No schedules found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Modal -->
                        <div x-show="showModal" class="fixed z-10 inset-0 overflow-y-auto" style="display: none;">
                            <div
                                class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                                </div>
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                                    aria-hidden="true">&#8203;</span>
                                <div
                                    class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                                    <form
                                        :action="isEdit ? '/firewall/{{ $firewall->id }}/schedules/' + form.id : '{{ route('firewall.schedules.store', $firewall) }}'"
                                        method="POST">
                                        @csrf
                                        <template x-if="isEdit">
                                            <input type="hidden" name="_method" value="PUT">
                                        </template>
                                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            <div class="grid grid-cols-6 gap-6">
                                                <div class="col-span-6 sm:col-span-3">
                                                    <label for="name"
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                                                    <input type="text" name="name" x-model="form.name"
                                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                        required>
                                                </div>

                                                <div class="col-span-6">
                                                    <label for="descr"
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                                    <input type="text" name="descr" x-model="form.descr"
                                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                </div>

                                                <div class="col-span-6">
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Time
                                                        Ranges</label>
                                                    <template x-for="(range, index) in form.timerange" :key="index">
                                                        <div class="flex gap-2 mb-2 items-end">
                                                            <div class="flex-1">
                                                                <label class="block text-xs text-gray-500">Month</label>
                                                                <select :name="'timerange['+index+'][month]'"
                                                                    x-model="range.month"
                                                                    class="block w-full py-1 px-2 border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded-md shadow-sm text-xs">
                                                                    <option value="">Any</option>
                                                                    @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $m)
                                                                        <option value="{{ $loop->index + 1 }}">{{ $m }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="flex-1">
                                                                <label class="block text-xs text-gray-500">Day</label>
                                                                <select :name="'timerange['+index+'][day]'"
                                                                    x-model="range.day"
                                                                    class="block w-full py-1 px-2 border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded-md shadow-sm text-xs">
                                                                    <option value="">Any</option>
                                                                    @for($i = 1; $i <= 31; $i++)
                                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                                    @endfor
                                                                </select>
                                                            </div>
                                                            <div class="flex-1">
                                                                <label class="block text-xs text-gray-500">Hour</label>
                                                                <input type="text" :name="'timerange['+index+'][hour]'"
                                                                    x-model="range.hour" placeholder="0:00-23:59"
                                                                    class="block w-full py-1 px-2 border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded-md shadow-sm text-xs">
                                                            </div>
                                                            <div class="flex-1">
                                                                <label
                                                                    class="block text-xs text-gray-500">Description</label>
                                                                <input type="text"
                                                                    :name="'timerange['+index+'][rangedescr]'"
                                                                    x-model="range.rangedescr"
                                                                    class="block w-full py-1 px-2 border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded-md shadow-sm text-xs">
                                                            </div>
                                                            <button type="button" @click="removeRange(index)"
                                                                class="text-red-600 hover:text-red-900 p-1">
                                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                    </path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </template>
                                                    <button type="button" @click="addRange()"
                                                        class="mt-2 inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                        Add Time Range
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function scheduleHandler() {
            return {
                showModal: false,
                isEdit: false,
                form: {
                    id: '',
                    name: '',
                    descr: '',
                    timerange: []
                },
                resetForm() {
                    this.form = {
                        id: '',
                        name: '',
                        descr: '',
                        timerange: []
                    };
                    this.addRange(); // Start with one empty range
                },
                addRange() {
                    this.form.timerange.push({
                        month: '',
                        day: '',
                        hour: '',
                        position: '',
                        rangedescr: ''
                    });
                },
                removeRange(index) {
                    this.form.timerange.splice(index, 1);
                },
                openModal() {
                    this.resetForm();
                    this.isEdit = false;
                    this.showModal = true;
                },
                editSchedule(index, schedule) {
                    this.resetForm();
                    this.form.timerange = []; // Clear default empty range
                    this.isEdit = true;
                    this.form.id = index;
                    this.form.name = schedule.name || '';
                    this.form.descr = schedule.descr || '';

                    if (schedule.timerange && Array.isArray(schedule.timerange)) {
                        this.form.timerange = schedule.timerange.map(range => ({
                            month: range.month || '',
                            day: range.day || '',
                            hour: range.hour || '',
                            position: range.position || '',
                            rangedescr: range.rangedescr || ''
                        }));
                    } else if (schedule.timerange) {
                        // Handle single object case if API returns it not as array
                        this.form.timerange.push({
                            month: schedule.timerange.month || '',
                            day: schedule.timerange.day || '',
                            hour: schedule.timerange.hour || '',
                            position: schedule.timerange.position || '',
                            rangedescr: schedule.timerange.rangedescr || ''
                        });
                    }

                    if (this.form.timerange.length === 0) {
                        this.addRange();
                    }

                    this.showModal = true;
                }
            }
        }
    </script>
</x-app-layout>