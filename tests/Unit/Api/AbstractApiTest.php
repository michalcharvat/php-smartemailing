<?php
declare(strict_types=1);

namespace SmartEmailing\Test\Unit\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use SmartEmailing\Exception\RequestException;
use SmartEmailing\SmartEmailing;

class AbstractApiTest extends TestCase
{
    private function facadeWithHandler(MockHandler $mock): SmartEmailing
    {
        $facade = new class ('username', 'api-key') extends SmartEmailing {
            public function replaceClient(Client $client): void
            {
                $this->client = $client;
            }
        };
        $facade->replaceClient(new Client(['handler' => HandlerStack::create($mock)]));

        return $facade;
    }

    public function testNetworkFailureThrowsRequestExceptionNotError(): void
    {
        $facade = $this->facadeWithHandler(new MockHandler([
            new ConnectException('Connection refused', new Request('GET', 'ping')),
        ]));

        try {
            $facade->tests()->aliveness();
            $this->fail('Expected RequestException');
        } catch (RequestException $e) {
            $this->assertStringContainsString('Connection refused', $e->getMessage());
            $this->assertInstanceOf(ConnectException::class, $e->getPrevious());
            $this->assertNotNull($e->getRequest());
        }
    }

    public function testHttpErrorThrowsRequestExceptionWithApiMessageAndChain(): void
    {
        $facade = $this->facadeWithHandler(new MockHandler([
            new Response(422, [], '{"status":"error","meta":[],"message":"Emailaddress is not valid"}'),
        ]));

        try {
            $facade->tests()->aliveness();
            $this->fail('Expected RequestException');
        } catch (RequestException $e) {
            $this->assertStringContainsString('Emailaddress is not valid', $e->getMessage());
            $this->assertInstanceOf(GuzzleException::class, $e->getPrevious());
        }
    }
}
