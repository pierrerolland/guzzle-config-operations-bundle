<?php

namespace Guzzle\ConfigOperationsBundle;

use GuzzleHttp\Command\Guzzle\Description;
use JMS\Serializer\Serializer;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
     * @var Serializer
     */
    protected $serializer;

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Gets the client
     *
     * @param string $clientId
     * @param string $alias
     *
     * @return GuzzleClient
     */
    public function getClient($clientId, $alias)
    {
        $descriptions = $this->container->getParameter('guzzle_client_factory.descriptions');
        if (!isset($descriptions[$alias])) {
            return null;
        }

        /* @var \GuzzleHttp\ClientInterface $client */
        $client = $this->container->get($clientId);
        $responseClasses = $this->extractResponseClasses($descriptions[$alias]);
        $description = new Description($descriptions[$alias]);

        return new GuzzleClient($client, $description, $responseClasses, $this->serializer);
    }

    /**
     * @param array $description
     *
     * @return array
     */
    protected function extractResponseClasses(array &$description)
    {
        if (!array_key_exists('operations', $description)) {
            return [];
        }

        $responseClasses = [];
        foreach ($description['operations'] as $operationName => $operation) {
            if (array_key_exists('responseClass', $operation)) {
                $responseClasses[$operationName] = $operation['responseClass'];
                unset($description['operations'][$operationName]['responseClass']);
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
