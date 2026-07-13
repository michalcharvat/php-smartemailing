<?php
declare(strict_types=1);

namespace SmartEmailing\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JetBrains\PhpStorm\Pure;
use Psr\Http\Message\ResponseInterface;
use SmartEmailing\Api\Model\Response\BaseResponse;
use SmartEmailing\Exception\RequestException;
use SmartEmailing\SmartEmailing;
use SmartEmailing\Util\Helpers;

abstract class AbstractApi
{
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_PUT = 'PUT';
    public const METHOD_PATCH = 'PATCH';
    public const METHOD_DELETE = 'DELETE';

    protected const URI_PREFIX = '/api/v3/';

    /** @var positive-int */
    protected int $chunkLimit = 500;

    private Client $client;

    #[Pure]
    public function __construct(SmartEmailing $smartEmailing)
    {
        $this->client = $smartEmailing->getClient();
    }

    protected function get(string $uri, array $params = []): ResponseInterface
    {
        return $this->queryRequest(self::METHOD_GET, $uri, $params);
    }

    protected function post(string $uri, array $params = []): ResponseInterface
    {
        return $this->jsonRequest(self::METHOD_POST, $uri, $params);
    }

    protected function put(string $uri, array $params = []): ResponseInterface
    {
        return $this->jsonRequest(self::METHOD_PUT, $uri, $params);
    }

    protected function patch(string $uri, array $params = []): ResponseInterface
    {
        return $this->jsonRequest(self::METHOD_PATCH, $uri, $params);
    }

    protected function delete(string $uri, array $params = []): ResponseInterface
    {
        return $this->queryRequest(self::METHOD_DELETE, $uri, $params);
    }

    protected function queryRequest(string $method, string $uri, array $params = []): ResponseInterface
    {
        return $this->request($method, $uri, ['query' => $params]);
    }

    protected function jsonRequest(string $method, string $uri, array $params = []): ResponseInterface
    {
        return $this->request($method, $uri, ['json' => $params]);
    }

    protected function request(string $method, string $uri, array $options = []): ResponseInterface
    {
        try {
            return $this->getClient()->request(
                $method,
                self::URI_PREFIX . $uri,
                $options
            );
        } catch (GuzzleException $exception) {
            // Only Guzzle's RequestException carries an HTTP response; network-level
            // failures (ConnectException, ...) have none — guard, don't assume.
            $httpResponse = $exception instanceof \GuzzleHttp\Exception\RequestException
                ? $exception->getResponse()
                : null;
            $request = \method_exists($exception, 'getRequest') ? $exception->getRequest() : null;

            try {
                $response = new BaseResponse($httpResponse);
            } catch (RequestException $apiException) {
                // BaseResponse throws for error/absent responses; re-throw with the
                // transport exception chained and a meaningful message for the
                // no-API-message case (e.g. "Connection refused").
                $message = $apiException->getResponse()->getMessage() !== ''
                    ? $apiException->getMessage()
                    : $exception->getMessage();

                throw new RequestException(
                    $apiException->getResponse(),
                    $request,
                    $message,
                    \intval($exception->getCode()),
                    $exception
                );
            }

            // Defensive: only reachable if the transport failed while the response
            // parsed as successful (should not happen) — preserve legacy behavior.
            throw new RequestException(
                $response,
                $request,
                $exception->getMessage(),
                \intval($exception->getCode()),
                $exception
            );
        }
    }

    protected function replaceUrlParameters(string $uri, array $parameters): string
    {
        return Helpers::replaceUrlParameters($uri, $parameters);
    }

    protected function getClient(): Client
    {
        return $this->client;
    }

    #[Pure]
    protected static function encodePath(string $uri): string
    {
        return \rawurlencode($uri);
    }
}
