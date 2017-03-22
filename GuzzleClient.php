<?php

namespace Guzzle\ConfigOperationsBundle;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Command\CommandInterface;
use GuzzleHttp\Command\Guzzle\DescriptionInterface;
use GuzzleHttp\Command\Guzzle\GuzzleClient as BaseGuzzleClient;
use JMS\Serializer\SerializerInterface as JMSSerializerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Overloads the original GuzzleClient to add the Serializer
 *
 * @author Pierre Rolland <roll.pierre@gmail.com>
 */
class GuzzleClient extends BaseGuzzleClient
{
    /**
     * @var array
     */
    protected $responseClasses = [];

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var SerializerInterface|JMSSerializerInterface
     */
    protected $serializer;

    /**
     * @param ClientInterface $client
     * @param DescriptionInterface $description
     * @param array $responseClasses
     * @param SerializerInterface|JMSSerializerInterface $serializer
     * @param array $config
     */
    public function __construct(
        ClientInterface $client,
        DescriptionInterface $description,
        array $responseClasses,
        $serializer,
        array $config = []
    ) {
        $this->client = $client;
        $this->responseClasses = $responseClasses;
        $this->serializer = $serializer;

        parent::__construct(
            $client,
            $description,
            null,
            [$this, 'transformResponse'],
            null,
            $config
        );
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getResponseClass($name)
    {
        return array_key_exists($name, $this->responseClasses) ? $this->responseClasses[$name] : 'array';
    }

    /**
     * @param ResponseInterface $response
     * @param RequestInterface $request
     * @param CommandInterface $command
     *
     * @return mixed
     */
    public function transformResponse(ResponseInterface $response, RequestInterface $request, CommandInterface $command)
    {
        $body = $response->getBody()->getContents();

        if (empty($body)) {
            return null;
        }

        $responseClass = $this->getResponseClass($command->getName());

        return $responseClass === 'array' ? json_decode($body, true) : $this
            ->serializer
            ->deserialize($body, $responseClass, 'json');
    }
}
