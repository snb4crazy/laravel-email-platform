<?php

namespace App\Jobs;

use App\Mail\ContactMail;
use App\Models\MailMessage;
use App\Models\MailMessageEvent;
use App\Models\MailTemplate;
use App\Models\Site;
use App\Services\Mail\MailTemplateResolver;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendMailJob implements ShouldQueue
{
    use Queueable;

    public const TYPE_WEB = 'web';

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
        public readonly ?int $tenantId = null,
        public readonly ?int $siteId = null,
    ) {}

    public function handle(MailTemplateResolver $resolver): void
    {
        // Resolve delivery address.
        // When the submission belongs to a registered site that has a
        // notification_email configured, ALL messages are delivered there
        // (the site owner's inbox) regardless of what the caller supplied
        // as `email`.  The caller's address is stored as reply_to so the
        // owner can reply directly.  This prevents the platform from being
        // used to send email to arbitrary addresses.
        $fallbackEmail = (string) config('mail.from.address');
        $fallbackName = (string) config('mail.from.name', config('app.name'));

        $deliveryEmail = $fallbackEmail;
        $deliveryName = $fallbackName;
        $replyTo = null;

        if ($this->siteId) {
            $site = Site::find($this->siteId);
            if ($site && $site->notification_email) {
                $deliveryEmail = $site->notification_email;
                $deliveryName = $site->name;
                $replyTo = $this->email;
            }
        }

        // 1. Persist the message record
        $mailMessage = MailMessage::create([
            'source' => $this->type,
            'to_name' => $deliveryName,
            'to_email' => $deliveryEmail,
            'reply_to' => $replyTo,
            'from_email' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
            'subject' => $this->subject,
            'body_text' => $this->message,
            'file_url' => $this->fileUrl,
            'status' => MailMessage::STATUS_QUEUED,
            'ip' => $this->ip,
            'user_agent' => $this->userAgent,
            'tenant_id' => $this->tenantId,
            'site_id' => $this->siteId,
            'metadata' => [
                'submitter_name' => $this->name,
                'submitter_email' => $this->email,
            ],
        ]);

        $mailMessage->recordEvent(MailMessageEvent::TYPE_QUEUED);

        // 2. Attempt sending
        try {
            $mailMessage->update(['status' => MailMessage::STATUS_SENDING]);
            $mailMessage->recordEvent(MailMessageEvent::TYPE_SENDING);

            // Map job type to template event type constant
            $eventType = $this->type === self::TYPE_WEBHOOK
                ? MailTemplate::EVENT_WEBHOOK_CONTACT
                : MailTemplate::EVENT_CONTACT_FORM;

            // Resolve best available template (blade fallback → global DB → tenant DB)
            $resolved = $resolver->resolve(
                eventType: $eventType,
                vars: [
                    'name' => $this->name,
                    'subject' => $this->subject ?? '',
                    'body' => $this->message,
                    'source' => $this->type,
                    'file_url' => $this->fileUrl ?? '',
                    'app_name' => config('app.name'),
                    'received_at' => now()->toDateTimeString(),
                    'senderName' => $this->name,
                    'fileUrl' => $this->fileUrl ?? '',
                    'receivedAt' => now()->toDateTimeString(),
                ],
                tenantId: $this->tenantId,
            );

            Log::info('SendMailJob resolved template', [
                'mail_message_id' => $mailMessage->id,
                'resolved_via' => $resolved->resolvedVia,
                'event_type' => $eventType,
                'delivery_to' => $deliveryEmail,
                'submitter' => $this->email,
            ]);

            // TODO: uncomment once MAIL_MAILER is configured for real delivery
            // Mail::to($deliveryEmail, $deliveryName)->send(new ContactMail($resolved));

            // 3. Mark as sent
            $mailMessage->update(['status' => MailMessage::STATUS_SENT]);
            $mailMessage->recordEvent(MailMessageEvent::TYPE_SENT);

        } catch (Throwable $e) {
            // 4. Record failure
            $mailMessage->update(['status' => MailMessage::STATUS_FAILED]);
            $mailMessage->recordEvent(MailMessageEvent::TYPE_FAILED, [
                'error' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            throw $e;
        }
    }
}
