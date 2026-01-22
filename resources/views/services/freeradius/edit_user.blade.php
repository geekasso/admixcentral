<x-app-layout>
    <x-slot name="header">
        <x-firewall-header title="{{ isset($user) ? __('Edit FreeRADIUS User') : __('Add FreeRADIUS User') }}" :firewall="$firewall" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form
                    action="{{ isset($user) ? route('services.freeradius.users.update', ['firewall' => $firewall, 'username' => $user['username']]) : route('services.freeradius.users.store', $firewall) }}"
                    method="POST">
                    @csrf
                    @if(isset($user))
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 gap-6">

                        <div>
                            <x-input-label for="username" :value="__('Username')" />
                            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username"
                                :value="$user['username'] ?? ''" required />
                        </div>

                        <div>
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                                :value="$user['password'] ?? ''" required />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <x-text-input id="description" class="block mt-1 w-full" type="text" name="description"
                                :value="$user['description'] ?? ''" />
                        </div>

                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('services.freeradius.users.index', $firewall) }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:text-gray-800 active:bg-gray-50 transition ease-in-out duration-150">
                            Cancel
                        </a>
                        <x-primary-button>
                            {{ __('Save') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
