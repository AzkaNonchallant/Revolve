<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Dashboard — Revolve')</title>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-body bg-surface-subtle text-text-primary">
        <div class="dashboard-shell" data-dashboard-shell>
            @include('partials.dashboard-sidebar')

            <div class="dashboard-main">
                <header class="dashboard-topbar">
                    <div class="h-full flex items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center gap-3">
                            <button type="button" data-dashboard-sidebar-toggle class="lg:hidden w-10 h-10 flex items-center justify-center rounded-full hover:bg-surface-muted transition-colors" aria-label="Buka menu">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                    <path d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>
                            <h1 class="text-headline-sm font-semibold">@yield('header', 'Dashboard')</h1>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('home') }}" class="btn btn-outline btn-sm hidden sm:inline-flex">Toko</a>
                            <div class="relative" data-user-menu>
                                <button type="button" data-user-menu-toggle
                                    class="w-10 h-10 flex items-center justify-center rounded-full bg-obsidian text-white text-sm font-bold hover:bg-obsidian-light transition-colors"
                                    aria-label="Menu akun" aria-expanded="false">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </button>
                                <div data-user-menu-panel class="hidden absolute right-0 mt-2 w-56 card elevation-3 overflow-hidden z-50">
                                    <div class="px-4 py-3 border-b border-surface-muted">
                                        <p class="text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                                        <p class="text-xs text-text-muted truncate">{{ auth()->user()->email }}</p>
                                    </div>
                                    <div class="py-1">
                                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2.5 text-sm hover:bg-surface-subtle">Profil</a>
                                        <a href="{{ route('notifications.index') }}" class="block px-4 py-2.5 text-sm hover:bg-surface-subtle">Notifikasi</a>
                                    </div>
                                    <div class="py-1 border-t border-surface-muted">
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2.5 text-sm text-error hover:bg-error-bg">Keluar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="flex-grow max-w-[1400px] w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    @include('partials.alerts')
                    @yield('content')
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>