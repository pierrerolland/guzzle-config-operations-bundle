<?php

namespace spec\Guzzle\ConfigOperationsBundle\DependencyInjection\CompilerPass;

use Guzzle\ConfigOperationsBundle\DependencyInjection\CompilerPass\ClientCompilerPass;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ClientCompilerPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ClientCompilerPass::class);
    }

    function its_process_should_add_definitions(ContainerBuilder $container)
    {
        $container->has('guzzle_client.1')->willReturn(false);
        $container->has('guzzle_client.2')->willReturn(true);

        $container
            ->findTaggedServiceIds('guzzle.client')
            ->willReturn([
                'client.1' => [
                    ['alias' => '1']
                ],
                'client.2' => [
                    ['alias' => '2']
                ]
            ])
            ->shouldBeCalled();

        $container
            ->addDefinitions(Argument::allOf(
                Argument::type('array'),
                Argument::containing(Argument::type(Definition::class)),
                Argument::size(1)
            ))
            ->shouldBeCalled();

        $this->process($container);
    }
}
