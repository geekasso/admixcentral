<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System: User Manager: Users: Edit') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST"
                        action="{{ route('system.user_manager.users.update', [$firewall->id, $user['id']]) }}">
                        @csrf
                        @method('PATCH')

                        <!-- Username (Read-only usually, but API might allow change. Let's keep it editable but warn) -->
                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Username')" />
                            <x-text-input id="name" class="block mt-1 w-full bg-gray-100" type="text" name="name"
                                :value="old('name', $user['name'])" required readonly />
                            <p class="text-xs text-gray-500 mt-1">Username cannot be changed.</p>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                                placeholder="Leave blank to keep unchanged" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Full Name -->
                        <div class="mb-4">
                            <x-input-label for="descr" :value="__('Full Name')" />
                            <x-text-input id="descr" class="block mt-1 w-full" type="text" name="descr"
                                :value="old('descr', $user['descr'] ?? '')" />
                            <x-input-error :messages="$errors->get('descr')" class="mt-2" />
                        </div>

                        <!-- Disabled -->
                        <div class="flex items-center mb-4">
                            <input id="disabled" name="disabled" type="checkbox"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ (old('disabled') || !empty($user['disabled'])) ? 'checked' : '' }}>
                            <label for="disabled"
                                class="ml-2 block text-sm text-gray-900">{{ __('Disable this user') }}</label>
                        </div>

                        <!-- Groups -->
                        <div class="mb-4">
                            <x-input-label for="groups" :value="__('Group Membership')" />
                            <select id="groups" name="groups[]" multiple
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm h-32">
                                @foreach($groups as $group)
                                    @php
                                        $isMember = in_array($user['name'], $group['member'] ?? []);
                                    @endphp
                                    <option value="{{ $group['name'] }}" {{ $isMember ? 'selected' : '' }}>
                                        {{ $group['name'] }} ({{ $group['description'] }})</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Hold Ctrl (Windows) or Command (Mac) to select
                                multiple groups.</p>
                            <x-input-error :messages="$errors->get('groups')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('system.user_manager.index', ['firewall' => $firewall->id, 'tab' => 'users']) }}"
                                class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                            <x-primary-button>
                                {{ __('Save') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>