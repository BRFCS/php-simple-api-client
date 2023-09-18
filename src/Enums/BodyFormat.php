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

namespace Thojou\SimpleApiClient\Enums;

enum BodyFormat: string
{
    case JSON = 'json';
    case FORM_PARAMS = 'form_params';
    case MULTIPART = 'multipart';
    case EMPTY = 'empty';
}
