@extends('layouts.portal')

@section('header', 'Edit Template')

@section('main')
<div class="max-w-2xl">
    <a href="{{ route('templates.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Back to Templates</a>

    @if ($errors->any())
        <div class="mt-4 px-4 py-3 bg-red-50 border border-red-200 rounded-md text-red-700 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('templates.update', $template) }}"
          class="mt-6 bg-white border border-gray-200 rounded-lg p-6 space-y-5">
        @method('PUT')
        @include('portal.templates._form')
        <button type="submit"
                class="w-full px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition-colors">
            Save Changes
        </button>
    </form>
</div>
@endsection
