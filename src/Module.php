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

use Laminas\ModuleManager\Feature\ConfigProviderInterface;

final class Module implements ConfigProviderInterface
{
    /**
     * @return array<string, array<string, array<string, string>>>
     */
    public function getConfig(): array
    {
        $provider = new ConfigProvider();

        return [
            'route_manager' => $provider->getRouteManagerConfig(),
            'router' => ['routes' => []],
        ];
    }
}
