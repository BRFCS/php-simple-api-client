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

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Thojou\SimpleApiClient\Adapter\GuzzleClientAdapter;
use Thojou\SimpleApiClient\Adapter\GuzzleClientFactory;
use Thojou\SimpleApiClient\Contracts\ClientFactoryInterface;
use Thojou\SimpleApiClient\Contracts\ClientInterface;

class GuzzleClientFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $factory = new GuzzleClientFactory(
            'https://example.com/api/',
            'MyApiClient/1.0',
            ['Authorization' => 'Bearer token']
        );

        $result = $factory->create();

        $this->assertInstanceOf(ClientInterface::class, $result);
        $this->assertInstanceOf(GuzzleClientAdapter::class, $result);
    }
}
