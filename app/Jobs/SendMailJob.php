<?php

namespace App\Jobs;

use App\Models\MailMessage;
use App\Models\MailMessageEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendMailJob implements ShouldQueue
{
    use Queueable;

    public const TYPE_WEB     = 'web';
    public const TYPE_WEBHOOK = 'webhook';

    public function __construct(
        public readonly string $type,
        public readonly string $name,
        public readonly string $email,
        public readonly string $message,
        public readonly ?string $subject = null,
        public readonly ?string $fileUrl = null,
        public readonly ?string $ip = null,
        public readonly ?string $userAgent = null,
    ) {}

    public function handle(): void
    {
        // 1. Persist the message record
        $mailMessage = MailMessage::create([
            'source'     => $this->type,
            'to_name'    => $this->name,
            'to_email'   => $this->email,
            'from_email' => config('mail.from.address'),
            'from_name'  => config('mail.from.name'),
            'subject'    => $this->subject,
            'body_text'  => $this->message,
            'file_url'   => $this->fileUrl,
            'status'     => MailMessage::STATUS_QUEUED,
            'ip'         => $this->ip,
            'user_agent' => $this->userAgent,
        ]);

        $mailMessage->recordEvent(MailMessageEvent::TYPE_QUEUED);

        // 2. Attempt sending
        try {
            $mailMessage->update(['status' => MailMessage::STATUS_SENDING]);
            $mailMessage->recordEvent(MailMessageEvent::TYPE_SENDING);

            // TODO: replace with actual mail sending, e.g.:
            // Mail::to($this->email)->send(new ContactMail($this->name, $this->message, $this->subject));
            Log::info('SendMailJob processing contact submission', [
                'mail_message_id' => $mailMessage->id,
                'type'            => $this->type,
                'to'              => $this->email,
                'subject'         => $this->subject,
                'file_url'        => $this->fileUrl,
            ]);

            // 3. Mark as sent
            $mailMessage->update(['status' => MailMessage::STATUS_SENT]);
            $mailMessage->recordEvent(MailMessageEvent::TYPE_SENT);

        } catch (Throwable $e) {
            // 4. Record failure
            $mailMessage->update(['status' => MailMessage::STATUS_FAILED]);
            $mailMessage->recordEvent(MailMessageEvent::TYPE_FAILED, [
                'error'     => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            throw $e; // let Laravel queue handle retries
        }
    }
}
