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

use function mb_strtoupper;
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
        $this->expectExceptionMessage('one of config keys "host" or "hosts" is required');

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
        $this->expectExceptionMessage('one of config keys "host" or "hosts" is required');

        HostName::factory([]);
    }

    /**
     * @throws InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testFactoryFailed3(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('Options must be an Array');

        HostName::factory(true);
    }

    /**
     * @throws Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws ReflectionException
     */
    public function testFactoryWithOneHost(): void
    {
        $host     = 'abc.test';
        $hostname = HostName::factory(['host' => $host]);

        self::assertInstanceOf(HostName::class, $hostname);

        $hostsP = new ReflectionProperty($hostname, 'hosts');
        $hostsP->setAccessible(true);

        self::assertSame([$host], $hostsP->getValue($hostname));

        $hostP = new ReflectionProperty($hostname, 'host');
        $hostP->setAccessible(true);

        self::assertNull($hostP->getValue($hostname));

        $defaultsP = new ReflectionProperty($hostname, 'defaults');
        $defaultsP->setAccessible(true);

        self::assertSame([], $defaultsP->getValue($hostname));

        $portP = new ReflectionProperty($hostname, 'port');
        $portP->setAccessible(true);

        self::assertNull($portP->getValue($hostname));
    }

    /**
     * @throws InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testFactoryWithOneWrongHost(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('the config key "host" must be a string');

        HostName::factory(['host' => 1234]);
    }

    /**
     * @throws Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws ReflectionException
     */
    public function testFactoryWithOneHostAndDefaults(): void
    {
        $host     = 'abc.test';
        $defaults = ['edf' => 'xyz'];
        $hostname = HostName::factory(['host' => $host, 'defaults' => $defaults]);

        self::assertInstanceOf(HostName::class, $hostname);

        $hostsP = new ReflectionProperty($hostname, 'hosts');
        $hostsP->setAccessible(true);

        self::assertSame([$host], $hostsP->getValue($hostname));

        $hostP = new ReflectionProperty($hostname, 'host');
        $hostP->setAccessible(true);

        self::assertNull($hostP->getValue($hostname));

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
    public function testFactoryWithOneHostAndArrayAccess(): void
    {
        $host     = 'abc.test';
        $hostname = HostName::factory(new ArrayObject(['host' => $host]));

        self::assertInstanceOf(HostName::class, $hostname);

        $hostsP = new ReflectionProperty($hostname, 'hosts');
        $hostsP->setAccessible(true);

        self::assertSame([$host], $hostsP->getValue($hostname));

        $hostP = new ReflectionProperty($hostname, 'host');
        $hostP->setAccessible(true);

        self::assertNull($hostP->getValue($hostname));

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
    public function testFactoryWithOneHostAndDefaultsAndArrayAccess(): void
    {
        $host     = 'abc.test';
        $defaults = ['edf' => 'xyz'];
        $hostname = HostName::factory(new ArrayObject(['host' => $host, 'defaults' => $defaults]));

        self::assertInstanceOf(HostName::class, $hostname);

        $hostsP = new ReflectionProperty($hostname, 'hosts');
        $hostsP->setAccessible(true);

        self::assertSame([$host], $hostsP->getValue($hostname));

        $hostP = new ReflectionProperty($hostname, 'host');
        $hostP->setAccessible(true);

        self::assertNull($hostP->getValue($hostname));

        $defaultsP = new ReflectionProperty($hostname, 'defaults');
        $defaultsP->setAccessible(true);

        self::assertSame($defaults, $defaultsP->getValue($hostname));

        $portP = new ReflectionProperty($hostname, 'port');
        $portP->setAccessible(true);

        self::assertNull($portP->getValue($hostname));
    }

    /**
     * @throws InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testFactoryWithWrongHosts(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('the config key "hosts" must be an array');

        HostName::factory(['hosts' => 1234]);
    }

    /**
     * @throws Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws ReflectionException
     */
    public function testFactoryWithTwoHosts(): void
    {
        $hostOne  = 'abc.test';
        $hostTow  = 'abc2.test';
        $hostname = HostName::factory(['hosts' => [$hostOne, $hostTow]]);

        self::assertInstanceOf(HostName::class, $hostname);

        $hostsP = new ReflectionProperty($hostname, 'hosts');
        $hostsP->setAccessible(true);

        self::assertSame([$hostOne, $hostTow], $hostsP->getValue($hostname));

        $hostP = new ReflectionProperty($hostname, 'host');
        $hostP->setAccessible(true);

        self::assertNull($hostP->getValue($hostname));

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
    public function testFactoryWithTwoHostsAndArrayAccess(): void
    {
        $hostOne  = 'abc.test';
        $hostTow  = 'abc2.test';
        $hostname = HostName::factory(new ArrayObject(['hosts' => [$hostOne, $hostTow]]));

        self::assertInstanceOf(HostName::class, $hostname);

        $hostsP = new ReflectionProperty($hostname, 'hosts');
        $hostsP->setAccessible(true);

        self::assertSame([$hostOne, $hostTow], $hostsP->getValue($hostname));

        $hostP = new ReflectionProperty($hostname, 'host');
        $hostP->setAccessible(true);

        self::assertNull($hostP->getValue($hostname));

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
    public function testMatchWithoutUri(): void
    {
        $host     = 'abc.test';
        $defaults = ['edf' => 'xyz'];
        $hostname = HostName::factory(new ArrayObject(['host' => $host, 'defaults' => $defaults]));

        self::assertInstanceOf(HostName::class, $hostname);

        $hostsP = new ReflectionProperty($hostname, 'hosts');
        $hostsP->setAccessible(true);

        self::assertSame([$host], $hostsP->getValue($hostname));

        $hostP = new ReflectionProperty($hostname, 'host');
        $hostP->setAccessible(true);

        self::assertNull($hostP->getValue($hostname));

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
    public function testMatchWithNullUri(): void
    {
        $host     = 'abc.test';
        $defaults = ['edf' => 'xyz'];
        $hostname = HostName::factory(new ArrayObject(['host' => $host, 'defaults' => $defaults]));

        self::assertInstanceOf(HostName::class, $hostname);

        $hostsP = new ReflectionProperty($hostname, 'hosts');
        $hostsP->setAccessible(true);

        self::assertSame([$host], $hostsP->getValue($hostname));

        $hostP = new ReflectionProperty($hostname, 'host');
        $hostP->setAccessible(true);

        self::assertNull($hostP->getValue($hostname));

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
    public function testMatchWithUri(): void
    {
        $host     = 'abc.test';
        $port     = 80;
        $defaults = ['edf' => 'xyz'];
        $hostname = HostName::factory(new ArrayObject(['host' => $host, 'defaults' => $defaults]));

        self::assertInstanceOf(HostName::class, $hostname);

        $hostsP = new ReflectionProperty($hostname, 'hosts');
        $hostsP->setAccessible(true);

        self::assertSame([$host], $hostsP->getValue($hostname));

        $hostP = new ReflectionProperty($hostname, 'host');
        $hostP->setAccessible(true);

        self::assertNull($hostP->getValue($hostname));

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
        self::assertSame(0, $match->getLength());

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
    public function testAssembleWithoutUri(): void
    {
        $host     = 'abc.test';
        $defaults = ['edf' => 'xyz'];
        $hostname = HostName::factory(new ArrayObject(['host' => $host, 'defaults' => $defaults]));

        self::assertInstanceOf(HostName::class, $hostname);

        $hostsP = new ReflectionProperty($hostname, 'hosts');
        $hostsP->setAccessible(true);

        self::assertSame([$host], $hostsP->getValue($hostname));

        $hostP = new ReflectionProperty($hostname, 'host');
        $hostP->setAccessible(true);

        self::assertNull($hostP->getValue($hostname));

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
    public function testAssembleWithUriAndOneHost(): void
    {
        $host     = 'abc.test';
        $defaults = ['edf' => 'xyz'];
        $hostname = HostName::factory(new ArrayObject(['host' => $host, 'defaults' => $defaults]));

        self::assertInstanceOf(HostName::class, $hostname);

        $hostsP = new ReflectionProperty($hostname, 'hosts');
        $hostsP->setAccessible(true);

        self::assertSame([$host], $hostsP->getValue($hostname));

        $hostP = new ReflectionProperty($hostname, 'host');
        $hostP->setAccessible(true);

        self::assertNull($hostP->getValue($hostname));

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
    public function testAssembleWithUriAndSingleHost(): void
    {
        $host     = 'abc.test';
        $defaults = ['edf' => 'xyz'];
        $hostname = HostName::factory(new ArrayObject(['hosts' => [$host], 'defaults' => $defaults]));

        self::assertInstanceOf(HostName::class, $hostname);

        $hostsP = new ReflectionProperty($hostname, 'hosts');
        $hostsP->setAccessible(true);

        self::assertSame([$host], $hostsP->getValue($hostname));

        $hostP = new ReflectionProperty($hostname, 'host');
        $hostP->setAccessible(true);

        self::assertNull($hostP->getValue($hostname));

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
    public function testAssembleWithUriAfterMatch(): void
    {
        $host     = 'abc.test';
        $port     = 80;
        $defaults = ['edf' => 'xyz'];
        $hostname = HostName::factory(new ArrayObject(['host' => $host, 'defaults' => $defaults]));

        self::assertInstanceOf(HostName::class, $hostname);

        $hostsP = new ReflectionProperty($hostname, 'hosts');
        $hostsP->setAccessible(true);

        self::assertSame([$host], $hostsP->getValue($hostname));

        $hostP = new ReflectionProperty($hostname, 'host');
        $hostP->setAccessible(true);

        self::assertNull($hostP->getValue($hostname));

        $defaultsP = new ReflectionProperty($hostname, 'defaults');
        $defaultsP->setAccessible(true);

        self::assertSame($defaults, $defaultsP->getValue($hostname));

        $portP = new ReflectionProperty($hostname, 'port');
        $portP->setAccessible(true);

        self::assertNull($portP->getValue($hostname));

        $uri1 = $this->getMockBuilder(Http::class)
            ->disableOriginalConstructor()
            ->getMock();
        $uri1->expects(self::once())
            ->method('getHost')
            ->willReturn($host);
        $uri1->expects(self::once())
            ->method('getPort')
            ->willReturn($port);

        $uri2 = $this->getMockBuilder(Http::class)
            ->disableOriginalConstructor()
            ->getMock();
        $uri2->expects(self::once())
            ->method('setHost')
            ->with($host);
        $uri2->expects(self::once())
            ->method('setPort')
            ->with($port);

        $request = $this->getMockBuilder(\Laminas\Http\Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects(self::once())
            ->method('getUri')
            ->willReturn($uri1);

        $match = $hostname->match($request);

        self::assertInstanceOf(RouteMatch::class, $match);

        $portP = new ReflectionProperty($hostname, 'port');
        $portP->setAccessible(true);

        self::assertSame($port, $portP->getValue($hostname));

        $url = $hostname->assemble([], ['uri' => $uri2]);

        self::assertSame('', $url);
        self::assertSame(['host', 'port'], $hostname->getAssembledParams());
    }

    /**
     * @throws Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws ReflectionException
     * @throws InvalidUriPartException
     */
    public function testAssembleWithUriAfterMatch2(): void
    {
        $host     = 'abc.test';
        $port     = 80;
        $defaults = ['edf' => 'xyz'];
        $hostname = HostName::factory(new ArrayObject(['host' => mb_strtoupper($host), 'defaults' => $defaults]));

        self::assertInstanceOf(HostName::class, $hostname);

        $hostsP = new ReflectionProperty($hostname, 'hosts');
        $hostsP->setAccessible(true);

        self::assertSame([mb_strtoupper($host)], $hostsP->getValue($hostname));

        $hostP = new ReflectionProperty($hostname, 'host');
        $hostP->setAccessible(true);

        self::assertNull($hostP->getValue($hostname));

        $defaultsP = new ReflectionProperty($hostname, 'defaults');
        $defaultsP->setAccessible(true);

        self::assertSame($defaults, $defaultsP->getValue($hostname));

        $portP = new ReflectionProperty($hostname, 'port');
        $portP->setAccessible(true);

        self::assertNull($portP->getValue($hostname));

        $uri1 = $this->getMockBuilder(Http::class)
            ->disableOriginalConstructor()
            ->getMock();
        $uri1->expects(self::once())
            ->method('getHost')
            ->willReturn($host);
        $uri1->expects(self::once())
            ->method('getPort')
            ->willReturn($port);

        $uri2 = $this->getMockBuilder(Http::class)
            ->disableOriginalConstructor()
            ->getMock();
        $uri2->expects(self::once())
            ->method('setHost')
            ->with($host);
        $uri2->expects(self::once())
            ->method('setPort')
            ->with($port);

        $request = $this->getMockBuilder(\Laminas\Http\Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects(self::once())
            ->method('getUri')
            ->willReturn($uri1);

        $match = $hostname->match($request);

        self::assertInstanceOf(RouteMatch::class, $match);

        $portP = new ReflectionProperty($hostname, 'port');
        $portP->setAccessible(true);

        self::assertSame($port, $portP->getValue($hostname));

        $url = $hostname->assemble([], ['uri' => $uri2]);

        self::assertSame('', $url);
        self::assertSame(['host', 'port'], $hostname->getAssembledParams());
    }

    /**
     * @throws Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     * @throws ReflectionException
     * @throws InvalidUriPartException
     */
    public function testAssembleWithBoolUri(): void
    {
        $host     = 'abc.test';
        $defaults = ['edf' => 'xyz'];
        $hostname = HostName::factory(new ArrayObject(['host' => $host, 'defaults' => $defaults]));

        self::assertInstanceOf(HostName::class, $hostname);

        $hostsP = new ReflectionProperty($hostname, 'hosts');
        $hostsP->setAccessible(true);

        self::assertSame([$host], $hostsP->getValue($hostname));

        $hostP = new ReflectionProperty($hostname, 'host');
        $hostP->setAccessible(true);

        self::assertNull($hostP->getValue($hostname));

        $defaultsP = new ReflectionProperty($hostname, 'defaults');
        $defaultsP->setAccessible(true);

        self::assertSame($defaults, $defaultsP->getValue($hostname));

        $portP = new ReflectionProperty($hostname, 'port');
        $portP->setAccessible(true);

        self::assertNull($portP->getValue($hostname));

        $url = $hostname->assemble([], ['uri' => true]);

        self::assertSame('', $url);
        self::assertSame([], $hostname->getAssembledParams());
    }

    /**
     * @throws InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testFactoryWithWrongDefaults(): void
    {
        $host = 'abc.test';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('the optional config key "defaults" must be an array, if available');

        HostName::factory(['host' => $host, 'defaults' => 1234]);
    }
}
