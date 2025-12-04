<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System: User Manager: Groups: Edit') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST"
                        action="{{ route('system.user_manager.groups.update', [$firewall->id, $group['id']]) }}">
                        @csrf
                        @method('PATCH')

                        <!-- Group Name -->
                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Group Name')" />
                            <x-text-input id="name" class="block mt-1 w-full bg-gray-100" type="text" name="name"
                                :value="old('name', $group['name'])" required readonly />
                            <p class="text-xs text-gray-500 mt-1">Group name cannot be changed.</p>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Description')" />
                            <x-text-input id="description" class="block mt-1 w-full" type="text" name="description"
                                :value="old('description', $group['description'] ?? '')" />
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('system.user_manager.index', ['firewall' => $firewall->id, 'tab' => 'groups']) }}"
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