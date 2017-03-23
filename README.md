# guzzle-config-operations-bundle
[![Build Status](https://travis-ci.org/pierrerolland/guzzle-config-operations-bundle.svg?branch=master)](https://travis-ci.org/pierrerolland/guzzle-config-operations-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/pierrerolland/guzzle-config-operations-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/pierrerolland/guzzle-config-operations-bundle/?branch=master)
[![Total Downloads](https://poser.pugx.org/pierrerolland/guzzle-config-operations-bundle/downloads)](https://packagist.org/packages/pierrerolland/guzzle-config-operations-bundle)

This bundle allows Symfony projects to add Guzzle operations to their configuration. It also uses Symfony's serializer to directly deserialize responses into objects. All you have to do is define your calls in Yaml, and your model classes to welcome the responses, and you're done !

## Installation
`composer require pierrerolland/guzzle-config-operations-bundle`

In your app/AppKernel.php
```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...
            new Guzzle\ConfigOperationsBundle\GuzzleConfigOperationsBundle()
        ];

        return $bundles;
    }
```

Activate Symfony serializer in app/config.yml
```yaml
framework:
    serializer: ~
```

or JMSSerializer

https://github.com/schmittjoh/JMSSerializerBundle
http://jmsyst.com/bundles/JMSSerializerBundle

## Usage

### 1. Define your client

```yaml
# services.yml

services:
    app.client.foo:
        class: GuzzleHttp\Client
        tags:
            - { name: guzzle.client, alias: foo }
        arguments:
            config:
                baseUrl: "http://foo"
                operations:
                    readBar:
                        httpMethod: "GET"
                        uri: "/bar/{barId}"
                        responseClass: AppBundle\Model\Bar # The model used to deserialize the response
                        parameters:
                            barId:
                                type: "string"
                                location: "uri"
                                required: true
                    # other operations here    
      
```
The tag line is important, and requires both the `name: guzzle.client` and `alias` parts.

### 2. Use the client

A new service will appear, called guzzle_client.[the alias you used]. You can call the operations directly.

```php
   // @var AppBundle\Model\Bar $bar
   $bar = $this->get('guzzle_client.foo')->readBar(['barId' => 1]);
```

## Objects normalization

This bundle provides a recursive normalizer. Use the Type annotation
to make the normalizer know which object should be recursively
populated (suffixed by [] for arrays).


```php
<?php
// Article.php

namespace AppBundle\Model;

use Guzzle\ConfigOperationsBundle\Normalizer\Annotation as Normalizer;

class Article
{
    /**
     * @var Tag[]
     *
     * @Normalizer\Type(name="AppBundle\Model\Tag[]")
     */
    private $tags;

    /**
     * @var Category
     *
     * @Normalizer\Type(name="AppBundle\Model\Category")
     */
    private $category;

    // ...
```
