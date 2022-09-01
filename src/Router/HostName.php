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

namespace Mimmi20\Routing\Router;

use Laminas\Router\Exception\InvalidArgumentException;
use Laminas\Router\Http\RouteInterface;
use Laminas\Router\Http\RouteMatch;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Stdlib\RequestInterface as Request;
use Laminas\Uri\Exception\InvalidUriPartException;
use Laminas\Uri\Http;
use Traversable;

use function array_key_exists;
use function array_keys;
use function array_map;
use function array_merge;
use function assert;
use function count;
use function in_array;
use function is_array;
use function is_string;
use function mb_strtolower;
use function method_exists;
use function rawurldecode;
use function rawurlencode;

/**
 * route for Hostnames
 */
final class HostName implements RouteInterface
{
    private string | null $host = null;

    private int | null $port = null;

    /**
     * Default values.
     *
     * @var array<mixed>
     */
    private array $defaults;

    /**
     * List of assembled parameters.
     *
     * @var array<int, string>
     */
    private array $assembledParams = [];

    /**
     * Create a new hostname route.
     *
     * @param array<string>            $hosts
     * @param array<int|string, mixed> $defaults
     */
    public function __construct(private array $hosts = [], array $defaults = [])
    {
        $this->defaults = $defaults;
    }

    /**
     * factory(): defined by RouteInterface interface.
     *
     * @see    \Laminas\Router\RouteInterface::factory()
     *
     * @param array<string, (string|array<int|string, mixed>)>|Traversable<string, mixed>|bool $options
     * @phpstan-param array{host?: string, hosts?: array<int|string, string>, defaults?: array<int|string, mixed>}|Traversable<string, mixed>|bool $options
     *
     * @throws InvalidArgumentException
     * @throws \Laminas\Stdlib\Exception\InvalidArgumentException
     */
    public static function factory($options = []): self
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (!is_array($options)) {
            throw new InvalidArgumentException('Options must be an Array');
        }

        if (!array_key_exists('host', $options) && !array_key_exists('hosts', $options)) {
            throw new InvalidArgumentException('one of config keys "host" or "hosts" is required');
        }

        if (array_key_exists('hosts', $options)) {
            if (!is_array($options['hosts'])) {
                throw new InvalidArgumentException('the config key "hosts" must be an array');
            }

            $hosts = $options['hosts'];
        } else {
            if (!is_string($options['host'])) {
                throw new InvalidArgumentException('the config key "host" must be a string');
            }

            $hosts = [$options['host']];
        }

        if (array_key_exists('defaults', $options)) {
            if (!is_array($options['defaults'])) {
                throw new InvalidArgumentException('the optional config key "defaults" must be an array, if available');
            }

            $defaults = $options['defaults'];
        } else {
            $defaults = [];
        }

        return new self($hosts, $defaults);
    }

    /**
     * match(): defined by RouteInterface interface.
     */
    public function match(Request $request): RouteMatch | null
    {
        if (!method_exists($request, 'getUri')) {
            return null;
        }

        $uri = $request->getUri();
        assert($uri instanceof Http);

        $host = $uri->getHost();

        if (null === $host || !in_array(mb_strtolower($host), array_map('strtolower', $this->hosts), true)) {
            return null;
        }

        $this->port = $uri->getPort();
        $this->host = $host;

        return new RouteMatch(array_merge($this->defaults, ['host' => rawurldecode($host)]));
    }

    /**
     * assemble(): Defined by RouteInterface interface.
     *
     * @see    \Laminas\Router\RouteInterface::assemble()
     *
     * @param array<mixed>             $params
     * @param array<string, bool|Http> $options
     * @phpstan-param array{uri?: bool|Http} $options
     *
     * @throws InvalidUriPartException
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    public function assemble(array $params = [], array $options = []): string
    {
        $this->assembledParams = [];

        if (array_key_exists('uri', $options) && $options['uri'] instanceof Http) {
            if (null !== $this->host) {
                $options['uri']->setHost(rawurlencode($this->host));

                $this->assembledParams[] = 'host';
            } elseif (1 === count($this->hosts)) {
                $keys       = array_keys($this->hosts);
                $host       = $this->hosts[$keys[0]];
                $this->host = $host;

                $options['uri']->setHost(rawurlencode($this->host));

                $this->assembledParams[] = 'host';
            }

            if (null !== $this->port) {
                $options['uri']->setPort($this->port);
                $this->assembledParams[] = 'port';
            }
        }

        // A hostname does not contribute to the path, thus nothing is returned.
        return '';
    }

    /**
     * getAssembledParams(): defined by RouteInterface interface.
     *
     * @see    RouteInterface::getAssembledParams
     *
     * @return array<int, string>
     */
    public function getAssembledParams(): array
    {
        return $this->assembledParams;
    }
}
