# Vercom API Symfony Bundle

![Build and tests](https://github.com/plotkabytes/vercom-api-php-client/actions/workflows/ci.yml/badge.svg)
[![GitHub license](https://img.shields.io/github/license/Naereen/StrapDown.js.svg)](https://github.com/plotkabytes/vercom-api-php-client/blob/main/LICENSE)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg?style=flat-square)](http://makeapullrequest.com)

This repository contains simple symfony bundle for [vercom-api-php-client](https://github.com/plotkabytes/vercom-api-php-client).

## Requirements

This version supports [PHP](https://php.net) >= 7.2 and [Symfony](https://symfony.com/) >= 5.3.0.

## Installation

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Applications that use Symfony Flex

Open a command console, enter your project directory and execute:

```console
$ composer require plotkabytes/vercom-api-php-client plotkabytes/vercom-api-symfony-bundle
```

After getting composer you have to install PSR HTTP client implementation (if you dont have one already - 
for example [Guzzle](https://github.com/guzzle/guzzle) / [Buzz](https://github.com/kriswallsmith/Buzz)):

```console
$ composer require guzzlehttp/guzzle
```

### Applications that don't use Symfony Flex

#### Step 1: Download the Bundle

Procedure is the same as in "Applications that use Symfony Flex" chapter.

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Plotkabytes\VercomApiBundle\PlotkabytesVercomApiBundle.php::class => ['all' => true],
];
```

### Step 3: Configure the Bundle

Then, configure the bundle by adding following configuration to the `app/config/config.yml` file:

```yml
plotkabytes_vercom_api:
  
  clients:
    
    client_name:
      authorization_key: HERE_INSERT_AUTHORIZATION_KEY
      application_key: HERE_INSERT_APPLICATION_KEY
      alias: OPTIONAL_CLIENT_ALIAS
      default: false
      http_client: HTTP_CLIENT_SERVICE_ID
      
    other_client_name:
      authorization_key: HERE_INSERT_AUTHORIZATION_KEY
      application_key: HERE_INSERT_APPLICATION_KEY
      alias: OPTIONAL_CLIENT_ALIAS
      default: true
      http_client: HTTP_CLIENT_SERVICE_ID
```

### Step 4: Use bundle

With manual wiring the arguments:
```yml
services:
    App\Controller\DefaultController:
        arguments: {$client: '@plotkabytes_vercom_api.client.default'}
```

or by dependency injection:

```php

<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Plotkabytes\VercomApi;

class DefaultController extends AbstractController {

    private $client;

    public __construct(DefaultClient $client) {
        $this->client = $client;
    }
}
```

## Versioning

We use [Semantic Versioning 2.0.0](https://semver.org/).

Given a version number MAJOR.MINOR.PATCH, increment the:

* MAJOR version when you make incompatible API changes,
* MINOR version when you add functionality in a backwards compatible manner, and
* PATCH version when you make backwards compatible bug fixes.

Additional labels for pre-release and build metadata are available as extensions to the MAJOR.MINOR.PATCH format.

## Contributing

We will gladly receive issue reports and review and accept pull requests.
Feel free to contribute in any way.

## Author

Mateusz Żyła <mateusz.zylaa@gmail.com>

## License

Vercom Api Symfony Bundle is licensed under [The MIT License (MIT)](LICENSE).