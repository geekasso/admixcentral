@props(['title', 'firewall'])

<div>
    <div class="flex justify-between items-start">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $title }}
            </h2>
            <div class="mt-1 flex flex-col sm:flex-row sm:items-center">
                <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center">
                     <a href="{{ route('dashboard') }}" class="font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors duration-200">
                        Dashboard
                     </a>
                     <span class="mx-2 text-gray-300 dark:text-gray-600">/</span>
                     <a href="{{ route('dashboard', ['customer' => $firewall->company->name ?? '']) }}" class="inline-flex items-center font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors duration-200" title="Filter Dashboard by this Company">
                        {{ $firewall->company->name ?? 'Unknown Company' }}
                        <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                     </a>
                     <span class="mx-2 text-gray-300 dark:text-gray-600">/</span>
                     <a href="{{ route('firewall.dashboard', $firewall) }}" class="font-medium hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200">
                        {{ \Illuminate\Support\Str::replaceFirst(($firewall->company->name ?? '') . ' - ', '', $firewall->name) }}
                     </a>
                </div>
                <!-- URL Link - Separator on mobile/desktop? -->
                <span class="hidden sm:inline mx-2 text-gray-300 dark:text-gray-600">|</span>
                <a href="{{ \Illuminate\Support\Str::startsWith($firewall->url, ['http://', 'https://']) ? $firewall->url : 'https://' . $firewall->url }}" target="_blank" rel="noopener noreferrer" class="mt-1 sm:ml-0 text-xs text-gray-400 hover:text-indigo-500 dark:text-gray-500 dark:hover:text-indigo-400 flex items-center transition-colors duration-200">
                    <span>{{ parse_url(\Illuminate\Support\Str::startsWith($firewall->url, ['http://', 'https://']) ? $firewall->url : 'https://' . $firewall->url, PHP_URL_HOST) }}</span>
                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </div>
        </div>
        @if(isset($actions))
            <div class="ml-4 flex-shrink-0">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>
