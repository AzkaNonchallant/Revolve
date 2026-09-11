<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Revolve'))</title>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-body bg-surface-subtle text-text-primary min-h-screen flex flex-col">
        @include('partials.navbar')

        <main class="flex-grow">
            @yield('content')
        </main>

        @include('partials.footer')

        @stack('scripts')
    </body>
</html>