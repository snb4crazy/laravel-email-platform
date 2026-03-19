<?php

namespace App\Http\Controllers;

use App\Models\MailMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(): View
    {
        $messages = MailMessage::query()
            ->where('tenant_id', Auth::id())
            ->with('site')
            ->latest()
            ->paginate(20);

        return view('portal.messages.index', compact('messages'));
    }

    public function show(MailMessage $mailMessage): View
    {
        $this->authorize('view', $mailMessage);

        $mailMessage->load(['site', 'events']);

        return view('portal.messages.show', compact('mailMessage'));
    }
}
