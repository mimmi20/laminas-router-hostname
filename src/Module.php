<?php
/**
 * This file is part of the mimmi20/laminas-router-hostname package.
 *
 * Copyright (c) 2021-2023, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\Routing;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\DependencyIndicatorInterface;

final class Module implements ConfigProviderInterface, DependencyIndicatorInterface
{
    /**
     * @return array<string, array<string, array<string, string>>>
     * @phpstan-return array{route_manager: array{factories: array<class-string, class-string>}, router: array{routes: array<mixed>}}
     *
     * @throws void
     */
    public function getConfig(): array
    {
        $provider = new ConfigProvider();

        return [
            'router' => ['routes' => []],
            'route_manager' => $provider->getRouteManagerConfig(),
        ];
    }

    /**
     * Expected to return an array of modules on which the current one depends on
     *
     * @return array<int, string>
     *
     * @throws void
     */
    public function getModuleDependencies(): array
    {
        return ['Laminas\Router'];
    }
}
