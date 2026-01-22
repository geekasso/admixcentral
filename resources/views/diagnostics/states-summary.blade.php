<x-app-layout>
    <x-slot name="header">
        <x-firewall-header title="{{ __('States Summary') }}" :firewall="$firewall" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <x-api-not-supported :firewall="$firewall" urlSuffix="diag_states_summary.php" featureName="States Summary" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
