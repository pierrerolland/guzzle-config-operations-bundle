# guzzle-config-operations-bundle
[![Build Status](https://travis-ci.org/pierrerolland/guzzle-config-operations-bundle.svg?branch=master)](https://travis-ci.org/pierrerolland/guzzle-config-operations-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/pierrerolland/guzzle-config-operations-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/pierrerolland/guzzle-config-operations-bundle/?branch=master)
[![Total Downloads](https://poser.pugx.org/pierrerolland/guzzle-config-operations-bundle/downloads)](https://packagist.org/packages/pierrerolland/guzzle-config-operations-bundle)

This bundle allows Symfony projects to add Guzzle operations to their configuration. It also uses Symfony's or JMS' serializer to directly deserialize responses into objects. All you have to do is define your calls in Yaml, and your model classes to welcome the responses, and you're done !

## Installation
`composer require pierrerolland/guzzle-config-operations-bundle`

In your app/AppKernel.php if you're using Symfony 2/3
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

In `config/bundles.php` with Symfony 4
```php
<?php

return [
    // ...
    Guzzle\ConfigOperationsBundle\GuzzleConfigOperationsBundle::class => ['all' => true],
];

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
            $config:
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

## Client configuration

This documentation is extracted from the Guzzle Services package.

#### Operation

| Property | Description | Type |
| -------- | ----------- | ---- |
| httpMethod | HTTP method of the operation. | string |
| uri | URI template that can create a relative or absolute URL. | string |
| parameters | Associative array of parameters for the command. Each value must be an array that is used to create objects. See the `Parameter` config section below. | array |
| summary |  This is a short summary of what the operation does. | string |
| notes | A longer description of the operation. | string |
| documentationUrl | Reference URL providing more information about the operation. | string |
| responseClass | The model name used for deserializing response. | string |
| deprecated | Set to true if this is a deprecated command. | bool |
| errorResponses | Errors that could occur when executing the command. Array of hashes, each with a 'code' (the HTTP response code), 'phrase' (response reason phrase or description of the error), and 'class' (a custom exception class that would be thrown if the error is encountered). | array |
| data | Any extra data that might be used to help build or serialize the operation. | array |
| additionalParameters | Parameter schema to use when an option is passed to the operation that is not in the schema. | array |

*From https://github.com/guzzle/guzzle-services/blob/master/src/Operation.php#L23*

#### Parameter

| Property | Description | Type | Possible values |
| -------- | ----------- | ---- | --------------- |
| type | Type of variable. Types are used for validation and determining the structure of a parameter. You can use a union type by providing an array of simple types. If one of the union types matches the provided value, then the value is valid. |string or array | string, number, integer, boolean, object, array, numeric, null, any |
| required | Whether or not the parameter is required. | bool | |
| default | Default value to use if no value is supplied. | mixed | |
| static | Set to true to specify that the parameter value cannot be changed from the default. | bool | |
| description | Documentation of the parameter. | string |
| location | The location of a request used to apply a parameter. Custom locations can be registered with a command. | string | uri, query, header, body, json, xml, formParam, multipart, *custom* |
| sentAs | Specifies how the data being modeled is sent over the wire. For example, you may wish to include certain headers in a response model that have a normalized casing of FooBar, but the actual header is x-foo-bar. In this case, sentAs would be set to x-foo-bar. | string | |
| filters | Array of static method names to run a parameter value through. Each value in the array must be a string containing the full class path to a static method or an array of complex filter information. You can specify static methods of classes using the full namespace class name followed by '::' (e.g. Foo\Bar::baz). Some filters require arguments in order to properly filter a value. For complex filters, use a hash containing a 'method' key pointing to a static method, and an 'args' key containing an array of positional arguments to pass to the method. Arguments can contain keywords that are replaced when filtering a value: '@value' is replaced with the value being validated, '@api' is replaced with the Parameter object. | array | |
| properties | When the type is an object, you can specify nested parameters. | array | |
| additionalProperties | This attribute defines a schema for all properties that are not explicitly defined in an object type definition. If specified, the value MUST be a schema or a boolean. If false is provided, no additional properties are allowed beyond the properties defined in the schema. The default value is an empty schema which allows any value for additional properties. | bool or array | |
| items | This attribute defines the allowed items in an instance array, and MUST be a schema or an array of schemas. The default value is an empty schema which allows any value for items in the instance array. When this attribute value is a schema and the instance value is an array, then all the items in the array MUST be valid according to the schema. | array | |
| pattern | When the type is a string, you can specify the regex pattern that a value must match. | string | |
| enum | When the type is a string, you can specify a list of acceptable values. | array | |
| minItems | Minimum number of items allowed in an array. | int | |
| maxItems | Maximum number of items allowed in an array. | int | |
| minLength | Minimum length of a string. | int | |
| maxLength | Maximum length of a string. | int | |
| minimum | Minimum value of an integer. | int | |
| maximum | Maximum value of an integer. | int | |
| data |  Any additional custom data to use when serializing, validating, etc. | array | |
| format | Format used to coax a value into the correct format when serializing or unserializing. You may specify either an array of filters OR a format, but not both. | string | date-time, date, time, timestamp, date-time-http, boolean-string |

*From https://github.com/guzzle/guzzle-services/blob/master/src/Parameter.php#L84*

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
