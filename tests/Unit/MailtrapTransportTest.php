<?php
namespace Mazi\MailtrapDriver\Tests\Unit;

use Mazi\MailtrapDriver\Tests\TestCase;
use Swift_Message;

class MailtrapTransportTest extends TestCase
{
    public function testSendSuccess()
    {
        $message = new Swift_Message();
        $message->setFrom('example@example.com', 'Example Sender');
        $message->setTo('to@example', 'Example Receiver');
        $message->setSubject('Test Subject');
        $message->setBody('Test Body');

        $response = $this->transport->send($message);
        $this->assertEquals(1, $response);
    }

    public function testSendFailure()
    {

        $this->expectException(\Exception::class);

        $message = new Swift_Message();
        $message->setFrom(['from@example.com' => 'Example Sender']);
        $message->setTo(['to@example.com' => 'Example Recipient']);
        $message->setBody('This is a test email body.');
        $message->setSubject('Test Subject');

        $this->faliureTransport->send($message);
    }

}
