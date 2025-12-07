<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('DNS Server (Bind) - Settings') }} - {{ $firewall->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if(isset($api_not_supported) && $api_not_supported)
                        <x-api-not-supported :firewall="$firewall" urlSuffix="pkg_edit.php?xml=bind.xml"
                            featureName="Bind DNS Server" />
                    @elseif(isset($error))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <strong class="font-bold">Error:</strong>
                            <span class="block sm:inline">{{ $error }}</span>
                        </div>
                    @else
                        <!-- Content for Settings would go here -->
                        <p>Bind Settings Configuration.</p>
                        <!-- Form Implementation Placeholder -->
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>