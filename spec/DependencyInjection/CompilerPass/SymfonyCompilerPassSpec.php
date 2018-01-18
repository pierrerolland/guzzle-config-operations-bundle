<?php

namespace spec\Guzzle\ConfigOperationsBundle\DependencyInjection\CompilerPass;

use Guzzle\ConfigOperationsBundle\DependencyInjection\CompilerPass\SymfonyCompilerPass;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\DependencyInjection\Loader;

class SymfonyCompilerPassSpec extends ObjectBehavior
{
    function it_implement_a_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SymfonyCompilerPass::class);
    }

    function its_process(ContainerBuilder $container, Definition $serializer, Loader\YamlFileLoader $loader)
    {
        $this->setFileLoader($loader);
        $container->hasDefinition('serializer')->willReturn(true);
        $container->getDefinition('serializer')->willReturn($serializer);
        $serializer->getClass()->willReturn(Serializer::class);
        $loader->load('symfony_normalizer.yml')->shouldBeCalled();

        $this->process($container);
    }

    function its_process_without_serializer(ContainerBuilder $container, Definition $serializer)
    {
        $container->hasDefinition('serializer')->willReturn(false);

        $container->setDefinition(
            'guzzle_config_operations.normalizer.recursive_object',
            Argument::type(Definition::class)
        )->shouldNotBeCalled();

        $this->process($container);
    }

    function its_process_with_jms_serializer(ContainerBuilder $container, Definition $serializer)
    {
        $container->hasDefinition('serializer')->willReturn(false);
        $container->getDefinition('serializer')->willReturn($serializer);
        $serializer->getClass()->willReturn('JMS\Serializer\Serializer');

        $container->setDefinition(
            'guzzle_config_operations.normalizer.recursive_object',
            Argument::type(Definition::class)
        )->shouldNotBeCalled();

        $this->process($container);
    }
}
