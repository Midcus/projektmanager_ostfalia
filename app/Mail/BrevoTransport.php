<?php

namespace App\Mail;

use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Configuration;
use GuzzleHttp\Client;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\RawMessage;

class BrevoTransport extends AbstractTransport
{
    protected $client;
    protected $apiKey;

    public function __construct($apiKey)
    {
        parent::__construct();
        $this->apiKey = $apiKey;
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $this->apiKey);
        $this->client = new TransactionalEmailsApi(new Client(), $config);
    }

    protected function doSend(SentMessage $message): void
    {
        $email = $message->getOriginalMessage();
        $sendSmtpEmail = new \Brevo\Client\Model\SendSmtpEmail();
    

        $from = $email->getFrom();
        $fromEmail = $from[0]->getAddress();
        $fromName = $from[0]->getName() ?: 'Laravel';
        $sendSmtpEmail->setSender(['email' => $fromEmail, 'name' => $fromName]);
    

        $recipients = [];
        foreach ($email->getTo() as $to) {
            $recipientName = $to->getName() ?: 'Recipient'; // Giá trị mặc định
            $recipients[] = ['email' => $to->getAddress(), 'name' => $recipientName]; // Sử dụng $recipientName
        }
        $sendSmtpEmail->setTo($recipients);
    

        $sendSmtpEmail->setSubject($email->getSubject());
        $sendSmtpEmail->setHtmlContent($email->getHtmlBody());
    

        try {
            $this->client->sendTransacEmail($sendSmtpEmail);
        } catch (\Exception $e) {
            \Log::error('Failed to send email via Brevo: ' . $e->getMessage());
            throw new \RuntimeException('Failed to send email: ' . $e->getMessage());
        }
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}