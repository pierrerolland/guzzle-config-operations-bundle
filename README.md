# guzzle-config-operations-bundle
This bundle allows Symfony projects to add Guzzle operations to their configuration. It also uses JMS Serializer to directly deserialize responses into objects. All you have to do is define your calls in Yaml, and your model classes to welcome the responses, and you're done !

## Installation
`composer require pierrerolland/guzzle-config-operations-bundle`

And in your app/AppKernel.php
```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Guzzle\ConfigOperationsBundle\GuzzleConfigOperationsBundle()
        ];

        return $bundles;
    }
```

## Usage

### 1. Define your client

```yaml
# services.yml

services:
    app.client.foo:
        class: GuzzleHttp\Client
        tags:
            - { name: guzzle.client, alias: foo }
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

The bundle is still in development, for example it needs some exceptions handling. If you're motivated, don't hesitate to PR :)
