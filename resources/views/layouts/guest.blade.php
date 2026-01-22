@php
    $settings = \App\Models\SystemSetting::all()->pluck('value', 'key');
    $theme = $settings['theme'] ?? 'light';
    $logo = $settings['logo_path'] ?? null;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ $theme === 'dark' ? 'dark' : '' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if(isset($settings['favicon_path']))
        <link rel="icon" href="{{ $settings['favicon_path'] }}">
    @endif
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-800 dark:bg-gray-800">
        <div>
            <a href="/">
                @if($logo)
                    <img src="{{ $logo }}" class="fill-current text-gray-500"
                        style="width: 5rem; height: 5rem; object-fit: contain;" alt="Logo" />
                @else
                    <img src="{{ asset('images/logo.png') }}" class="w-20 h-20 fill-current text-gray-500"
                        style="width: 5rem; height: 5rem;" alt="Logo" />
                @endif
            </a>
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-gray-200 dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>
    </div>
</body>

</html>
