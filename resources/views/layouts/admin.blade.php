@extends('layouts.app')

@section('body')
<div class="min-h-screen flex">

    {{-- ─── Sidebar ─────────────────────────────────────────────────── --}}
    <aside class="w-64 bg-gray-900 text-gray-100 flex flex-col flex-shrink-0">
        <div class="px-6 py-5 border-b border-gray-700">
            <span class="block text-lg font-semibold tracking-tight">Email Platform</span>
            <span class="block text-xs text-gray-400 mt-0.5">Admin Panel</span>
        </div>

        <nav class="flex-1 py-4">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-6 py-2.5 text-sm transition-colors
                      {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('admin.users.index') }}"
               class="flex items-center gap-3 px-6 py-2.5 text-sm transition-colors
                      {{ request()->routeIs('admin.users.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Users
            </a>

            <a href="{{ route('admin.sites.index') }}"
               class="flex items-center gap-3 px-6 py-2.5 text-sm transition-colors
                      {{ request()->routeIs('admin.sites.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                </svg>
                All Sites
            </a>

            <a href="{{ route('admin.messages.index') }}"
               class="flex items-center gap-3 px-6 py-2.5 text-sm transition-colors
                      {{ request()->routeIs('admin.messages.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                All Messages
            </a>
        </nav>

        <div class="px-6 py-4 border-t border-gray-700 text-xs text-gray-500">
            v{{ config('app.version', '1.x') }}
        </div>
    </aside>

    {{-- ─── Main ─────────────────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col min-w-0">

        <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between shrink-0">
            <h1 class="text-base font-semibold text-gray-800">@yield('header', 'Admin')</h1>
            <div class="flex items-center gap-4 text-sm">
                <span class="text-gray-500">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-red-600 hover:text-red-800 transition-colors">Logout</button>
                </form>
            </div>
        </header>

        @if (session('status'))
            <div class="mx-6 mt-4 px-4 py-3 bg-green-50 border border-green-200 rounded-md text-green-700 text-sm">
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mx-6 mt-4 px-4 py-3 bg-red-50 border border-red-200 rounded-md text-red-700 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <main class="flex-1 p-6 overflow-auto">
            @yield('main')
        </main>
    </div>
</div>
@endsection


