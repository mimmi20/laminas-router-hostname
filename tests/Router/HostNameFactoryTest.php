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

namespace Mimmi20Test\Routing\Router;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Mimmi20\Routing\Router\HostName;
use Mimmi20\Routing\Router\HostNameFactory;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

use function assert;

final class HostNameFactoryTest extends TestCase
{
    private HostNameFactory $object;

    /**
     * Erstellt alle notwendigen Objekte
     */
    protected function setUp(): void
    {
        $this->object = new HostNameFactory();
    }

    /**
     * @throws ServiceNotCreatedException
     */
    public function testInvokeFailed(): void
    {
        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();
        assert($container instanceof ContainerInterface);

        $this->expectException(ServiceNotCreatedException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('an error occured while creating this router');

        $this->object->__invoke($container, 'test', []);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws ServiceNotCreatedException
     */
    public function testInvoke(): void
    {
        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();
        assert($container instanceof ContainerInterface);

        self::assertInstanceOf(HostName::class, $this->object->__invoke($container, 'test', ['host' => 'abc.test']));
    }
}
