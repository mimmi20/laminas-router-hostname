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

namespace Mimmi20\Routing;

use Mimmi20\Routing\Router\HostName;
use Mimmi20\Routing\Router\HostNameFactory;

final class ConfigProvider
{
    /**
     * Returns configuration from file
     *
     * @return array<string, array<string, array<string, string>>>
     * @phpstan-return array{route_manager: array{factories: array<class-string, class-string>}, router: array{routes: array<mixed>}}
     *
     * @throws void
     */
    public function __invoke(): array
    {
        return [
            'router' => ['routes' => []],
            'route_manager' => $this->getRouteManagerConfig(),
        ];
    }

    /**
     * Get view helper configuration
     *
     * @return array<string, array<string, string>>
     * @phpstan-return array{factories: array<class-string, class-string>}
     *
     * @throws void
     */
    public function getRouteManagerConfig(): array
    {
        return ['factories' => [HostName::class => HostNameFactory::class]];
    }
}
