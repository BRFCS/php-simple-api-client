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

use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;
use Thojou\SimpleApiClient\Contracts\ClientInterface;

class GuzzleClientAdapter implements ClientInterface
{
    public function __construct(
        private readonly GuzzleClientInterface $client
    ) {
    }

    /**
     * @param string               $method
     * @param string               $uri
     * @param array<string, mixed> $options
     *
     * @return GuzzlePromiseAdapter<Psr7ResponseInterface>
     */
    public function send(string $method, string $uri, array $options = []): GuzzlePromiseAdapter
    {
        $promise = $this->client->requestAsync($method, $uri, $options);

        return new GuzzlePromiseAdapter($promise);
    }
}
