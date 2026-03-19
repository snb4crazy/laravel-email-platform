@extends('layouts.app')

@section('title', 'Login')

@section('body')
<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-semibold text-gray-900">Email Platform</h1>
            <p class="text-sm text-gray-500 mt-1">Sign in to your account</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-md text-red-700 text-sm">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}"
              class="bg-white border border-gray-200 rounded-lg p-6 space-y-5 shadow-sm">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="password">Password</label>
                <input id="password" type="password" name="password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" id="remember" name="remember" value="1"
                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <label for="remember" class="text-sm text-gray-700">Remember me</label>
            </div>

            <button type="submit"
                    class="w-full px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition-colors">
                Sign In
            </button>
        </form>

        <p class="mt-4 text-center text-xs text-gray-400">
            Account provisioning is admin-only.
        </p>
    </div>
</div>
@endsection
