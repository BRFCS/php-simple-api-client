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

use GuzzleHttp\Client;
use Thojou\SimpleApiClient\Contracts\ClientFactoryInterface;
use Thojou\SimpleApiClient\Contracts\ClientInterface;

class GuzzleClientFactory implements ClientFactoryInterface
{
    /**
     * @param string               $baseUrl
     * @param string               $userAgentName
     * @param array<string, mixed> $headers
     */
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $userAgentName,
        private readonly array $headers = []
    ) {
    }

    public function create(): ClientInterface
    {
        return new GuzzleClientAdapter(
            new Client(
                [
                    'base_uri' => $this->baseUrl,
                    'headers' => [
                            'User-Agent' => $this->userAgentName
                        ] + $this->headers,
                ]
            )
        );
    }
}
