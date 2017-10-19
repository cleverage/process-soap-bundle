<?php

namespace CleverAge\ProcessSoapBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class CleverAgeProcessSoapExtension extends Extension
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * @var array
     */
    protected $config = [];

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->container = $container;

        $configuration = new Configuration();
        $this->config = $this->processConfiguration($configuration, $configs);

        $this->loadSoapClients();

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('tasks.yml');
    }

    protected function loadSoapClients()
    {
        foreach ($this->config['clients'] as $key => $client) {
            $definition = new Definition($client['class']);
            $definition->addTag('clever_age_process_soap.base_soap');
            $definition->addTag('clever_age_process_soap.client');
            $definition->addArgument(new Reference('logger'));

            if (array_key_exists('wsdl', $client)) {
                $definition->addMethodCall('setWsdl', [$client['wsdl']]);
            }
            if (array_key_exists('options', $client)) {
                $definition->addMethodCall('setOptions', [$client['options']]);
            }

            $definition->addMethodCall('setLogger', [$this->container->get('logger')]);

            $clientServiceName = sprintf('clever_age_process_soap.soap_client.%s', $key);

            $this->container->setDefinition($clientServiceName, $definition);
            if (null !== $client['service_alias']) {
                $this->container->setAlias($client['service_alias'], $clientServiceName);
            }
        }
    }
}
