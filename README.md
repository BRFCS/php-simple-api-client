# PHP Simple Client Api

![Static Badge](https://img.shields.io/badge/PHP_Version-%3E%3D8.1-blue)
[![CI](https://github.com/thojou/php-simple-api-client/actions/workflows/ci.yml/badge.svg)](https://github.com/thojou/php-simple-api-client/actions/workflows/ci.yml)
![Coverage](https://img.shields.io/badge/coverage-100%25-green)
[![License](https://img.shields.io/github/license/thojou/php-simple-api-client)](./LICENSE)

**PHP Simple Client API** is a lightweight PHP library for building REST API clients easily. It simplifies making HTTP requests, handling responses, and managing API interactions.

## Requirements
* PHP version >= 8.1
* [GuzzleHttp](https://github.com/guzzle/guzzle) >= 7.8

## Installation

You can effortlessly install the **PHP Simple Client Api** using the popular package manager [composer](https://getcomposer.org/).

```bash
composer require thojou/php-simple-api-client:dev-master
```

## Usage

To start implementing your REST API client with the Simple API Client library, follow these steps:

### Quick Start

#### 1. Create Your API Class

Extend the ``Thojou\SimpleApiClient\AbstractApi`` class to create your API client class. Implement the onSuccessResponse, onRedirectResponse, and onErrorResponse methods to handle different types of responses.

```php
<?php

use Thojou\SimpleApiClient\AbstractApi;
use Thojou\SimpleApiClient\Contracts\ClientFactoryInterface;
use Thojou\SimpleApiClient\Exception\ApiException;

class MyApi extends AbstractApi
{
    protected function onSuccessResponse(int $statusCode, array $headers, string $response): mixed
    {
        return (array) json_decode($response, true);
    }

    protected function onRedirectResponse(int $statusCode, array $headers, string $response): mixed
    {
        throw new ApiException('Redirects are not supported');
    }

    protected function onErrorResponse(int $statusCode, array $headers, string $response): mixed
    {
        throw new ApiException('Status code ' . $statusCode . ': ' . $response);
    }
}
```

#### 2. Create Your Request Class

Implement the ``Thojou\SimpleApiClient\Contracts\RequestInterface`` interface to create your request class. Define the HTTP method, URI, headers, body format, and body as needed for your API endpoint.

````php
<?php

use Thojou\SimpleApiClient\Contracts\RequestInterface;use Thojou\SimpleApiClient\Enums\RequestMethod;

class MyRequest implements RequestInterface
{
    public function getMethod(): RequestMethod
    {
        return RequestMethod::GET;
    }

    public function getUri(): string
    {
        return 'v1/weather';
    }

    public function getHeaders(): array
    {
         return [
            'Accept' => 'application/json',
        ];
    }
    
    public function getBodyFormat(): BodyFormat
    {
        return BodyFormat::Empty;
    }

    public function getBody(): null|array
    {
        return null;
    }
}
````

#### 3. Use Your API Client

Now, you can use your API client to make requests to the API endpoints. Here's an example of how to create an instance of your API and send a request:

```php
<?php

use Thojou\SimpleApiClient\Adapter\GuzzleClientFactory;

$httpClientFactory = new GuzzleClientFactory('https://api.example.com', 'MyApi/1.0.0', [
    'Authorization' => 'Bearer: mySecretApiToken'
]); 
$api = new MyApi($httpClientFactory);

$response = $api->send(new MyRequest());
var_dump($response);

// Or send an asynchronous request
$promise = $api->sendAsync(new MyRequest());
$response = $promise->wait(); // Wait for the asynchronous request to complete
var_dump($response);
```

### Example: Making a POST Request with JSON Data

Suppose you need to make a POST request to an API endpoint with JSON data. You can achieve this by defining a request class with the ``RequestMethod::POST`` method and specifying the ``BodyFormat::JSON`` body format along with your JSON data.

```php
<?php

use Thojou\SimpleApiClient\Contracts\RequestInterface;
use Thojou\SimpleApiClient\Enums\RequestMethod;
use Thojou\SimpleApiClient\Enums\BodyFormat;

class MyJsonPostRequest implements RequestInterface
{
    public function getMethod(): RequestMethod
    {
        return RequestMethod::POST;
    }

    public function getUri(): string
    {
        return 'v1/create-resource';
    }

    public function getHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }
    
    public function getBodyFormat(): BodyFormat
    {
        return BodyFormat::JSON;
    }

    public function getBody(): null|array
    {
        // Define your JSON data as an associative array
        return [
            'key' => 'value',
            'nested' => [
                'property' => 'nested-value',
            ],
        ];
    }
}
```

### Example: Making a POST Request with Multipart Form Data

Suppose you need to make a POST request to an API endpoint with multipart form data. You can achieve this by defining a request class with the ``RequestMethod::POST`` method and specifying the ``BodyFormat::MULTIPART`` body format along with your form data.

```php
<?php

use Thojou\SimpleApiClient\Contracts\RequestInterface;
use Thojou\SimpleApiClient\Enums\RequestMethod;
use Thojou\SimpleApiClient\Enums\BodyFormat;

class MyMultipartPostRequest implements RequestInterface
{
    public function getMethod(): RequestMethod
    {
        return RequestMethod::POST;
    }

    public function getUri(): string
    {
        return 'v1/upload-file';
    }

    public function getHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }
    
    public function getBodyFormat(): BodyFormat
    {
        return BodyFormat::MULTIPART;
    }

    public function getBody(): null|array
    {
        // Define your multipart form data as an associative array
        return [
            'file' => fopen('/path/to/file.jpg', 'r'), // Replace with your file path
            'field1' => 'value1',
            'field2' => 'value2',
        ];
    }
}
```

Now, you can use your API client to make a POST request with multipart form data like in the previous example.


## License

This project is licensed under the generous and permissive [MIT license](./LICENSE).