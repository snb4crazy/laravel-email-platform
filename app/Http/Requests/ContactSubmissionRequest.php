<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // TODO: enforce site-level auth policy in middleware/policies.
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'subject' => ['nullable', 'string', 'max:255'],
            'file_url' => ['nullable', 'url', 'max:2048'],

            // Draft multi-site contract (no enforcement yet)
            'site_key' => ['nullable', 'string', 'max:128'],
            'site_domain' => ['nullable', 'string', 'max:255'],
            'site_id' => ['nullable', 'integer', 'exists:sites,id'],

            // Draft anti-bot/auth fields (TODO to verify later)
            'captcha_token' => ['nullable', 'string', 'max:4096'],
            'api_key' => ['nullable', 'string', 'max:255'],
            'request_id' => ['nullable', 'string', 'max:128'],
            'meta' => ['nullable', 'array'],
        ];
    }
}
