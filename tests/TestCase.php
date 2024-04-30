<?php
namespace Mazi\MailtrapDriver\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mazi\MailtrapDriver\MailtrapTransport;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public $client;

    public $transport;
    public $faliureTransport;
    protected function setUp(): void
    {
        parent::setUp();

        $mock = new MockHandler([
            new Response(200, [], 'Email Sent Successfully'),
            new RequestException("Error Communicating with Server", new Request('POST', 'test'))
        ]);

        $faliureMock = new MockHandler([
            new RequestException("Error Communicating with Server", new Request('POST', 'test')),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $faliureHandlerStack = HandlerStack::create($faliureMock);

        $this->client = new Client(['handler' => $handlerStack]);
        $faliureClient = new Client(['handler' => $faliureHandlerStack]);

        $this->transport = new MailtrapTransport('api_token', 'category');
        $this->transport->setClient($this->client);
        $this->faliureTransport = new MailtrapTransport('api_token', 'category');
        $this->faliureTransport->setClient($faliureClient);

    }

}
