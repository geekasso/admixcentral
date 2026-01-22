@props(['firewall', 'urlSuffix', 'featureName' => 'Feature', 'message' => null])

<div class="flex flex-col items-center justify-center py-12">
    <div class="bg-yellow-100 dark:bg-yellow-900 rounded-full p-4 mb-4">
        <svg class="w-12 h-12 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
    </div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">{{ $featureName }} Not Supported</h3>
    <p class="text-gray-500 dark:text-gray-400 text-center max-w-md">
        {{ $message ?? 'The pfSense API does not currently support configuring ' . $featureName . '. Please configure these settings directly in the pfSense web interface.' }}
    </p>
    <div class="mt-6">
        <a href="{{ $firewall->url }}/{{ $urlSuffix }}" target="_blank"
            class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
            Open in pfSense
            <svg class="ml-2 -mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
        </a>
    </div>
</div>
