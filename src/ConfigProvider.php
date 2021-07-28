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

namespace Mimmi20\Routing;

use Mimmi20\Routing\Router\HostName;
use Mimmi20\Routing\Router\HostNameFactory;

final class ConfigProvider
{
    /**
     * Returns configuration from file
     *
     * @return array<string, array<string, array<string, string>>>
     */
    public function __invoke(): array
    {
        return [
            'route_manager' => $this->getRouteManagerConfig(),
            'router' => ['routes' => []],
        ];
    }

    /**
     * Get view helper configuration
     *
     * @return array<string, array<string, string>>
     */
    public function getRouteManagerConfig(): array
    {
        return [
            'factories' => [
                HostName::class => HostNameFactory::class,
            ],
        ];
    }
}
