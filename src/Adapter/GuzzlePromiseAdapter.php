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

namespace Thojou\SimpleApiClient\Adapter;

use Exception;
use GuzzleHttp\Promise\PromiseInterface as GuzzlePromiseInterface;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;
use Thojou\SimpleApiClient\Contracts\PromiseInterface;

/**
 * @template T as mixed
 * @implements PromiseInterface<T>
 */
class GuzzlePromiseAdapter implements PromiseInterface
{
    public function __construct(
        private readonly GuzzlePromiseInterface $promise
    ) {
    }

    /**
     * @param callable|null $onFulfilled
     * @param callable|null $onRejected
     *
     * @return GuzzlePromiseAdapter<mixed>
     */
    public function then(callable $onFulfilled = null, callable $onRejected = null): GuzzlePromiseAdapter
    {
        return new GuzzlePromiseAdapter($this->promise->then($onFulfilled, $onRejected));
    }

    /**
     * @param callable $onRejected
     *
     * @return GuzzlePromiseAdapter<Exception>
     */
    public function otherwise(callable $onRejected): GuzzlePromiseAdapter
    {
        return new GuzzlePromiseAdapter($this->promise->otherwise($onRejected));
    }

    public function getState(): string
    {
        return $this->promise->getState();
    }

    public function resolve($value): void
    {
        $this->promise->resolve($value);
    }

    public function reject($reason): void
    {
        $this->promise->reject($reason);
    }

    public function cancel(): void
    {
        $this->promise->cancel();
    }

    public function wait(bool $unwrap = true): mixed
    {
        return $this->promise->wait($unwrap);
    }
}
