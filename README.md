# Meilisearch Light PHP Client

Meilisearch Light PHP Client is a PHP library for using a Meilisearch server in PHP.

PHP 5.6+ compatible unlike official clients ([meilisearch-php](https://github.com/meilisearch/meilisearch-php) and [meilisearch-symfony](https://github.com/meilisearch/meilisearch-symfony)) that allow PHP 7.4 or 8.0.



## Requirements

* Meilisearch server URL
* Search API Key 
* Admin API Key



## Installation

With Composer, run this command:

    composer require shevabam/meilisearch-light-php-client



## Usage

### Request

First, include the library in your code using the Composer autoloader:

```php
require 'vendor/autoload.php';
```


Then, create an MeilisearchLightClient object with some paremeters:

```php
$host = 'http://xxx.xxx.xxx.xxx:7700';
$searchKey = 'yyy';
$adminKey = 'zzz';

$Request = new MeilisearchLighClient\Request($host);
```


Make a call:

```php
$params = ['key' => $adminKey];
$Request->call($params, 'GET', 'indexes');
```

The `call` method takes as a parameter:

* the parameters (see below)
* the HTTP method (GET, POST, PUT, ...)
* the endpoint (corresponds to the Meilisearch query)
* the data to transmit: can be a file (must start with @) or an array

The parameters allow you to specify the API key to use (search or admin) as well as the headers if necessary: 

```php
$params = [
    'key' => $searchKey,
    'headers' => ['Content-type: application/json'],
];
```

### Response

To check that a request is valid, use the `isOk()` method:

```php
if ($Request->isOk())
{
    $Response = $Request->getResponse();
        
    var_dump($Response->get()); // Response content
}
else
{
    echo $Request->getHttpStatus();
}
```

By default, the return is an object. To get an array:

```php
$Response = $Request->getResponse(true);
```


### Examples

#### List of indexes:

```php
<?php
require 'vendor/autoload.php';

$host = 'http://xxx.xxx.xxx.xxx:7700';
$searchKey = 'yyy';
$adminKey = 'zzz';

$Request = new MeilisearchLightClient\Request($host);

$params = ['key' => $adminKey];

$Request->call($params, 'GET', 'indexes');

$response_content = null;
if ($Request->isOk())
{
    $Response = $Request->getResponse(true);

    $response_content = $Response->get();
}
else
{
    echo $Request->getHttpStatus();
}

echo '<pre>'; print_r($response_content);
```

#### Adding documents to an index via a file

```php
<?php
require 'vendor/autoload.php';

$host = 'http://xxx.xxx.xxx.xxx:7700';
$searchKey = 'yyy';
$adminKey = 'zzz';

$Request = new MeilisearchLightClient\Request($host);

$params = [
    'key' => $adminKey, 
    'headers' => ['Content-type: application/json'],
];

$Request->call($params, 'POST', 'indexes/movies/documents', '@movies.json');

$response_content = null;
if ($Request->isOk())
{
    $Response = $Request->getResponse(true);

    $response_content = $Response->get();
}
else
{
    echo $Request->getHttpStatus();
}

echo '<pre>'; print_r($response_content);
```

#### Adding documents to an index via an array

```php
<?php
require 'vendor/autoload.php';

$host = 'http://xxx.xxx.xxx.xxx:7700';
$searchKey = 'yyy';
$adminKey = 'zzz';

$Request = new MeilisearchLightClient\Request($host);

$params = [
    'key' => $adminKey, 
    'headers' => ['Content-type: application/json'],
];

$datas = [
    [
        'id' => 1,
        'title' => 'Sharknado',
    ],
    [
        'id' => 2,
        'title' => 'Phone Booth',
    ],
    [
        'id' => 3,
        'title' => 'Jurassic Park',
    ],
];
$Request->call($params, 'POST', 'indexes/movies/documents', $datas);

$response_content = null;
if ($Request->isOk())
{
    $Response = $Request->getResponse(true);

    $response_content = $Response->get();
}
else
{
    echo $Request->getHttpStatus();
}

echo '<pre>'; print_r($response_content);
```

#### Search

```php
<?php
require 'vendor/autoload.php';

$host = 'http://xxx.xxx.xxx.xxx:7700';
$searchKey = 'yyy';
$adminKey = 'zzz';

$Request = new MeilisearchLightClient\Request($host);

$params = [
    'key' => $searchKey, 
    'headers' => ['Content-type: application/json'],
];

$Request->call($params, 'POST', 'indexes/movies/search', [
    'q' => 'jurassic', 
    'limit' => 50,
    'filter' => 'release_date > '.strtotime(date('2018-01-01')),
]);

$response_content = null;
if ($Request->isOk())
{
    $Response = $Request->getResponse(true);

    $response_content = $Response->get();
}
else
{
    echo $Request->getHttpStatus();
}

echo '<pre>'; print_r($response_content);
```



## Resources

* [API Reference Meilisearch](https://docs.meilisearch.com/reference/api/overview.html)
* [Quick start Meilisearch](https://docs.meilisearch.com/learn/getting_started/quick_start.html)
