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

namespace Mimmi20Test\Routing\Router;

use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Mimmi20\Routing\Router\HostName;
use Mimmi20\Routing\Router\HostNameFactory;
use Override;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

use function assert;

final class HostNameFactoryTest extends TestCase
{
    private HostNameFactory $object;

    /** @throws void */
    #[Override]
    protected function setUp(): void
    {
        $this->object = new HostNameFactory();
    }

    /** @throws ServiceNotCreatedException */
    public function testInvokeWithoutOptions(): void
    {
        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();
        assert($container instanceof ContainerInterface);

        $this->expectException(ServiceNotCreatedException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('Options must be an Array');

        ($this->object)($container, 'test');
    }

    /** @throws ServiceNotCreatedException */
    public function testInvokeFailed(): void
    {
        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();
        assert($container instanceof ContainerInterface);

        $this->expectException(ServiceNotCreatedException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('an error occured while creating this router');

        ($this->object)($container, 'test', []);
    }

    /**
     * @throws Exception
     * @throws ServiceNotCreatedException
     */
    public function testInvoke(): void
    {
        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();
        assert($container instanceof ContainerInterface);

        self::assertInstanceOf(
            HostName::class,
            ($this->object)($container, 'test', ['host' => 'abc.test']),
        );
    }
}
