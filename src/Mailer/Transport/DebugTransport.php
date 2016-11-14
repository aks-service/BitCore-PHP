<?php
namespace Bit\Mailer\Transport;

use Bit\Mailer\AbstractTransport;
use Bit\Mailer\Email;

/**
 * Debug Transport class, useful for emulate the email sending process and inspect the resulted
 * email message before actually send it during development
 *
 */
class DebugTransport extends AbstractTransport
{

    /**
     * Send mail
     *
     * @param \Bit\Mailer\Email $email Bit Email
     * @return array
     */
    public function send(Email $email)
    {
        $headers = $email->getHeaders(['from', 'sender', 'replyTo', 'readReceipt', 'returnPath', 'to', 'cc', 'subject']);
        $headers = $this->_headersToString($headers);
        $message = implode("\r\n", (array)$email->message());
        return ['headers' => $headers, 'message' => $message];
    }
}
