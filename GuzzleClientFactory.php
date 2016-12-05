<?php

namespace Guzzle\ConfigOperationsBundle;

use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Factory for guzzle clients
 *
 * @author Pierre Rolland <roll.pierre@gmail.com>
 */
class GuzzleClientFactory implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Gets the client
     *
     * @param string $clientId
     *
     * @return GuzzleClient
     */
    public function getClient($clientId)
    {
        /* @var Client $client */
        $client = $this->container->get($clientId);
        $config = $client->getConfig();
        $responseClasses = $this->extractResponseClasses($config);
        $description = new Description($config);

        return new GuzzleClient($client, $description, $responseClasses, $this->serializer);
    }

    /**
     * @param array $config
     *
     * @return array
     */
    protected function extractResponseClasses(array &$config)
    {
        if (!array_key_exists('operations', $config)) {
            return [];
        }

        $responseClasses = [];
        foreach ($config['operations'] as $operationName => $operation) {
            if (array_key_exists('responseClass', $operation)) {
                $responseClasses[$operationName] = $operation['responseClass'];
                unset($config['operations'][$operationName]['responseClass']);
            }
        }

        return $responseClasses;
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
