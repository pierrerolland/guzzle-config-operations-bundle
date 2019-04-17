<?php

namespace Guzzle\ConfigOperationsBundle;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Command\CommandInterface;
use GuzzleHttp\Command\Guzzle\DescriptionInterface;
use GuzzleHttp\Command\Guzzle\GuzzleClient as BaseGuzzleClient;
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
     * @var SerializerInterface|\JMS\Serializer\SerializerInterface
     */
    protected $serializer;

    /**
     * @param ClientInterface $client
     * @param DescriptionInterface $description
     * @param array $responseClasses
     * @param SerializerInterface|\JMS\Serializer\SerializerInterface $serializer
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

    protected function getResponseClass(string $name): ?string
    {
        return array_key_exists($name, $this->responseClasses) ? $this->responseClasses[$name] : null;
    }

    /**
     * @param ResponseInterface $response
     * @param RequestInterface $request
     * @param CommandInterface $command
     *
     * @return ResponseInterface|object|array
     */
    public function transformResponse(ResponseInterface $response, RequestInterface $request, CommandInterface $command)
    {
        $responseClass = $this->getResponseClass($command->getName());

        if (null === $responseClass) {
            return $response;
        }

        $body = $response->getBody()->getContents();

        if (empty($body)) {
            return null;
        }

        return $responseClass === 'array' ? json_decode($body, true) : $this
            ->serializer
            ->deserialize($body, $responseClass, 'json');
    }
}
