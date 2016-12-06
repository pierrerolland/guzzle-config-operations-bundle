<?php

namespace spec\Guzzle\ConfigOperationsBundle;

use Guzzle\ConfigOperationsBundle\GuzzleClient;
use Guzzle\ConfigOperationsBundle\GuzzleClientFactory;
use GuzzleHttp\Client;
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Serializer\SerializerInterface;

class GuzzleClientFactorySpec extends ObjectBehavior
{
    function let(SerializerInterface $serializer, ContainerBuilder $container)
    {
        $this->beConstructedWith($serializer);
        $this->setContainer($container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GuzzleClientFactory::class);
    }

    function its_get_client_returns_client($container, Client $client)
    {
        $client->getConfig()->willReturn([])->shouldBeCalled();
        $container->get('client')->willReturn($client);

        $this->getClient('client')->shouldHaveType(GuzzleClient::class);
    }
}
