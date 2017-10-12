<?php

namespace CleverAge\ProcessSoapBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    use Traits\SoapClientConfigurationTrait;

    /**
     * {@inheritdoc}
     * @throws \RuntimeException
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('clever_age_process_soap');

        $this->addSoapClientsTree($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     * @return $this|ArrayNodeDefinition
     */
    protected function addSoapClientsTree(ArrayNodeDefinition $node)
    {
        // @formatter:off
        $node
            ->fixXmlConfig('client')
            ->children()
                ->arrayNode('clients')
                    ->canBeUnset()
                    ->useAttributeAsKey('key')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('wsdl')->end()
                            ->append($this->getSoapClientConfiguration())
                            ->scalarNode('service_alias')->end()
                            ->scalarNode('class')->defaultValue('%clever_age_process_soap.soap_client.class%')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
        // @formatter:on

        return $node;
    }
}
