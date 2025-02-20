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

namespace Mimmi20\Routing\Router;

use Laminas\Router\Exception\InvalidArgumentException;
use Laminas\Router\Http\RouteInterface;
use Laminas\Router\Http\RouteMatch;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Stdlib\RequestInterface as Request;
use Laminas\Uri\Exception\InvalidUriPartException;
use Laminas\Uri\Http;
use Override;
use Traversable;

use function array_key_exists;
use function array_keys;
use function array_map;
use function assert;
use function count;
use function in_array;
use function is_array;
use function is_string;
use function mb_strtolower;
use function method_exists;
use function rawurldecode;
use function rawurlencode;
use function sprintf;

/**
 * route for Hostnames
 */
final class HostName implements RouteInterface
{
    private string | null $host = null;
    private int | null $port    = null;

    /**
     * List of assembled parameters.
     *
     * @var array<int, string>
     */
    private array $assembledParams = [];

    /**
     * Create a new hostname route.
     *
     * @param array<int|string, string> $hosts
     * @param array<int|string, mixed>  $defaults
     *
     * @throws void
     */
    public function __construct(private readonly array $hosts = [], private readonly array $defaults = [])
    {
        // nothing to do here
    }

    /**
     * factory(): defined by RouteInterface interface.
     *
     * @see    \Laminas\Router\RouteInterface::factory()
     *
     * @param bool|iterable<string, (array<int|string, mixed>|string)> $options
     *
     * @throws InvalidArgumentException
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    #[Override]
    public static function factory($options = []): self
    {
        if ($options instanceof Traversable) {
            /** @phpstan-ignore-next-line */
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
                throw new InvalidArgumentException(
                    'the optional config key "defaults" must be an array, if available',
                );
            }

            $defaults = $options['defaults'];
        } else {
            $defaults = [];
        }

        return new self($hosts, $defaults);
    }

    /**
     * match(): defined by RouteInterface interface.
     *
     * @throws void
     */
    #[Override]
    public function match(Request $request): RouteMatch | null
    {
        if (!method_exists($request, 'getUri')) {
            return null;
        }

        $uri = $request->getUri();
        assert($uri instanceof Http);

        $host = $uri->getHost();

        if (
            $host === null
            || !in_array(mb_strtolower($host), array_map('strtolower', $this->hosts), true)
        ) {
            return null;
        }

        $this->port = $uri->getPort();
        $this->host = $host;

        return new RouteMatch([...$this->defaults, 'host' => rawurldecode($host)]);
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
     * @throws InvalidArgumentException
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    #[Override]
    public function assemble(array $params = [], array $options = []): string
    {
        $this->assembledParams = [];

        if (array_key_exists('uri', $options) && $options['uri'] instanceof Http) {
            if ($this->host !== null) {
                try {
                    $options['uri']->setHost(rawurlencode($this->host));
                } catch (InvalidUriPartException $e) {
                    throw new InvalidArgumentException(
                        sprintf('Could not set host %s', $this->host),
                        0,
                        $e,
                    );
                }

                $this->assembledParams[] = 'host';
            } elseif (count($this->hosts) === 1) {
                $keys       = array_keys($this->hosts);
                $this->host = $this->hosts[$keys[0]];

                try {
                    $options['uri']->setHost(rawurlencode($this->host));
                } catch (InvalidUriPartException $e) {
                    throw new InvalidArgumentException(
                        sprintf('Could not set host %s', $this->host),
                        0,
                        $e,
                    );
                }

                $this->assembledParams[] = 'host';
            }

            if ($this->port !== null) {
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
     *
     * @throws void
     */
    #[Override]
    public function getAssembledParams(): array
    {
        return $this->assembledParams;
    }
}
