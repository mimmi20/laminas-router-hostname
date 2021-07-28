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

use Laminas\Router\Exception\InvalidArgumentException;
use Mimmi20\Routing\Router\HostName;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

final class HostNameTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testFactoryFailed(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required config key "host" is missing');

        HostName::factory();
    }

    /**
     * @throws Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public function testFactory(): void
    {
        self::assertInstanceOf(HostName::class, HostName::factory(['host' => 'abc.test']));
    }
}
