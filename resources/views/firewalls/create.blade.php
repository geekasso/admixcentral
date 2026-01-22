<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add Firewall') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('firewalls.store') }}" method="POST">
                        @csrf

                        {{-- Company Selection --}}
                        @if($companies->count() > 1 || auth()->user()->isGlobalAdmin())
                            <div class="mb-4">
                                <label for="company_id"
                                    class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Company</label>
                                <select name="company_id" id="company_id" required
                                    class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ (old('company_id') == $company->id || request('company_id') == $company->id) ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @else
                            {{-- Single company available (Company Admin) --}}
                            <input type="hidden" name="company_id" value="{{ $companies->first()->id }}">
                            <div class="mb-4">
                                <label
                                    class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Company</label>
                                <input type="text" disabled value="{{ $companies->first()->name }}"
                                    class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-400">
                            </div>
                        @endif

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium mb-2">Firewall Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                placeholder="e.g., Office Firewall">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="url" class="block text-sm font-medium mb-2">URL</label>
                            <input type="url" name="url" id="url" value="{{ old('url') }}" required
                                class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                placeholder="https://192.168.1.1:443">
                            @error('url')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-data="{ authMethod: '{{ old('auth_method', 'basic') }}' }">
                            <div class="mb-4">
                                <label for="auth_method" class="block text-sm font-medium mb-2">Authentication
                                    Method</label>
                                <select name="auth_method" id="auth_method" x-model="authMethod"
                                    class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                    <option value="basic">Basic Auth (Username/Password)</option>
                                    <option value="token">Bearer Token</option>
                                </select>
                            </div>

                            <div x-show="authMethod === 'basic'">
                                <div class="mb-4">
                                    <label for="api_key" class="block text-sm font-medium mb-2">API Username</label>
                                    <input type="text" name="api_key" id="api_key" value="{{ old('api_key') }}"
                                        :required="authMethod === 'basic'"
                                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                        placeholder="admin">
                                    @error('api_key')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="api_secret" class="block text-sm font-medium mb-2">API Password</label>
                                    <input type="password" name="api_secret" id="api_secret"
                                        :required="authMethod === 'basic'"
                                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                    @error('api_secret')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div x-show="authMethod === 'token'">
                                <div class="mb-4">
                                    <label for="api_token" class="block text-sm font-medium mb-2">API Token</label>
                                    <textarea name="api_token" id="api_token" rows="3"
                                        :required="authMethod === 'token'"
                                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                        placeholder="ey..."></textarea>
                                    @error('api_token')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium mb-2">Description
                                (Optional)</label>
                            <textarea name="description" id="description" rows="3"
                                class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex gap-3">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Create Firewall
                            </button>
                            <a href="{{ route('firewalls.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
