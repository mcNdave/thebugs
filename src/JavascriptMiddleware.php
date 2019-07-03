<?php

namespace TheBugs;

use Psr\Http\Message\ResponseFactoryInterface,
    Psr\Container\ContainerInterface,
    Psr\Http\Message\ResponseInterface,
    Psr\Http\Message\ServerRequestInterface,
    Psr\Http\Server\MiddlewareInterface,
    Psr\Http\Server\RequestHandlerInterface;

use Zend\Diactoros\Response,
    Zend\Diactoros\ServerRequest,
    Zend\Diactoros\Stream,
    Zend\Diactoros\Uri;

class JavascriptMiddleware implements MiddlewareInterface
{
    const FILTERS = [
        "code" => null,
        "headers" => [
            "User-Agent" => "TheBugs/1.0",
        ],
    ];

    /**
     * Filter list, allows custom invokable
     * @var array
     */
    protected $filters = [];

    public function __construct($options = [])
    {
        foreach(static::FILTERS as $key => $default) {
            $this->filters[$key] = ( $options[$key] ?? false ) ? $options[$key] : $default;
        }
    }

    public function throwError($errorDetails)
    {
        throw new Exception\JavascriptException($errorDetails['message'], 0, null, $errorDetails['location'], $errorDetails['line'], $errorDetails['url'], (array) $errorDetails['stack']);
    }

    /**
     * Process a server request and return a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ( ( $this->filters['code'] ?? false ) && ($this->filters['code'] !== $request->getCode())) {
            return $handler->handle($request);
        }

        foreach($this->filters['headers'] as $key => $value) {
            if ( ! in_array($value, $request->getHeader($key)) ) {
                return $handler->handle($request);
            }
        }

        $this->throwError(json_decode($request->getBody(), true));
    }

}
