{{-- 
    View: Captive Portal Status
    Purpose: Displays the Captive Portal status page.
    Note: Currently acts as a placeholder or error wrapper using the x-api-not-supported component if the API isn't ready.
--}}
<x-app-layout>
    <x-slot name="header">
        <x-firewall-header title="{{ __('Captive Portal Status') }}" :firewall="$firewall" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <x-api-not-supported :firewall="$firewall" urlSuffix="status_captiveportal.php" featureName="Captive Portal Status" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>