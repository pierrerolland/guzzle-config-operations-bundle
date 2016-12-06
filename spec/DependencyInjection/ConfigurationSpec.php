<?php

namespace spec\Guzzle\ConfigOperationsBundle\DependencyInjection;

use Guzzle\ConfigOperationsBundle\DependencyInjection\Configuration;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class ConfigurationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Configuration::class);
    }

    function its_get_config_tree_builder_should_return_tree_builder()
    {
        $this->getConfigTreeBuilder()->shouldHaveType(TreeBuilder::class);
    }
}
