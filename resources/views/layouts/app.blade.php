<!DOCTYPE html>
@php
    $settings = \App\Models\SystemSetting::pluck('value', 'key')->toArray();
    $theme = $settings['theme'] ?? 'light';
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="{{ $theme === 'dark' ? 'dark' : '' }} h-full bg-gray-100 dark:bg-gray-900 overflow-hidden">

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

<body class="font-sans antialiased h-full overflow-hidden bg-gray-100 dark:bg-gray-900">
    <div class="flex h-screen bg-gray-100 dark:bg-gray-900">
        @include('layouts.sidebar')

        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow z-10">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            @if(request()->route('firewall'))
                @include('layouts.navigation')
            @endif

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900">

                <!-- Page Content -->
                @isset($firewall)
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                        <x-apply-changes-banner :firewall="$firewall" />
                    </div>
                @endisset


                {{ $slot }}
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: "{{ session('error') }}",
                    toast: true, // Set to false if you want a modal instead of a toast
                    position: 'top-end', // Center if modal
                    showConfirmButton: true,
                    // timer: 5000 
                });
            @endif
        });
    </script>


</body>

</html>