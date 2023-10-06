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

namespace Thojou\SimpleApiClient\Tests;

use PHPUnit\Framework\TestCase;
use Thojou\SimpleApiClient\AbstractApi;
use Thojou\SimpleApiClient\Contracts\ClientFactoryInterface;
use Thojou\SimpleApiClient\Contracts\ClientInterface;
use Thojou\SimpleApiClient\Contracts\PromiseInterface;
use Thojou\SimpleApiClient\Contracts\RequestInterface;
use Thojou\SimpleApiClient\Enums\BodyFormat;
use Thojou\SimpleApiClient\Enums\RequestMethod;
use Psr\Http\Message\ResponseInterface;
use Thojou\SimpleApiClient\Tests\Fixture\DecoratedApiFixture;

class AbstractApiTest extends TestCase
{
    public function testSend(): void
    {
        $clientFactoryMock = $this->createMock(ClientFactoryInterface::class);
        $requestMock = $this->createMock(RequestInterface::class);
        $clientMock = $this->createMock(ClientInterface::class);

        $apiInstance = $this->getMockBuilder(AbstractApi::class)
            ->onlyMethods(['onSuccessResponse', 'onRedirectResponse', 'onErrorResponse'])
            ->setConstructorArgs([$clientFactoryMock])
            ->getMock();

        $clientFactoryMock
            ->method('create')
            ->willReturn($clientMock);

        $requestMock
            ->method('getMethod')
            ->willReturn(RequestMethod::GET);

        $apiInstance->send($requestMock);

        $this->assertTrue(true);
    }

    public function testSendAsync(): void
    {
        $clientFactoryMock = $this->createMock(ClientFactoryInterface::class);
        $requestMock = $this->createMock(RequestInterface::class);
        $clientMock = $this->createMock(ClientInterface::class);

        $apiInstance = $this->getMockBuilder(AbstractApi::class)
            ->onlyMethods(['onSuccessResponse', 'onRedirectResponse', 'onErrorResponse'])
            ->setConstructorArgs([$clientFactoryMock])
            ->getMock();

        $clientFactoryMock
            ->method('create')
            ->willReturn($clientMock);

        $requestMock
            ->method('getMethod')
            ->willReturn(RequestMethod::GET);

        $result = $apiInstance->sendAsync($requestMock);

        $this->assertInstanceOf(PromiseInterface::class, $result);
    }

    public function testResolveRequestOptionsWithEmptyBody(): void
    {
        $clientFactoryMock = $this->createMock(ClientFactoryInterface::class);
        $requestMock = $this->createMock(RequestInterface::class);
        $clientMock = $this->createMock(ClientInterface::class);

        $apiInstance = $this->getMockBuilder(DecoratedApiFixture::class)
            ->onlyMethods(['onSuccessResponse', 'onRedirectResponse', 'onErrorResponse'])
            ->setConstructorArgs([$clientFactoryMock])
            ->getMock();

        $clientFactoryMock
            ->method('create')
            ->willReturn($clientMock);

        $options = $apiInstance->decorateResolveRequestOptions($requestMock);

        $this->assertIsArray($options);
    }

    public function testResolveRequestOptionsWithJson(): void
    {
        $clientFactoryMock = $this->createMock(ClientFactoryInterface::class);
        $requestMock = $this->createMock(RequestInterface::class);
        $clientMock = $this->createMock(ClientInterface::class);

        $apiInstance = $this->getMockBuilder(DecoratedApiFixture::class)
            ->onlyMethods(['onSuccessResponse', 'onRedirectResponse', 'onErrorResponse'])
            ->setConstructorArgs([$clientFactoryMock])
            ->getMock();

        $clientFactoryMock
            ->method('create')
            ->willReturn($clientMock);

        $requestMock
            ->method('getBodyFormat')
            ->willReturn(BodyFormat::JSON);

        $requestMock
            ->method('getBody')
            ->willReturn(['test' => 'test']);

        $options = $apiInstance->decorateResolveRequestOptions($requestMock);

        $this->assertIsArray($options);
    }

    public function testResolveRequestOptionsWithMultipart(): void
    {
        $clientFactoryMock = $this->createMock(ClientFactoryInterface::class);
        $requestMock = $this->createMock(RequestInterface::class);
        $clientMock = $this->createMock(ClientInterface::class);

        $apiInstance = $this->getMockBuilder(DecoratedApiFixture::class)
            ->onlyMethods(['onSuccessResponse', 'onRedirectResponse', 'onErrorResponse'])
            ->setConstructorArgs([$clientFactoryMock])
            ->getMock();

        $clientFactoryMock
            ->method('create')
            ->willReturn($clientMock);

        $requestMock
            ->method('getBodyFormat')
            ->willReturn(BodyFormat::MULTIPART);

        $requestMock
            ->method('getBody')
            ->willReturn(['test' => 'test']);

        $options = $apiInstance->decorateResolveRequestOptions($requestMock);

        $this->assertIsArray($options);
    }

    /**
     * @param int    $statusCode
     * @param string $methodName
     *
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @dataProvider provideHandleResponseData
     */
    public function testHandleResponse(int $statusCode, string $methodName): void
    {
        $clientFactoryMock = $this->createMock(ClientFactoryInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);
        $clientMock = $this->createMock(ClientInterface::class);

        $apiInstance = $this->getMockBuilder(DecoratedApiFixture::class)
            ->onlyMethods(['onSuccessResponse', 'onRedirectResponse', 'onErrorResponse'])
            ->setConstructorArgs([$clientFactoryMock])
            ->getMock();

        $clientFactoryMock
            ->method('create')
            ->willReturn($clientMock);

        $responseMock
            ->method('getStatusCode')
            ->willReturn($statusCode);

        $apiInstance
            ->expects($this->once())
            ->method($methodName);

        $apiInstance->decorateHandleResponse($responseMock);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public static function provideHandleResponseData(): array
    {
        return [
            'success' => [200, 'onSuccessResponse'],
            'success_empty' => [204, 'onSuccessResponse'],
            'redirect' => [300, 'onRedirectResponse'],
            'error' => [400, 'onErrorResponse'],
            'error_server' => [500, 'onErrorResponse'],
        ];
    }
}
