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

namespace Thojou\SimpleApiClient\Tests\Fixture;

use Psr\Http\Message\ResponseInterface;
use Thojou\SimpleApiClient\AbstractApi;
use Thojou\SimpleApiClient\Contracts\RequestInterface;

abstract class DecoratedApiFixture extends AbstractApi
{
    public function decorateHandleResponse(ResponseInterface $response): mixed
    {
        return parent::handleResponse($response);
    }

    /**
     * @param RequestInterface $request
     *
     * @return array<array-key, mixed>
     */
    public function decorateResolveRequestOptions(RequestInterface $request): array
    {
        return parent::resolveRequestOptions($request);
    }

}
