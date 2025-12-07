<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create CRL') }} - {{ $firewall->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <strong class="font-bold">Error:</strong>
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('system.certificate_manager.crls.store', $firewall) }}">
                        @csrf
                        <input type="hidden" name="method" value="internal">

                        <div class="grid grid-cols-1 gap-6">
                            <!-- Descriptive Name -->
                            <div>
                                <x-input-label for="descr" :value="__('Descriptive Name')" />
                                <x-text-input id="descr" class="block mt-1 w-full" type="text" name="descr" required
                                    autofocus />
                                <x-input-error :messages="$errors->get('descr')" class="mt-2" />
                            </div>

                            <!-- Certificate Authority -->
                            <div>
                                <x-input-label for="caref" :value="__('Certificate Authority')" />
                                <select id="caref" name="caref"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    @foreach($cas as $ca)
                                        <option value="{{ $ca['refid'] }}">{{ $ca['descr'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Save') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>