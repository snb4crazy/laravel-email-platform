<?php

namespace App\Http\Controllers;

use App\Models\MailTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MailTemplateController extends Controller
{
    public function index(): View
    {
        $templates = MailTemplate::query()
            ->where('tenant_id', Auth::id())
            ->latest()
            ->paginate(15);

        return view('portal.templates.index', compact('templates'));
    }

    public function create(): View
    {
        return view('portal.templates.create', [
            'eventTypes' => [
                MailTemplate::EVENT_CONTACT_FORM => 'Contact Form',
                MailTemplate::EVENT_WEBHOOK_CONTACT => 'Webhook Contact',
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'event_type' => ['required', 'string', 'max:100'],
            'subject_template' => ['required', 'string', 'max:500'],
            'body_html' => ['nullable', 'string'],
            'body_text' => ['nullable', 'string'],
            'is_default' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['tenant_id'] = Auth::id();
        $validated['is_default'] = ! empty($validated['is_default']);
        $validated['is_active'] = ! empty($validated['is_active']);

        MailTemplate::query()->create($validated);

        return redirect()->route('templates.index')->with('status', 'Template created.');
    }

    public function edit(MailTemplate $template): View
    {
        $this->authorize('update', $template);

        return view('portal.templates.edit', [
            'template' => $template,
            'eventTypes' => [
                MailTemplate::EVENT_CONTACT_FORM => 'Contact Form',
                MailTemplate::EVENT_WEBHOOK_CONTACT => 'Webhook Contact',
            ],
        ]);
    }

    public function update(Request $request, MailTemplate $template): RedirectResponse
    {
        $this->authorize('update', $template);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'event_type' => ['required', 'string', 'max:100'],
            'subject_template' => ['required', 'string', 'max:500'],
            'body_html' => ['nullable', 'string'],
            'body_text' => ['nullable', 'string'],
            'is_default' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_default'] = ! empty($validated['is_default']);
        $validated['is_active'] = ! empty($validated['is_active']);

        $template->update($validated);

        return redirect()->route('templates.index')->with('status', 'Template updated.');
    }

    public function destroy(MailTemplate $template): RedirectResponse
    {
        $this->authorize('delete', $template);

        $template->delete();

        return redirect()->route('templates.index')->with('status', 'Template deleted.');
    }
}
