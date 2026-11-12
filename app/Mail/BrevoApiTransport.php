<?php

namespace App\Mail;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\MessageConverter;

/**
 * Envía los correos a través de la API HTTPS de Brevo (puerto 443).
 * Se usa porque el plan gratuito de Render bloquea los puertos SMTP.
 */
class BrevoApiTransport extends AbstractTransport
{
    public function __construct(protected string $key)
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $map = fn (array $addresses) => array_map(
            fn (Address $a) => array_filter(['email' => $a->getAddress(), 'name' => $a->getName() ?: null]),
            $addresses
        );

        $from = $email->getFrom()[0];

        $payload = array_filter([
            'sender' => array_filter(['email' => $from->getAddress(), 'name' => $from->getName() ?: null]),
            'to' => $map($email->getTo()),
            'cc' => $map($email->getCc()) ?: null,
            'bcc' => $map($email->getBcc()) ?: null,
            'replyTo' => ($r = $email->getReplyTo()) ? $map($r)[0] : null,
            'subject' => $email->getSubject(),
            'htmlContent' => $email->getHtmlBody(),
            'textContent' => $email->getTextBody(),
        ]);

        $response = Http::withHeaders(['api-key' => $this->key, 'accept' => 'application/json'])
            ->timeout(20)
            ->post('https://api.brevo.com/v3/smtp/email', $payload);

        if ($response->failed()) {
            throw new TransportException('Brevo API error '.$response->status().': '.$response->body());
        }
    }

    public function __toString(): string
    {
        return 'brevo+api://api.brevo.com';
    }
}
