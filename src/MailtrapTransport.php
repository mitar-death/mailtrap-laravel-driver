<?php

namespace Mazi\MailtrapDriver;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Mail\Transport\Transport;
use Swift_Mime_SimpleMessage;

class MailtrapTransport extends Transport
{

    /**
     * Guzzle client instance.
     *
     * @var \GuzzleHttp\ClientInterface
     */
    protected Client $client;

    /**
     * The MailTrap API key.
     *
     * @var string
     */
    protected string $apiToken;

    /**
     * The MailTrap API endpoint.
     *
     * @var string
     */
    protected string $baseUrl = 'https://send.api.mailtrap.io/api/';

    /**
     * The MailTrap headers.
     *
     * @var array
     */
    protected array $headers = [];

    /**
     * The MailTrap send data.
     *
     * @var array
     */
    protected array $sendData = [];

    /**
     * The Guzzle request method.
     *
     * @var string
     */
    protected string $method = 'POST';

    protected string $category;

    /**
     * Create a new MailTrap transport instance.
     *
     * @param  string  $apiToken
     * @return void
     */
    public function __construct(string $apiToken, $category = null)
    {
        $this->setApiToken($apiToken);
        $this->setHeaders(
            ['Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json']
        );
        $this->setClient(new Client(
            [
                'base_uri' => $this->baseUrl,
                'headers' => $this->headers,
                "verify" => false,
            ]));
        $this->setCategory($category ?? 'default');
    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {

        $this->beforeSendPerformed($message);
        $to = $this->getTo($message);

        $bcc = $message->getBcc();

        $message->setBcc([]);

        $payload = $this->payload($message, $to);
        $this->setSendData($this->sendData);

        try {

            $this->client->request('post', 'send', [
                "json" => $payload,
            ]);

            $message->setBcc($bcc);
            $this->sendPerformed($message);

            return $this->numberOfRecipients($message);
        } catch (GuzzleException $e) {
            throw new \Exception("Failed to send email");
        }

    }

    protected function getMessageId($response)
    {
        return object_get(
            json_decode($response->getBody()->getContents()), 'id'
        );
    }

    /**
     * Get the "to" payload field for the API request.
     *
     * @param  \Swift_Mime_SimpleMessage  $message
     * @return array
     */
    protected function getTo(Swift_Mime_SimpleMessage $message)
    {
        return array_map(function ($address, $name) {
            return ['email' => $address, 'name' => $name];
        }, array_keys($message->getTo()), $message->getTo());
    }

    /**
     * Get tall of the contacts for the message.
     *
     * @param  \Swift_Mime_SimpleMessage  $message
     * @return array
     */
    protected function allContacts(Swift_Mime_SimpleMessage $message)
    {
        return array_merge(
            (array) $message->getTo(),
            (array) $message->getcc(),
            (array) $message->getBcc()
        );
    }

    /**
     * Get the HTTP payload for sending the Mailtrap message.
     *
     * @param  \Swift_Mime_SimpleMessage  $message
     * @param string $to
     * @return array
     */
    protected function payload($message, $to)
    {

        return [
            'from' => [
                'email' => key($message->getFrom()),
                'name' => current($message->getFrom()),
            ],
            'headers' => [
                'Authorization' => 'Bearer caa992920637efa171fca0b480774087',
            ],
            'to' => $to,
            'subject' => $message->getSubject(),
            'html' => $message->getBody(),
            'category' => $this->category,
        ];
    }

    /**
     * Set Guzzle headers.
     *
     * @param  array  $headers
     * @return array
     */
    public function setHeaders(array $headers = [])
    {
        return $this->headers = $headers;
    }
    /**
     * Set Base Uri.
     *
     * @param  string  $baseUrl
     * @return string
     */
    public function setBaseUrl(string $baseUrl)
    {
        return $this->baseUrl = $baseUrl;
    }

    /**
     * Set Guzzle client.
     *
     * @param  Client  $client
     * @return Client
     */
    public function setClient(Client $client): Client
    {
        return $this->client = $client;
    }

    /**
     * Set MailTrap Api Token.
     *
     * @param  string $apiToken
     * @return string
     */
    public function setApiToken(string $apiToken)
    {
        return $this->apiToken = $apiToken;
    }
    /**
     * Get MailTrap Api Token.
     *
     * @return string
     */
    protected function getApiToken()
    {
        return $this->apiToken;
    }
    /**
     * Set MailTrap Category.
     *
     * @param  string $category
     * @return string
     */
    public function setCategory(string $category)
    {
        return $this->category = $category;
    }
    /**
     * Get MailTrap Category.
     *
     * @return string
     */
    protected function getCategory()
    {
        return $this->category;
    }
    /**
     * Get Base Uri.
     * @return string
     */
    protected function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Get Guzzle headers.
     *
     * @return array
     */
    protected function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get Guzzle client request payload.
     *
     * @return array
     */
    protected function getSendData()
    {
        return $this->sendData;
    }

    /**
     * Set Guzzle client request payload.
     *
     * @param  array  $sendData
     * @return array
     */
    public function setSendData($sendData)
    {
        return $this->sendData = $sendData;
    }
}
