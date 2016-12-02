<?php

namespace Guzzle\ConfigOperationsBundle;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Command\CommandInterface;
use GuzzleHttp\Command\Guzzle\DescriptionInterface;
use GuzzleHttp\Command\Guzzle\GuzzleClient as BaseGuzzleClient;
use JMS\Serializer\Serializer;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

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
    protected $responseClasses = array();

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @param ClientInterface $client
     * @param DescriptionInterface $description
     * @param array $responseClasses
     * @param Serializer $serializer
     * @param array $config
     */
    public function __construct(
        ClientInterface $client,
        DescriptionInterface $description,
        array $responseClasses,
        Serializer $serializer,
        array $config = array()
    ) {
        $this->client = $client;
        $this->responseClasses = $responseClasses;
        $this->serializer = $serializer;

        parent::__construct(
            $client,
            $description,
            null,
            array($this, 'transformResponse'),
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
        return $this
            ->serializer
            ->deserialize(
                $response->getBody()->getContents(),
                $this->getResponseClass($command->getName()),
                'json'
            );
    }
}
