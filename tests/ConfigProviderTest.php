<?php
/**
 * This file is part of the mimmi20/laminas-router-hostname package.
 *
 * Copyright (c) 2021, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20Test\Routing;

use Mimmi20\Routing\ConfigProvider;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

final class ConfigProviderTest extends TestCase
{
    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testGetRouteManagerConfig(): void
    {
        $object = new ConfigProvider();
        $config = $object->getRouteManagerConfig();

        self::assertIsArray($config);
        self::assertCount(1, $config);
        self::assertArrayHasKey('factories', $config);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testInvoke(): void
    {
        $object = new ConfigProvider();
        $config = $object();

        self::assertIsArray($config);
        self::assertCount(2, $config);
        self::assertArrayHasKey('route_manager', $config);
        self::assertArrayHasKey('router', $config);
    }
}
