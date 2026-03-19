@extends('layouts.app')

@section('title', 'Login')

@section('body')
<div class="min-h-screen bg-gradient-to-b from-slate-50 to-slate-100 flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-4xl grid grid-cols-1 md:grid-cols-2 bg-white border border-slate-200 rounded-2xl shadow-lg overflow-hidden">
        <div class="p-8 md:p-10 bg-slate-900 text-slate-100">
            <p class="inline-flex items-center rounded-full bg-slate-800 px-3 py-1 text-xs font-semibold tracking-wide uppercase">
                Email Platform
            </p>
            <h1 class="mt-5 text-3xl font-semibold leading-tight">Welcome back</h1>
            <p class="mt-3 text-sm text-slate-300">
                Sign in to manage sites, templates, and submission messages in one place.
            </p>

            <div class="mt-8 space-y-3 text-sm text-slate-300">
                <p class="flex items-start gap-2"><span class="text-emerald-400">-</span><span>Multi-site contact intake</span></p>
                <p class="flex items-start gap-2"><span class="text-emerald-400">-</span><span>Per-tenant templates and message history</span></p>
                <p class="flex items-start gap-2"><span class="text-emerald-400">-</span><span>Admin-managed accounts only</span></p>
            </div>
        </div>

        <div class="p-8 md:p-10 bg-white">
            <h2 class="text-xl font-semibold text-slate-900">Sign in</h2>
            <p class="mt-1 text-sm text-slate-500">Use your provisioned credentials.</p>

            @if ($errors->any())
                <div class="mt-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-5">
                @csrf

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700" for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700" for="password">Password</label>
                    <input id="password" type="password" name="password" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                </div>

                <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" id="remember" name="remember" value="1"
                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    Remember me
                </label>

                <button type="submit"
                    class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                    Sign In
                </button>
            </form>

            <p class="mt-6 text-xs text-slate-400">
                Need access? Ask an administrator to create your account.
            </p>
        </div>
    </div>
</div>
@endsection
