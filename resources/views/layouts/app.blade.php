<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Audit COBIT 2019') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    
    <body class="font-sans antialiased text-gray-900 bg-slate-50" x-data="{ sidebarOpen: false, isMini: false }">
        
        <div class="flex h-screen overflow-hidden bg-slate-50">
            
            @include('layouts.sidebar')

            <div class="flex flex-col flex-1 w-full overflow-hidden">
                
                @include('layouts.navigation')

                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50">
                    {{ $slot }}
                </main>
                
            </div>
        </div>

    </body>
</html>