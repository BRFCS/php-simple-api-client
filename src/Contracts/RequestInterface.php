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

use Thojou\SimpleApiClient\Enums\BodyFormat;
use Thojou\SimpleApiClient\Enums\RequestMethod;

interface RequestInterface
{
    public function getMethod(): RequestMethod;

    public function getBodyFormat(): BodyFormat;

    public function getUri(): string;

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array;

    /**
     * @return null|array<int|string,mixed>
     */
    public function getBody(): null|array;
}
