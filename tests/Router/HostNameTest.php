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

use ArrayObject;
use Laminas\Router\Exception\InvalidArgumentException;
use Laminas\Router\Http\RouteMatch;
use Laminas\Stdlib\Request;
use Laminas\Uri\Exception\InvalidUriPartException;
use Laminas\Uri\Http;
use Mimmi20\Routing\Router\HostName;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionProperty;

use function mb_strlen;
use function rawurldecode;

final class HostNameTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testFactoryFailed(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('required config key "host" is missing');

        HostName::factory();
    }

    /**
     * @throws InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testFactoryFailed2(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('required config key "host" is missing');

        HostName::factory([]);
    }

    /**
     * @throws Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws ReflectionException
     */
    public function testFactory(): void
    {
        $host     = 'abc.test';
        $hostname = HostName::factory(['host' => $host]);

        self::assertInstanceOf(HostName::class, $hostname);

        $hostP = new ReflectionProperty($hostname, 'host');
        $hostP->setAccessible(true);

        self::assertSame($host, $hostP->getValue($hostname));

        $defaultsP = new ReflectionProperty($hostname, 'defaults');
        $defaultsP->setAccessible(true);

        self::assertSame([], $defaultsP->getValue($hostname));

        $portP = new ReflectionProperty($hostname, 'port');
        $portP->setAccessible(true);

        self::assertNull($portP->getValue($hostname));
    }

    /**
     * @throws Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws ReflectionException
     */
    public function testFactory2(): void
    {
        $host     = 'abc.test';
        $defaults = ['edf' => 'xyz'];
        $hostname = HostName::factory(['host' => $host, 'defaults' => $defaults]);

        self::assertInstanceOf(HostName::class, $hostname);

        $hostP = new ReflectionProperty($hostname, 'host');
        $hostP->setAccessible(true);

        self::assertSame($host, $hostP->getValue($hostname));

        $defaultsP = new ReflectionProperty($hostname, 'defaults');
        $defaultsP->setAccessible(true);

        self::assertSame($defaults, $defaultsP->getValue($hostname));

        $portP = new ReflectionProperty($hostname, 'port');
        $portP->setAccessible(true);

        self::assertNull($portP->getValue($hostname));
    }

    /**
     * @throws Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws ReflectionException
     */
    public function testFactoryWithArrayAccess(): void
    {
        $host     = 'abc.test';
        $hostname = HostName::factory(new ArrayObject(['host' => $host]));

        self::assertInstanceOf(HostName::class, $hostname);

        $hostP = new ReflectionProperty($hostname, 'host');
        $hostP->setAccessible(true);

        self::assertSame($host, $hostP->getValue($hostname));

        $defaultsP = new ReflectionProperty($hostname, 'defaults');
        $defaultsP->setAccessible(true);

        self::assertSame([], $defaultsP->getValue($hostname));

        $portP = new ReflectionProperty($hostname, 'port');
        $portP->setAccessible(true);

        self::assertNull($portP->getValue($hostname));
    }

    /**
     * @throws Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws ReflectionException
     */
    public function testFactoryWithArrayAccess2(): void
    {
        $host     = 'abc.test';
        $defaults = ['edf' => 'xyz'];
        $hostname = HostName::factory(new ArrayObject(['host' => $host, 'defaults' => $defaults]));

        self::assertInstanceOf(HostName::class, $hostname);

        $hostP = new ReflectionProperty($hostname, 'host');
        $hostP->setAccessible(true);

        self::assertSame($host, $hostP->getValue($hostname));

        $defaultsP = new ReflectionProperty($hostname, 'defaults');
        $defaultsP->setAccessible(true);

        self::assertSame($defaults, $defaultsP->getValue($hostname));

        $portP = new ReflectionProperty($hostname, 'port');
        $portP->setAccessible(true);

        self::assertNull($portP->getValue($hostname));
    }

    /**
     * @throws Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws ReflectionException
     */
    public function testMatch(): void
    {
        $host     = 'abc.test';
        $defaults = ['edf' => 'xyz'];
        $hostname = HostName::factory(new ArrayObject(['host' => $host, 'defaults' => $defaults]));

        self::assertInstanceOf(HostName::class, $hostname);

        $hostP = new ReflectionProperty($hostname, 'host');
        $hostP->setAccessible(true);

        self::assertSame($host, $hostP->getValue($hostname));

        $defaultsP = new ReflectionProperty($hostname, 'defaults');
        $defaultsP->setAccessible(true);

        self::assertSame($defaults, $defaultsP->getValue($hostname));

        $portP = new ReflectionProperty($hostname, 'port');
        $portP->setAccessible(true);

        self::assertNull($portP->getValue($hostname));

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $match = $hostname->match($request);

        self::assertNull($match);
    }

    /**
     * @throws Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws ReflectionException
     */
    public function testMatch2(): void
    {
        $host     = 'abc.test';
        $defaults = ['edf' => 'xyz'];
        $hostname = HostName::factory(new ArrayObject(['host' => $host, 'defaults' => $defaults]));

        self::assertInstanceOf(HostName::class, $hostname);

        $hostP = new ReflectionProperty($hostname, 'host');
        $hostP->setAccessible(true);

        self::assertSame($host, $hostP->getValue($hostname));

        $defaultsP = new ReflectionProperty($hostname, 'defaults');
        $defaultsP->setAccessible(true);

        self::assertSame($defaults, $defaultsP->getValue($hostname));

        $portP = new ReflectionProperty($hostname, 'port');
        $portP->setAccessible(true);

        self::assertNull($portP->getValue($hostname));

        $uri = $this->getMockBuilder(Http::class)
            ->disableOriginalConstructor()
            ->getMock();
        $uri->expects(self::once())
            ->method('getHost')
            ->willReturn(null);
        $uri->expects(self::never())
            ->method('getPort');

        $request = $this->getMockBuilder(\Laminas\Http\Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects(self::once())
            ->method('getUri')
            ->willReturn($uri);

        $match = $hostname->match($request);

        self::assertNull($match);
    }

    /**
     * @throws Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws ReflectionException
     */
    public function testMatch3(): void
    {
        $host     = 'abc.test';
        $port     = 80;
        $defaults = ['edf' => 'xyz'];
        $hostname = HostName::factory(new ArrayObject(['host' => $host, 'defaults' => $defaults]));

        self::assertInstanceOf(HostName::class, $hostname);

        $hostP = new ReflectionProperty($hostname, 'host');
        $hostP->setAccessible(true);

        self::assertSame($host, $hostP->getValue($hostname));

        $defaultsP = new ReflectionProperty($hostname, 'defaults');
        $defaultsP->setAccessible(true);

        self::assertSame($defaults, $defaultsP->getValue($hostname));

        $portP = new ReflectionProperty($hostname, 'port');
        $portP->setAccessible(true);

        self::assertNull($portP->getValue($hostname));

        $uri = $this->getMockBuilder(Http::class)
            ->disableOriginalConstructor()
            ->getMock();
        $uri->expects(self::once())
            ->method('getHost')
            ->willReturn($host);
        $uri->expects(self::once())
            ->method('getPort')
            ->willReturn($port);

        $request = $this->getMockBuilder(\Laminas\Http\Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects(self::once())
            ->method('getUri')
            ->willReturn($uri);

        $match = $hostname->match($request);

        self::assertInstanceOf(RouteMatch::class, $match);

        self::assertSame($defaults + ['host' => rawurldecode($host)], $match->getParams());
        self::assertSame(mb_strlen($host), $match->getLength());

        $portP = new ReflectionProperty($hostname, 'port');
        $portP->setAccessible(true);

        self::assertSame($port, $portP->getValue($hostname));
    }

    /**
     * @throws Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws ReflectionException
     * @throws InvalidUriPartException
     */
    public function testAssemble(): void
    {
        $host     = 'abc.test';
        $defaults = ['edf' => 'xyz'];
        $hostname = HostName::factory(new ArrayObject(['host' => $host, 'defaults' => $defaults]));

        self::assertInstanceOf(HostName::class, $hostname);

        $hostP = new ReflectionProperty($hostname, 'host');
        $hostP->setAccessible(true);

        self::assertSame($host, $hostP->getValue($hostname));

        $defaultsP = new ReflectionProperty($hostname, 'defaults');
        $defaultsP->setAccessible(true);

        self::assertSame($defaults, $defaultsP->getValue($hostname));

        $portP = new ReflectionProperty($hostname, 'port');
        $portP->setAccessible(true);

        self::assertNull($portP->getValue($hostname));

        $url = $hostname->assemble();

        self::assertSame('', $url);
        self::assertSame([], $hostname->getAssembledParams());
    }

    /**
     * @throws Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws ReflectionException
     * @throws InvalidUriPartException
     */
    public function testAssemble2(): void
    {
        $host     = 'abc.test';
        $defaults = ['edf' => 'xyz'];
        $hostname = HostName::factory(new ArrayObject(['host' => $host, 'defaults' => $defaults]));

        self::assertInstanceOf(HostName::class, $hostname);

        $hostP = new ReflectionProperty($hostname, 'host');
        $hostP->setAccessible(true);

        self::assertSame($host, $hostP->getValue($hostname));

        $defaultsP = new ReflectionProperty($hostname, 'defaults');
        $defaultsP->setAccessible(true);

        self::assertSame($defaults, $defaultsP->getValue($hostname));

        $portP = new ReflectionProperty($hostname, 'port');
        $portP->setAccessible(true);

        self::assertNull($portP->getValue($hostname));

        $uri = $this->getMockBuilder(Http::class)
            ->disableOriginalConstructor()
            ->getMock();
        $uri->expects(self::once())
            ->method('setHost')
            ->with($host);
        $uri->expects(self::never())
            ->method('setPort');

        $url = $hostname->assemble([], ['uri' => $uri]);

        self::assertSame('', $url);
        self::assertSame(['host'], $hostname->getAssembledParams());
    }

    /**
     * @throws Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws ReflectionException
     * @throws InvalidUriPartException
     */
    public function testAssemble3(): void
    {
        $host     = 'abc.test';
        $port     = 80;
        $defaults = ['edf' => 'xyz'];
        $hostname = HostName::factory(new ArrayObject(['host' => $host, 'defaults' => $defaults]));

        self::assertInstanceOf(HostName::class, $hostname);

        $hostP = new ReflectionProperty($hostname, 'host');
        $hostP->setAccessible(true);

        self::assertSame($host, $hostP->getValue($hostname));

        $defaultsP = new ReflectionProperty($hostname, 'defaults');
        $defaultsP->setAccessible(true);

        self::assertSame($defaults, $defaultsP->getValue($hostname));

        $portP = new ReflectionProperty($hostname, 'port');
        $portP->setAccessible(true);

        self::assertNull($portP->getValue($hostname));

        $uri = $this->getMockBuilder(Http::class)
            ->disableOriginalConstructor()
            ->getMock();
        $uri->expects(self::once())
            ->method('getHost')
            ->willReturn($host);
        $uri->expects(self::once())
            ->method('getPort')
            ->willReturn($port);
        $uri->expects(self::once())
            ->method('setHost')
            ->with($host);
        $uri->expects(self::once())
            ->method('setPort')
            ->with($port);

        $request = $this->getMockBuilder(\Laminas\Http\Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects(self::once())
            ->method('getUri')
            ->willReturn($uri);

        $match = $hostname->match($request);

        self::assertInstanceOf(RouteMatch::class, $match);

        $portP = new ReflectionProperty($hostname, 'port');
        $portP->setAccessible(true);

        self::assertSame($port, $portP->getValue($hostname));

        $url = $hostname->assemble([], ['uri' => $uri]);

        self::assertSame('', $url);
        self::assertSame(['host', 'port'], $hostname->getAssembledParams());
    }
}
