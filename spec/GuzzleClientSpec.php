<?php

namespace spec\Guzzle\ConfigOperationsBundle;

use Guzzle\ConfigOperationsBundle\GuzzleClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Command\CommandInterface;
use GuzzleHttp\Command\Guzzle\DescriptionInterface;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\Serializer\SerializerInterface;

class GuzzleClientSpec extends ObjectBehavior
{
    function let(
        ClientInterface $client,
        DescriptionInterface $description,
        SerializerInterface $serializer
    ) {
        $this->beConstructedWith($client, $description, ['test' => 'Result'], $serializer, []);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GuzzleClient::class);
    }

    function its_transform_response_deserializes(
        $serializer,
        ResponseInterface $response,
        StreamInterface $stream,
        RequestInterface $request,
        CommandInterface $command
    ) {
        $response->getBody()->willReturn($stream);
        $stream->getContents()->willReturn('body');
        $command->getName()->willReturn('test');
        $serializer->deserialize('body', 'Result', 'json')->willReturn('final');

        $this->transformResponse($response, $request, $command)->shouldReturn('final');
    }

    function its_transform_response_does_not_deserialize_if_no_response_class(
        ResponseInterface $response,
        RequestInterface $request,
        CommandInterface $command
    ) {
        $command->getName()->willReturn('no_response_class');

        $this->transformResponse($response, $request, $command)->shouldReturn($response);
    }
}
