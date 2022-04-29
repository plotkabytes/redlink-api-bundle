<?php

/*
 * This file is part of the Vercom PHP API Client Symfony Bundle.
 *
 * (c) Mateusz Żyła <mateusz.zylaa@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plotkabytes\VercomApiBundle\DependencyInjection;

use Plotkabytes\VercomApi\DefaultClient;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class PlotkabytesVercomApiExtension extends Extension
{
    const PATH_ALIAS = 'plotkabytes_vercom_api';

    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @return void
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $this->addClients($config['clients'], $container);
    }

    /**
     * Add all clients specified in configuration. Also set default client.
     *
     * @param array $clients
     * @param ContainerBuilder $container
     *
     * @return void
     */
    private function addClients(array $clients, ContainerBuilder $container) : void
    {
        if(count($clients) == 0)
        {
            $this->createClient(
                "default",
                "HERE_INSERT_AUTHORIZATION_KEY",
                "HERE_INSERT_APPLICATION_KEY",
                null,
                null,
                $container
            );

            return;
        }

        $defaultSelected = false;

        foreach ($clients as $name => $client) {

            $this->createClient(
                $name,
                $client['authorization_key'],
                $client['application_key'],
                $client['alias'],
                $client['http_client'],
                $container
            );

            if (true === $client['default'] && false === $defaultSelected) {
                $this->setDefaultClient((string)$name, $container);
                $defaultSelected = true;
            }
        }

        if (!$defaultSelected) {
            reset($clients);
            $this->setDefaultClient((string)key($clients), $container);
        }
    }

    /**
     * Set default client for requests.
     *
     * @param string $name
     * @param ContainerBuilder $container
     */
    private function setDefaultClient(string $name, ContainerBuilder $container): void
    {
        $container->setAlias(self::PATH_ALIAS, self::PATH_ALIAS . '.client.default');
        $container->setAlias(self::PATH_ALIAS . '.client.default', sprintf('%s.client.%s', self::PATH_ALIAS, $name));
        $container->setAlias(DefaultClient::class, self::PATH_ALIAS . '.client.default');
    }

    /**
     * Create clients and service definitions for them.
     *
     * @param string $name
     * @param string $authorizationKey
     * @param string|null $applicationKey
     * @param string|null $alias
     * @param string|null $httpClient
     * @param ContainerBuilder $container
     *
     * @return void
     */
    private function createClient(string $name, string $authorizationKey, ?string $applicationKey, ?string $alias, ?string $httpClient, ContainerBuilder $container): void
    {
        $definitionClass = '%' . self::PATH_ALIAS . '.client.class%';
        $internalAlias = sprintf('%s.http.client.%s', self::PATH_ALIAS, $name);

        $definition = new Definition($definitionClass);

        if (null !== $httpClient) {
            $container->setAlias($internalAlias, $httpClient);
            $definition->addArgument(new Reference($httpClient));
        } else {
            $psr18Client = new Definition('Symfony\Component\HttpClient\Psr18Client', [new Reference('http_client')]);
            $definition->addArgument($psr18Client);
        }

        $definition->setFactory(array(DefaultClient::class, 'createWithHttpClient'));
        $definition->addMethodCall('setAuthentication', [$authorizationKey, $applicationKey]);

        $container->setDefinition(
            sprintf('%s.client.%s', self::PATH_ALIAS, $name),
            $definition
        );

        if (null !== $alias) {
            $container->setAlias($alias, sprintf('%s.client.%s', self::PATH_ALIAS, $name));
        }
    }
}