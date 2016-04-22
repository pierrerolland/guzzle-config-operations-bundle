<?php

namespace Guzzle\ConfigOperationsBundle;

use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
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
        $description = new Description($descriptions[$alias]);

        return new GuzzleClient($client, $description);
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
