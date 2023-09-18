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

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\Promise;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Thojou\SimpleApiClient\Adapter\GuzzleClientAdapter;
use Thojou\SimpleApiClient\Adapter\GuzzlePromiseAdapter;

class GuzzleClientAdapterTest extends TestCase
{
    public function testSend(): void
    {
        $method = 'GET';
        $uri = 'https://example.com';
        $options = ['headers' => ['Authorization' => 'Bearer token']];

        $guzzleClientMock = $this->createMock(ClientInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);

        $guzzleClientMock
            ->expects($this->once())
            ->method('requestAsync')
            ->with($method, $uri, $options)
            ->willReturn(new FulfilledPromise($responseMock));

        $guzzleAdapter = new GuzzleClientAdapter($guzzleClientMock);
        $result = $guzzleAdapter->send($method, $uri, $options);

        $this->assertInstanceOf(GuzzlePromiseAdapter::class, $result);
        $this->assertSame($responseMock, $result->wait());
    }

    public function testPromisify(): void
    {
        $callable = function () {
            return new Promise(function () {});
        };

        $guzzleAdapter = new GuzzleClientAdapter($this->createMock(ClientInterface::class));

        $result = $guzzleAdapter->promisify($callable);
        $this->assertInstanceOf(GuzzlePromiseAdapter::class, $result);
    }
}
