<?php

/**
 * This file is part of the mimmi20/laminas-router-hostname package.
 *
 * Copyright (c) 2021-2024, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\Routing\Router;

use Laminas\Router\Exception\InvalidArgumentException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Override;
use Psr\Container\ContainerInterface;

use function is_array;

final class HostNameFactory implements FactoryInterface
{
    /**
     * @param string                                                $requestedName
     * @param array<string, (array<int|string, mixed>|string)>|null $options
     * @phpstan-param array{host?: string, hosts?: array<int|string, string>, defaults?: array<int|string, mixed>}|null $options
     *
     * @throws ServiceNotCreatedException if an exception is raised when creating a service
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    #[Override]
    public function __invoke(ContainerInterface $container, $requestedName, array | null $options = null): HostName
    {
        if (!is_array($options)) {
            throw new ServiceNotCreatedException('Options must be an Array');
        }

        try {
            return HostName::factory($options);
        } catch (InvalidArgumentException $e) {
            throw new ServiceNotCreatedException('an error occured while creating this router', 0, $e);
        }
    }
}
