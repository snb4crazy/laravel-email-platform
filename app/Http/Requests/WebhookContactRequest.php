<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WebhookContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        // TODO: enforce webhook signature + credential checks in middleware.
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

            // Draft multi-site contract (webhook can specify either key or id)
            'site_key' => ['nullable', 'string', 'max:128'],
            'site_id' => ['nullable', 'integer', 'exists:sites,id'],

            // Draft webhook integrity fields (not validated/enforced yet)
            'request_id' => ['nullable', 'string', 'max:128'],
            'timestamp' => ['nullable', 'integer'],
            'nonce' => ['nullable', 'string', 'max:128'],
            'signature' => ['nullable', 'string', 'max:1024'],
            'meta' => ['nullable', 'array'],
        ];
    }
}
