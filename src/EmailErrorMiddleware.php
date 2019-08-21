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


class EmailErrorMiddleware implements MiddlewareInterface
{
    protected /* EmailConfiguration */ $emailConfiguration;
    
    protected /* Callable */ $callable;
    
    protected /* Mailer */ $mailer;
    
    public function __construct(Email\EmailConfiguration $emailConfiguration, Email\MailerInterface $mailer, Callable $callable)
    {
        $this->emailConfiguration = $emailConfiguration;
        $this->callable = $callable;
        $this->mailer = $mailer;
    }
    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $response = $handler->handle($request);
        
        if ( $response->getStatusCode() === 500 ) {

            $this->mailer->setTo('dave.mcnicoll@cslsj.qc.ca');
            $this->mailer->setFrom(['test@johndoe.com' => 'John Doe']);

            $bugReport = $response->getBody()->__toString();
            
            if ( false === ( $this->mailer->send(error_get_last()['message'], $bugReport, true) ) ) {
                error_log("Impossile to send an email bug report from " . static::class);
            }
            
            return $this->callable->call($this);
        }
        
        return $response;
    }
}