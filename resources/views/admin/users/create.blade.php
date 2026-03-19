@extends('layouts.admin')

@section('header', 'Create User')

@section('main')
<div class="max-w-lg">
    <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Back to Users</a>

    @if ($errors->any())
        <div class="mt-4 px-4 py-3 bg-red-50 border border-red-200 rounded-md text-red-700 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.users.store') }}"
          class="mt-6 bg-white border border-gray-200 rounded-lg p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1" for="name">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1" for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1" for="role">Role</label>
            <select id="role" name="role" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="user" @selected(old('role', 'user') === 'user')>User</option>
                <option value="admin" @selected(old('role') === 'admin')>Admin</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1" for="password">Password</label>
            <input id="password" name="password" type="password" required minlength="8"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1" for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" id="email_verified" name="email_verified" value="1" @checked(old('email_verified'))
                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <label for="email_verified" class="text-sm text-gray-700">Mark email as verified</label>
        </div>

        <button type="submit"
                class="w-full px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition-colors">
            Create User
        </button>
    </form>
</div>
@endsection
