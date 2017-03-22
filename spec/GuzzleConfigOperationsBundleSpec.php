<?php

namespace spec\Guzzle\ConfigOperationsBundle;

use Guzzle\ConfigOperationsBundle\DependencyInjection\CompilerPass\ClientCompilerPass;
use Guzzle\ConfigOperationsBundle\DependencyInjection\CompilerPass\SymfonyCompilerPass;
use Guzzle\ConfigOperationsBundle\GuzzleConfigOperationsBundle;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class GuzzleConfigOperationsBundleSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(GuzzleConfigOperationsBundle::class);
    }

    function its_build_should_add_compiler_pass(ContainerBuilder $container)
    {
        $container->addCompilerPass(Argument::type(ClientCompilerPass::class))->shouldBeCalled();
        $container->addCompilerPass(Argument::type(SymfonyCompilerPass::class))->shouldBeCalled();

        $this->build($container);
    }
}
