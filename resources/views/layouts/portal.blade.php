@extends('layouts.app')

@section('body')
<div class="min-h-screen flex">

    {{-- ─── Sidebar ─────────────────────────────────────────────────── --}}
    <aside class="w-64 bg-indigo-900 text-indigo-100 flex flex-col flex-shrink-0">
        <div class="px-6 py-5 border-b border-indigo-700">
            <span class="block text-lg font-semibold tracking-tight">Email Platform</span>
            <span class="block text-xs text-indigo-300 mt-0.5 truncate">{{ auth()->user()->name }}</span>
        </div>

        <nav class="flex-1 py-4">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-6 py-2.5 text-sm transition-colors
                      {{ request()->routeIs('dashboard') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800 hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('sites.index') }}"
               class="flex items-center gap-3 px-6 py-2.5 text-sm transition-colors
                      {{ request()->routeIs('sites.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800 hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                </svg>
                My Sites
            </a>

            <a href="{{ route('messages.index') }}"
               class="flex items-center gap-3 px-6 py-2.5 text-sm transition-colors
                      {{ request()->routeIs('messages.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800 hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Messages
            </a>

            <a href="{{ route('templates.index') }}"
               class="flex items-center gap-3 px-6 py-2.5 text-sm transition-colors
                      {{ request()->routeIs('templates.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800 hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Templates
            </a>
        </nav>

        <div class="px-6 py-4 border-t border-indigo-700 text-xs text-indigo-400">
            v{{ config('app.version', '1.x') }}
        </div>
    </aside>

    {{-- ─── Main ─────────────────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col min-w-0">

        <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between shrink-0">
            <h1 class="text-base font-semibold text-gray-800">@yield('header', 'Portal')</h1>
            <div class="flex items-center gap-4 text-sm">
                <span class="text-gray-500">{{ auth()->user()->email }}</span>
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


