{{--
    View: Limiter Info
    Purpose: Wrapper for the Limiter Info diagnostic tool.
    Current State: Uses 'x-api-not-supported' component until the API endpoint is implemented.
--}}
<x-app-layout>
    <x-slot name="header">
        <x-firewall-header title="{{ __('Limiter Info') }}" :firewall="$firewall" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <x-api-not-supported :firewall="$firewall" urlSuffix="diag_limiter_info.php" featureName="Limiter Info" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>