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
use function array_merge;
use function is_array;
use function method_exists;
use function rawurldecode;
use function rawurlencode;

/**
 * route for Hostnames
 */
final class HostName implements RouteInterface
{
    private string $host = '';

    private ?int $port = null;

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
     * @param array<mixed> $defaults
     */
    public function __construct(string $host, array $defaults = [])
    {
        $this->host     = $host;
        $this->defaults = $defaults;
    }

    /**
     * factory(): defined by RouteInterface interface.
     *
     * @see    \Laminas\Router\RouteInterface::factory()
     *
     * @param array<string, (string|array<mixed>)>|Traversable<string, (string|array<mixed>)>|bool $options
     * @phpstan-param array{host?: string, defaults?: array<mixed>}|Traversable<string, (string|array<mixed>)>|bool $options
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

        if (!array_key_exists('host', $options)) {
            throw new InvalidArgumentException('required config key "host" is missing');
        }

        return new self(
            $options['host'],
            $options['defaults'] ?? []
        );
    }

    /**
     * match(): defined by RouteInterface interface.
     */
    public function match(Request $request): ?RouteMatch
    {
        if (!method_exists($request, 'getUri')) {
            return null;
        }

        $uri  = $request->getUri();
        $host = $uri->getHost();

        if ($host !== $this->host) {
            return null;
        }

        $this->port = $uri->getPort();

        return new RouteMatch(
            array_merge(
                $this->defaults,
                [
                    'host' => rawurldecode($host),
                ]
            )
        );
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

        if (isset($options['uri']) && $options['uri'] instanceof Http && null !== $this->host) {
            $options['uri']->setHost(rawurlencode($this->host));

            $this->assembledParams[] = 'host';

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
