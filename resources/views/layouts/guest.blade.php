@php
    $settings = \App\Models\SystemSetting::all()->pluck('value', 'key');
    $theme = $settings['theme'] ?? 'light';
    $logo = $settings['logo_path'] ?? null;
    $pageBg = $settings['sidebar_bg'] ?? '#1f2937';

    // Calculate YIQ contrast for the form container
    $hex = str_replace('#', '', $pageBg);
    if (strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
    $isPageLight = ($yiq >= 128);

    // If Page is Light -> Form Dark. If Page is Dark -> Form Light.
    // We add 'dark' class to the container if we want the Dark Form look, 
    // to potentially trigger any child dark-mode overrides if they exist.
    if ($isPageLight) {
        // Light Page -> Solid Form (Matching Sidebar Color)
        // Dark text for contrast against light background
        $formClasses = 'text-gray-900 border-gray-200 shadow-lg';
        $shadowClass = 'shadow-[0_50px_100px_-20px_rgba(0,0,0,0.1)]';
    } else {
        // Dark Page -> Solid Form (Matching Sidebar Color)
        // White text for contrast against dark background
        $formClasses = 'text-white border-white/10 shadow-lg dark';
        $shadowClass = 'shadow-[0_50px_100px_-20px_rgba(0,0,0,0.5)]';
    }

    // Pattern Settings
    // We use a CSS Mask approach to ensure ALL text/shapes in the SVGs render as the exact same color/opacity.
    // This solves the issue of some SVGs being "dark" and others "light".

    $patternColor = $isPageLight ? '#000000' : '#ffffff';
    $patternOpacity = $isPageLight ? '0.04' : '0.03'; // slightly tweaked for mask visibility

    // Default fallback
    $patternName = 'Grid Default';
    $base64Pattern = base64_encode("<svg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'><g fill='none' fill-rule='evenodd'><path d='M0 40L40 0H20L0 20M40 40V20L20 40' stroke='#000' stroke-width='2' stroke-opacity='1'/></g></svg>");
    $patternUrl = "data:image/svg+xml;base64,{$base64Pattern}";

    try {
        $files = glob(public_path('patterns/*.svg'));
        if ($files && count($files) > 0) {
            $randomFile = $files[array_rand($files)];
            // Use asset() to get the URL for the mask
            $patternUrl = asset('patterns/' . basename($randomFile));

            // Generate a readable name
            $filename = pathinfo($randomFile, PATHINFO_FILENAME);
            $patternName = ucwords(str_replace(['-', '_'], ' ', $filename));
        }
    } catch (\Exception $e) {
        // Fallback to default
    }

    // Input Field Colors (Derived from Page BG)
    // Rule: "Inputs should always be darker UNLESS the background is the darkest it can be"
    // We use YIQ ($yiq calculated above) as brightness proxy.

    if ($yiq < 30) {
        // Very Dark Background. Switch to Lightening.
        $mixWhite = 0.15;
        $rI = floor($r * (1 - $mixWhite) + 255 * $mixWhite);
        $gI = floor($g * (1 - $mixWhite) + 255 * $mixWhite);
        $bI = floor($b * (1 - $mixWhite) + 255 * $mixWhite);
    } else {
        // Standard Case: Darken inputs by 30%
        $factor = 0.7;
        $rI = floor($r * $factor);
        $gI = floor($g * $factor);
        $bI = floor($b * $factor);
    }
    $inputBg = sprintf("#%02x%02x%02x", $rI, $gI, $bI);

    // Focus Color
    $focusColor = '#6366f1';

    // Recalculate contrast for Input Text (YIQ)
    $yiqInput = (($rI * 299) + ($gI * 587) + ($bI * 114)) / 1000;
    $inputText = ($yiqInput >= 128) ? '#1f2937' : '#ffffff'; // Dark Text on Light Input, White on Dark
    $inputBorder = ($yiqInput >= 128) ? 'rgba(0,0,0,0.2)' : 'rgba(255,255,255,0.2)';

    // Vignette for depth (Radial Gradient) - Kept on Body
    $vignette = $isPageLight
        ? 'radial-gradient(circle at center, rgba(255,255,255,0) 0%, rgba(0,0,0,0.05) 100%)'
        : 'radial-gradient(circle at center, rgba(0,0,0,0) 20%, rgba(0,0,0,0.4) 100%)';
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
    <script>
        window.AdmixConfig = {
            reverb: {
                key: "{{ env('VITE_REVERB_APP_KEY') }}",
                host: "{{ env('VITE_REVERB_HOST') }}",
                port: "{{ env('VITE_REVERB_PORT', 8080) }}",
                scheme: "{{ env('VITE_REVERB_SCHEME', 'http') }}"
            }
        };
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if(isset($settings['favicon_path']))
        <link rel="icon" href="{{ $settings['favicon_path'] }}">
    @endif

    <!-- Dynamic Input Styling -->
    <style>
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="checkbox"] {
            background-color:
                {{ $inputBg }}
                !important;
            color:
                {{ $inputText }}
                !important;
            border-color:
                {{ $inputBorder }}
                !important;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="checkbox"]:focus {
            border-color:
                {{ $focusColor }}
                !important;
            box-shadow: 0 0 0 1px
                {{ $focusColor }}
                !important;
        }
    </style>
</head>

<body class="font-sans text-gray-900 antialiased h-screen overflow-hidden relative"
    style="background-color: {{ $pageBg }}; background-image: {{ $vignette }};">

    <!-- Pattern Overlay (CSS Mask) -->
    <div style="
        position: fixed; 
        inset: 0; 
        z-index: 0; 
        pointer-events: none; 
        background-color: {{ $patternColor }}; 
        opacity: {{ $patternOpacity }};
        -webkit-mask-image: url('{{ $patternUrl }}'); 
        mask-image: url('{{ $patternUrl }}');
        -webkit-mask-repeat: repeat; 
        mask-repeat: repeat;
    "></div>

    <!-- Pattern Name Indicator -->
    <div class="fixed bottom-4 right-4 z-0 font-mono text-[9px] uppercase tracking-widest opacity-10 select-none pointer-events-none pb-safe pr-safe"
        style="color: {{ $patternColor }}">
        {{ $patternName }}
    </div>

    <!-- Scrollable Content Wrapper -->
    <div class="w-full h-full overflow-y-auto flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative z-10">
        <!-- Main Content -->
        <div class="mb-6">
            <a href="/">
                @if($logo)
                    <img src="{{ $logo }}" class="fill-current text-gray-500"
                        style="width: auto; height: auto; max-width: 12rem; max-height: 4rem; object-fit: contain;"
                        alt="Logo" />
                @else
                    <img src="{{ asset('images/logo.png') }}" class="w-20 h-20 fill-current text-gray-500"
                        style="width: 5rem; height: 5rem;" alt="Logo" />
                @endif
            </a>
        </div>

        <div class="w-full sm:max-w-md px-6 py-4 {{ $shadowClass }} overflow-hidden sm:rounded-lg border {{ $formClasses }} relative"
            style="background-color: {{ $pageBg }};">
            <!-- Modern Gradient Top Border/Accent - Reduced Opacity -->
            <div
                class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 opacity-50">
            </div>
            {{ $slot }}
        </div>
    </div>
</body>

</html>