<x-app-layout>
    <x-slot name="header">
        <x-firewall-header title="{{ __('System: Advanced') }} - {{ ucfirst($tab) }}" :firewall="$firewall" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            @foreach(['admin' => 'Admin Access', 'firewall' => 'Firewall & NAT', 'networking' => 'Networking', 'miscellaneous' => 'Miscellaneous', 'tunables' => 'System Tunables', 'notifications' => 'Notifications'] as $key => $label)
                                <a href="{{ route('system.advanced', ['firewall' => $firewall, 'tab' => $key]) }}"
                                    class="{{ $tab === $key ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </nav>
                    </div>

                    @php
                        $urlSuffix = 'system_advanced_admin.php';
                        $name = 'System Advanced';
                        switch ($tab) {
                            case 'admin':
                                $urlSuffix = 'system_advanced_admin.php';
                                $name = 'Admin Access';
                                break;
                            case 'firewall':
                                $urlSuffix = 'system_advanced_firewall.php';
                                $name = 'Firewall & NAT';
                                break;
                            case 'networking':
                                $urlSuffix = 'system_advanced_network.php';
                                $name = 'Networking';
                                break;
                            case 'miscellaneous':
                                $urlSuffix = 'system_advanced_misc.php';
                                $name = 'Miscellaneous';
                                break;
                            case 'tunables':
                                $urlSuffix = 'system_advanced_sysctl.php';
                                $name = 'System Tunables';
                                break;
                            case 'notifications':
                                $urlSuffix = 'system_advanced_notifications.php';
                                $name = 'Notifications';
                                break;
                        }
                    @endphp

                    <x-api-not-supported :firewall="$firewall" :urlSuffix="$urlSuffix" :featureName="$name" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
