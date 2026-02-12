<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System Updates') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Current Version: <span
                                class="font-bold">{{ $currentVersion }}</span></h3>
                        <p class="text-sm text-gray-500">Manage your system updates and rollbacks.</p>
                    </div>

                    <form action="{{ route('system.updates.check') }}" method="POST">
                        @csrf
                        <x-button>
                            {{ __('Check for Updates') }}
                        </x-button>
                    </form>
                </div>

                @if(session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 font-medium text-sm text-red-600">
                        {{ session('error') }}
                    </div>
                @endif

                @if($latestUpdate)
                    <div class="border-t border-gray-200 py-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-md font-bold">Available Update: {{ $latestUpdate->available_version }}</h4>
                                <p class="text-sm text-gray-600">Status: <span
                                        class="uppercase font-semibold {{ $latestUpdate->status === 'failed' ? 'text-red-500' : 'text-blue-500' }}">{{ $latestUpdate->status }}</span>
                                </p>
                                @if($latestUpdate->requested_at)
                                    <p class="text-xs text-gray-400">Requested:
                                        {{ $latestUpdate->requested_at->diffForHumans() }}</p>
                                @endif
                            </div>

                            <div>
                                @if($latestUpdate->status === 'idle' || $latestUpdate->status === 'failed')
                                    <form action="{{ route('system.updates.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="version" value="{{ $latestUpdate->available_version }}">
                                        <x-button class="bg-blue-600 hover:bg-blue-500">
                                            {{ __('Install Update') }}
                                        </x-button>
                                    </form>
                                @elseif($latestUpdate->status === 'complete')
                                    <span class="text-green-500 font-bold">Installed</span>
                                @else
                                    <x-button disabled class="opacity-50 cursor-not-allowed">
                                        {{ __('In Progress...') }}
                                    </x-button>
                                @endif
                            </div>
                        </div>

                        <!-- Update Log -->
                        <div class="mt-4 bg-gray-100 p-4 rounded-md text-xs font-mono max-h-64 overflow-y-auto">
                            <p class="font-bold mb-2">Process Log:</p>
                            @if($latestUpdate->log)
                                @foreach($latestUpdate->log as $entry)
                                    <div class="mb-1">{{ $entry }}</div>
                                @endforeach
                            @else
                                <span class="text-gray-400">No logs yet.</span>
                            @endif

                            @if($latestUpdate->last_error)
                                <div class="mt-2 text-red-600 font-bold">Error: {{ $latestUpdate->last_error }}</div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="border-t border-gray-200 py-6 text-center text-gray-500">
                        No active update tasks. Click "Check for Updates" to see if a new version is available.
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>