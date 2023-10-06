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

namespace Thojou\SimpleApiClient;

use Psr\Http\Message\ResponseInterface;
use Thojou\SimpleApiClient\Contracts\ClientFactoryInterface;
use Thojou\SimpleApiClient\Contracts\ClientInterface;
use Thojou\SimpleApiClient\Contracts\PromiseInterface;
use Thojou\SimpleApiClient\Contracts\RequestInterface;
use Thojou\SimpleApiClient\Enums\BodyFormat;
use Thojou\SimpleApiClient\Exception\ApiException;
use Throwable;

abstract class AbstractApi
{
    private readonly ClientInterface $client;

    public function __construct(
        private readonly ClientFactoryInterface $factory,
    ) {
        $this->client = $this->factory->create();
    }

    /**
     * @param RequestInterface $request
     *
     * @return mixed
     */
    public function send(RequestInterface $request): mixed
    {
        return $this->sendAsync($request)->wait();
    }

    /**
     * @param RequestInterface $request
     *
     * @return PromiseInterface<ResponseInterface>
     */
    public function sendAsync(RequestInterface $request): PromiseInterface
    {
        return $this->client
            ->send(
                $request->getMethod()->value,
                $request->getUri(),
                $this->resolveRequestOptions($request)
            )
            ->then(
                fn (ResponseInterface $response) => $this->handleResponse($response),
                fn (Throwable $exception) => throw new ApiException(
                    $exception->getMessage(),
                    $exception->getCode(),
                    $exception
                )
            );
    }

    protected function handleResponse(ResponseInterface $response): mixed
    {
        $statusCode = $response->getStatusCode();
        $responseHeaders = $response->getHeaders();
        $responseContent = $response->getBody()->getContents();

        if ($statusCode >= 200 && $statusCode < 300) {
            return $this->onSuccessResponse($statusCode, $responseHeaders, $responseContent);
        }

        if ($statusCode >= 300 && $statusCode < 400) {
            return $this->onRedirectResponse($statusCode, $responseHeaders, $responseContent);
        }

        return $this->onErrorResponse($statusCode, $responseHeaders, $responseContent);
    }

    /**
     * @param RequestInterface $request
     *
     * @return array<string, mixed>
     */
    protected function resolveRequestOptions(RequestInterface $request): array
    {
        $options['headers'] = $request->getHeaders();

        if (!$request->getBody() || $request->getBodyFormat() === BodyFormat::EMPTY) {
            return $options;
        }

        $body = $request->getBody();

        if ($request->getBodyFormat() === BodyFormat::MULTIPART) {
            $multipart = [];
            foreach ($body as $key => $value) {
                $multipart[] = [
                    'name' => $key,
                    'contents' => $value
                ];
            }
            $body = $multipart;
        }

        $options[$request->getBodyFormat()->value] = $body;

        return $options;
    }

    /**
     * @param int                              $statusCode
     * @param array<string, array<int, mixed>> $headers
     * @param string                           $response
     *
     * @return mixed
     */
    abstract protected function onSuccessResponse(int $statusCode, array $headers, string $response): mixed;

    /**
     * @param int                              $statusCode
     * @param array<string, array<int, mixed>> $headers
     * @param string                           $response
     *
     * @return mixed
     */
    abstract protected function onRedirectResponse(int $statusCode, array $headers, string $response): mixed;

    /**
     * @param int                              $statusCode
     * @param array<string, array<int, mixed>> $headers
     * @param string                           $response
     *
     * @return mixed
     */
    abstract protected function onErrorResponse(int $statusCode, array $headers, string $response): mixed;
}
