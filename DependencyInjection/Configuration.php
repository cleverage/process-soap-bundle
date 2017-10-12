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
    /**
     * {@inheritdoc}
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
                            ->scalarNode('wsdl')->isRequired()->end()
                            ->append($this->getSoapClientConfiguration())
                            ->scalarNode('service_alias')->defaultNull()->end()
                            ->scalarNode('class')->defaultValue('%clever_age_process_soap.soap_client.class%')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
        // @formatter:on

        return $node;
    }

    /**
     * @return ArrayNodeDefinition
     */
    protected function getSoapClientConfiguration()
    {
        $node = new ArrayNodeDefinition('options');

        // @formatter:off
        $node
            ->children()
                ->scalarNode('location')->end()
                ->scalarNode('uri')->end()
                ->enumNode('soap_version')->values([SOAP_1_1, SOAP_1_2])->end()
                ->scalarNode('login')->end()
                ->scalarNode('password')->end()
                ->scalarNode('proxy_host')->end()
                ->scalarNode('proxy_port')->end()
                ->scalarNode('proxy_login')->end()
                ->scalarNode('proxy_password')->end()
                ->scalarNode('local_cert')->end()
                ->scalarNode('passphrase')->end()
                ->enumNode('authentication')->values([SOAP_AUTHENTICATION_BASIC, SOAP_AUTHENTICATION_DIGEST])->end()
                ->scalarNode('compression')->end()
                ->scalarNode('encoding')->end()
                ->scalarNode('trace')->end()
                ->scalarNode('classmap')->end()
                ->scalarNode('exceptions')->end()
                ->scalarNode('connection_timeout')->end()
                ->arrayNode('typemap')
                    ->children()
                        ->scalarNode('type_name')->end()
                        ->scalarNode('type_ns')->end()
                        ->scalarNode('from_xml')->end()
                        ->scalarNode('to_xml')->end()
                    ->end()
                ->end()
                ->enumNode('cache_wsdl')->values([WSDL_CACHE_NONE, WSDL_CACHE_DISK, WSDL_CACHE_MEMORY, WSDL_CACHE_BOTH])->end()
                ->scalarNode('user_agent')->end()
                ->scalarNode('stream_context')->end()
                ->enumNode('features')->values([SOAP_SINGLE_ELEMENT_ARRAYS, SOAP_USE_XSI_ARRAY_TYPE, SOAP_WAIT_ONE_WAY_CALLS])->end()
                ->booleanNode('keep_alive')->end()
                ->enumNode('ssl_method')->values([SOAP_SSL_METHOD_TLS, SOAP_SSL_METHOD_SSLv2, SOAP_SSL_METHOD_SSLv3, SOAP_SSL_METHOD_SSLv23])->end()
            ->end();
        // @formatter:on

        return $node;
    }
}
