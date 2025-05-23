<?php

/**
 * This file is part of the mimmi20/laminas-router-hostname package.
 *
 * Copyright (c) 2021-2025, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20Test\Routing;

use Mimmi20\Routing\ConfigProvider;
use Mimmi20\Routing\Router\HostName;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

final class ConfigProviderTest extends TestCase
{
    /** @throws Exception */
    public function testGetRouteManagerConfig(): void
    {
        $object = new ConfigProvider();
        $config = $object->getRouteManagerConfig();

        self::assertIsArray($config);
        self::assertCount(1, $config);
        self::assertArrayHasKey('factories', $config);

        self::assertIsArray($config['factories']);
        self::assertCount(1, $config['factories']);
        self::assertArrayHasKey(HostName::class, $config['factories']);
    }

    /** @throws Exception */
    public function testInvoke(): void
    {
        $object = new ConfigProvider();
        $config = $object();

        self::assertIsArray($config);
        self::assertCount(2, $config);
        self::assertArrayHasKey('route_manager', $config);
        self::assertArrayHasKey('router', $config);

        self::assertIsArray($config['route_manager']);
        self::assertCount(1, $config['route_manager']);
        self::assertArrayHasKey('factories', $config['route_manager']);

        self::assertIsArray($config['route_manager']['factories']);
        self::assertCount(1, $config['route_manager']['factories']);
        self::assertArrayHasKey(HostName::class, $config['route_manager']['factories']);

        self::assertIsArray($config['router']);
        self::assertCount(1, $config['router']);
        self::assertArrayHasKey('routes', $config['router']);

        self::assertIsArray($config['router']['routes']);
        self::assertCount(0, $config['router']['routes']);
    }
}
