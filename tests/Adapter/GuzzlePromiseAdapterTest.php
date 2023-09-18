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

namespace Thojou\SimpleApiClient\Tests\Adapter;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface as GuzzlePromiseInterface;
use GuzzleHttp\Promise\RejectedPromise;
use PHPUnit\Framework\TestCase;
use Thojou\SimpleApiClient\Adapter\GuzzlePromiseAdapter;

class GuzzlePromiseAdapterTest extends TestCase
{
    public function testThen(): void
    {
        $fulfilledValue = 'Fulfilled Value';
        $fulfilledPromise = new FulfilledPromise($fulfilledValue);

        $promiseAdapter = new GuzzlePromiseAdapter($fulfilledPromise);
        $result = $promiseAdapter->then(fn ($value) => $value . ' Processed');

        $this->assertInstanceOf(GuzzlePromiseAdapter::class, $result);
        $this->assertSame($fulfilledValue . ' Processed', $result->wait());
    }

    public function testOtherwise(): void
    {
        $reason = 'Rejection Reason';
        $rejectedPromise = new RejectedPromise($reason);

        $promiseAdapter = new GuzzlePromiseAdapter($rejectedPromise);
        $result = $promiseAdapter->otherwise(fn ($reason) => 'Error: ' . $reason);

        $this->assertInstanceOf(GuzzlePromiseAdapter::class, $result);
        $this->assertSame('Error: ' . $reason, $result->wait());
    }

    public function testGetState(): void
    {
        $fulfilledPromise = new FulfilledPromise('Fulfilled Value');

        $promiseAdapter = new GuzzlePromiseAdapter($fulfilledPromise);

        $this->assertSame(GuzzlePromiseInterface::FULFILLED, $promiseAdapter->getState());
    }

    public function testResolve(): void
    {
        $promiseAdapter = new GuzzlePromiseAdapter(new Promise(fn ($resolve) => $resolve('Value')));

        $promiseAdapter->resolve('New Value');

        $this->assertSame('New Value', $promiseAdapter->wait());
    }

    public function testReject(): void
    {
        $promiseAdapter = new GuzzlePromiseAdapter(new Promise(fn ($resolve, $reject) => $reject('Reason')));

        $promiseAdapter->reject('Reason');

        $this->expectExceptionMessage('Reason');
        $promiseAdapter->wait();
    }

    public function testCancel(): void
    {
        $promiseAdapter = new GuzzlePromiseAdapter(new Promise(fn ($resolve) => $resolve('Value')));

        $promiseAdapter->cancel();

        $this->assertSame(GuzzlePromiseInterface::REJECTED, $promiseAdapter->getState());
    }

    public function testWait(): void
    {
        $promiseAdapter = new GuzzlePromiseAdapter(new FulfilledPromise('Fulfilled Value'));

        $result = $promiseAdapter->wait();

        $this->assertSame('Fulfilled Value', $result);
    }
}
