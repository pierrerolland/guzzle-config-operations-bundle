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
     * @var SerializerInterface|\JMS\Serializer\SerializerInterface
     */
    protected $serializer;

    /**
     * @param SerializerInterface|\JMS\Serializer\SerializerInterface $serializer
     */
    public function __construct($serializer)
    {
        $this->serializer = $serializer;
    }

    public function getClient(string $clientId): GuzzleClient
    {
        /* @var Client $client */
        $client = $this->container->get($clientId);
        $config = $client->getConfig();
        $responseClasses = $this->extractResponseClasses($config);
        $description = new Description($config);

        return new GuzzleClient($client, $description, $responseClasses, $this->serializer);
    }

    protected function extractResponseClasses(array &$config): array
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
    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }
}
