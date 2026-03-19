<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailMessage;
use Illuminate\View\View;

class AdminMessageController extends Controller
{
    public function index(): View
    {
        $messages = MailMessage::query()
            ->with(['tenant', 'site'])
            ->latest()
            ->paginate(25);

        return view('admin.messages.index', compact('messages'));
    }

    public function show(MailMessage $mailMessage): View
    {
        $mailMessage->load(['tenant', 'site', 'events']);

        return view('admin.messages.show', compact('mailMessage'));
    }
}
