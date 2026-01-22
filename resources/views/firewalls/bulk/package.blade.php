<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Bulk Install Package') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="mb-6">
                        <div
                            class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg class="h-5 w-5 text-blue-500 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <h3 class="font-medium text-blue-800 dark:text-blue-200">Installing to
                                        {{ count(explode(',', $firewall_ids)) }} Firewall(s)</h3>
                                    <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                                        Select a package below to install on all selected firewalls. Firewalls that
                                        already have the package installed will be skipped automatically.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(isset($error))
                        <div
                            class="mb-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg p-4">
                            <p class="text-red-700 dark:text-red-300">{{ $error }}</p>
                        </div>
                    @endif

                    <form action="{{ route('firewalls.bulk.store', 'package') }}" method="POST">
                        @csrf
                        <input type="hidden" name="firewall_ids" value="{{ $firewall_ids }}">

                        <div class="mb-6">
                            <label for="package"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Select Package to Install
                            </label>
                            <select name="package" id="package" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Select Package --</option>
                                @foreach($packages as $pkg)
                                    <option value="{{ $pkg['name'] ?? '' }}">
                                        {{ $pkg['name'] ?? 'Unknown' }}
                                        @if(!empty($pkg['version'])) ({{ $pkg['version'] }}) @endif
                                        @if(!empty($pkg['category'])) - {{ $pkg['category'] }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('firewalls.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                onclick="return confirm('Are you sure you want to install this package on all selected firewalls?');">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Install Package
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
