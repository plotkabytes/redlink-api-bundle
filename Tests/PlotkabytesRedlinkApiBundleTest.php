<?php

/*
 * This file is part of the Redlink PHP API Client Symfony Bundle.
 *
 * (c) Mateusz Żyła <mateusz.zylaa@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plotkabytes\RedlinkApiBundle\Tests;

use PHPUnit\Framework\TestCase;
use Plotkabytes\RedlinkApiBundle\PlotkabytesRedlinkApiBundle;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PlotkabytesRedlinkApiBundleTest extends TestCase
{
    /**
     * @return void
     */
    public function testBuild(): void
    {
        $container = $this->getMockBuilder(ContainerBuilder::class)
            ->onlyMethods(['addCompilerPass'])
            ->getMock();

        $container->expects($this->exactly(0))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(CompilerPassInterface::class));

        $bundle = new PlotkabytesRedlinkApiBundle();
        $bundle->build($container);
    }
}