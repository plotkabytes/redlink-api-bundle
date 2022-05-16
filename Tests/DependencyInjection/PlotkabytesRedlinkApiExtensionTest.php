<?php

/*
 * This file is part of the Redlink PHP API Client Symfony Bundle.
 *
 * (c) Mateusz Żyła <mateusz.zylaa@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plotkabytes\RedlinkApiBundle\Tests\DependencyInjection;

use Http\Client\HttpClient;
use PHPUnit\Framework\TestCase;
use Plotkabytes\RedlinkApi\DefaultClient;
use Plotkabytes\RedlinkApiBundle\DependencyInjection\PlotkabytesRedlinkApiExtension;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpClient\MockHttpClient;

class PlotkabytesRedlinkApiExtensionTest extends TestCase
{

    /**
     * @var ContainerBuilder
     */
    private $container;
    /**
     * @var PlotkabytesRedlinkApiExtension
     */
    private $extension;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->container = new ContainerBuilder();
        $this->container->setDefinition('http_client', new Definition(MockHttpClient::class));
        $this->extension = new PlotkabytesRedlinkApiExtension();
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->container, $this->extension);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testCreateClients(): void
    {
        $prefix = 'plotkabytes_redlink_api';

        $config = array(
            $prefix => array('clients' => array(
                'firstclient' => array(
                    'authorization_key' => '12345',
                    'application_key' => '12345'
                ),
                'secondclient' => array(
                    'authorization_key' => '123456',
                    'application_key' => '123456'
                )
            )),
        );

        $this->extension->load($config, $this->container);
        $this->assertTrue($this->container->hasAlias($prefix . '.client.default'), 'missing alias .client.default');
        $this->assertTrue($this->container->has($prefix . '.client.firstclient'), 'missing .client.firstclient');
        $this->assertTrue($this->container->has($prefix . '.client.secondclient'), 'missing .client.secondclient');

        $this->assertInstanceOf(DefaultClient::class, $this->container->get($prefix . '.client.default'));
        $this->assertInstanceOf(DefaultClient::class, $this->container->get($prefix . '.client.firstclient'));
        $this->assertInstanceOf(DefaultClient::class, $this->container->get($prefix . '.client.secondclient'));

        $this->assertNotSame(
            $this->container->get($prefix . '.client.firstclient'),
            $this->container->get($prefix . '.client.secondclient'),
            '.client.secondclient is not same as .client.firstclient'
        );
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testWrongAuth(): void
    {
        $prefix = 'plotkabytes_redlink_api';

        $this->expectException(InvalidConfigurationException::class);

        $config = array(
            $prefix => array('clients' => array(
                'firstclient' => array()
            )),
        );

        $this->extension->load($config, $this->container);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testClientAlias(): void
    {

        $prefix = 'plotkabytes_redlink_api';

        $alias = 'test.client';

        $config = array(
            $prefix => array('clients' => array(
                'firstclient' => array(
                    'authorization_key' => '12345',
                    'application_key' => '12345',
                    'alias' => $alias
                )
            )),
        );

        $this->extension->load($config, $this->container);
        $this->assertTrue($this->container->has(DefaultClient::class), 'DefaultClient::class missing');
        $this->assertTrue($this->container->has($prefix . '.client.default'), '.client.default missing');
        $this->assertTrue($this->container->has($prefix . '.client.firstclient'), '.client.firstclient missing ');
        $this->assertTrue($this->container->has($alias), 'alias missing');

        $this->assertSame(
            $this->container->get($prefix . '.client.firstclient'),
            $this->container->get($prefix . '.client.default'),
            'default client is not same as firstclient'
        );

        $this->assertSame(
            $this->container->get(DefaultClient::class),
            $this->container->get($prefix . '.client.default'),
            'default client class is not same as firstclient'
        );

        $this->assertSame(
            $this->container->get($prefix . '.client.firstclient'),
            $this->container->get($prefix),
            'default client class is not same as prefix class'
        );

        $this->assertSame(
            $this->container->get($prefix . '.client.firstclient'),
            $this->container->get($alias),
            'default client class is not same as alias class'
        );
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testHttpClients(): void
    {
        $prefix = 'plotkabytes_redlink_api';

        $config = array(
            $prefix => array('clients' => array(
                'firstclient' => array(
                    'authorization_key' => '12345',
                    'application_key' => '12345',
                    'http_client' => 'http.client',
                ),
                'secondclient' => array(
                    'authorization_key' => '123456',
                    'application_key' => '123456'
                )
            )),
        );

        $httpClient = $this->createMock(HttpClient::class);
        $this->container->setDefinition('http.client', new Definition(HttpClient::class));
        $this->container->set('http.client', $httpClient);

        $this->extension->load($config, $this->container);

        /**
         * @var DefaultClient
         */
        $firstClient = $this->container->get($prefix . '.client.firstclient');

        /**
         * @var DefaultClient
         */
        $secondClient = $this->container->get($prefix . '.client.secondclient');

        $this->assertInstanceOf(
            HttpClient::class,
            $firstClient->getHttpClient()
        );

        $this->assertInstanceOf(
            HttpClient::class,
            $secondClient->getHttpClient()
        );
    }
}