<?php

namespace Guzzle\ConfigOperationsBundle;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Command\CommandInterface;
use GuzzleHttp\Command\Exception\CommandException;
use GuzzleHttp\Command\Guzzle\DescriptionInterface;
use GuzzleHttp\Command\Guzzle\GuzzleClient as BaseGuzzleClient;
use GuzzleHttp\Ring\Future\FutureInterface;
use JMS\Serializer\Serializer;

/**
 * Overloads the original GuzzleClient to add the Serializer
 *
 * @author Pierre Rolland <roll.pierre@gmail.com>
 */
class GuzzleClient extends BaseGuzzleClient
{
    /**
     * @var array
     */
    protected $responseClasses = array();

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @param ClientInterface $client
     * @param DescriptionInterface $description
     * @param array $responseClasses
     * @param Serializer $serializer
     * @param array $config
     */
    public function __construct(
        ClientInterface $client,
        DescriptionInterface $description,
        array $responseClasses,
        Serializer $serializer,
        array $config = []
    ) {
        $this->client = $client;
        $this->responseClasses = $responseClasses;
        $this->serializer = $serializer;
        parent::__construct($client, $description, $config);
    }

    public function execute(CommandInterface $command)
    {
        $trans = $this->initTransaction($command);

        if ($trans->result !== null) {
            return $trans->result;
        }

        try {
            $trans->response = $this->client->send($trans->request);
            return $trans->response instanceof FutureInterface
                ? $this->createFutureResult($trans)
                : $this
                    ->serializer
                    ->deserialize(
                        $trans->response->getBody()->getContents(),
                        $this->getResponseClass($command->getName()),
                        'json'
                    );
        } catch (CommandException $e) {
            throw $e;
        } catch (\Exception $e) {
            if ($trans->result !== null) {
                return $trans->result;
            }
            $trans->exception = $e;
            throw $this->createCommandException($trans);
        }
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getResponseClass($name)
    {
        return array_key_exists($name, $this->responseClasses) ? $this->responseClasses[$name] : 'array';
    }
}
