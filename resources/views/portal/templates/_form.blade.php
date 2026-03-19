@csrf

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1" for="name">Template Name *</label>
    <input id="name" name="name" type="text" value="{{ old('name', $template->name ?? '') }}" required
           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1" for="event_type">Event Type *</label>
    <select id="event_type" name="event_type" required
            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        @foreach ($eventTypes as $value => $label)
            <option value="{{ $value }}" @selected(old('event_type', $template->event_type ?? '') === $value)>{{ $label }}</option>
        @endforeach
    </select>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1" for="subject_template">Subject Template *</label>
    <input id="subject_template" name="subject_template" type="text"
           value="{{ old('subject_template', $template->subject_template ?? '') }}" required
           placeholder="New contact from {{name}}"
           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    <p class="mt-1 text-xs text-gray-400">You can use placeholders like {{name}}, {{email}}, {{message}}.</p>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1" for="body_text">Plain Text Body</label>
    <textarea id="body_text" name="body_text" rows="5"
              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono">{{ old('body_text', $template->body_text ?? '') }}</textarea>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1" for="body_html">HTML Body</label>
    <textarea id="body_html" name="body_html" rows="8"
              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono">{{ old('body_html', $template->body_html ?? '') }}</textarea>
</div>

<div class="flex items-center gap-6">
    <div class="flex items-center gap-2">
        <input type="checkbox" id="is_default" name="is_default" value="1"
               @checked(old('is_default', $template->is_default ?? false))
               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        <label for="is_default" class="text-sm text-gray-700">Set as default for this event type</label>
    </div>
    <div class="flex items-center gap-2">
        <input type="checkbox" id="is_active" name="is_active" value="1"
               @checked(old('is_active', $template->is_active ?? true))
               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        <label for="is_active" class="text-sm text-gray-700">Active</label>
    </div>
</div>
