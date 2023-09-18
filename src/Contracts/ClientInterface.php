<?php

declare(strict_types=1);

/*
 * This file is part of PHP Simple Client Api.
 *
 * (c) Thomas Joußen <tjoussen91@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Thojou\SimpleApiClient\Contracts;

use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;

interface ClientInterface
{
    /**
     * @param string               $method
     * @param string               $uri
     * @param array<string, mixed> $options
     *
     * @return PromiseInterface<Psr7ResponseInterface>
     */
    public function send(string $method, string $uri, array $options = []): PromiseInterface;

    /**
     * @param callable $callable
     *
     * @return PromiseInterface<mixed>
     */
    public function promisify(callable $callable): PromiseInterface;

}
