<?php

namespace TheBugs;

use Psr\Http\Message\ResponseFactoryInterface,
    Psr\Container\ContainerInterface,
    Psr\Http\Message\ResponseInterface,
    Psr\Http\Message\ServerRequestInterface,
    Psr\Http\Server\MiddlewareInterface,
    Psr\Http\Server\RequestHandlerInterface;

use Monolog\Logger;
use Monolog\Formatter\HtmlFormatter;
use Monolog\Processor\WebProcessor,
    Monolog\Processor\MemoryUsageProcessor,
    Monolog\Processor\IntrospectionProcessor;

use MonologPHPMailer\PHPMailerHandler;

use PHPMailer\PHPMailer\PHPMailer;

class EmailMiddleware implements MiddlewareInterface
{
    const DEFAULT_FROM_NAME = "Bug Email Reporter";

    /**
     * PHPMailer object
     * @var PHPMailer
     */
    protected $phpmailer = null;

    /**
     * Monolog object
     * @var Logger object
     */
    protected $logger = null;

    public function __construct(array $mail, array $options = [], PHPMailer $mailer = null, Logger $logger = null)
    {
        $this->mailer = $mailer ?: new PHPMailer(true);
        $this->logger = $logger ?: new Logger('default');
        $this->handler = new PHPMailerHandler($this->mailer);

        $this->mailer->isSMTP();
        $this->mailer->Host = $mail['host'];
        $this->mailer->SMTPAuth = $mail['smtp_auth'] ?? true;
        $this->mailer->Username = $mail['username'];
        $this->mailer->Password = $mail['password'];

        $this->mailer->setFrom($mail['from_address'], $mail['from_name'] ?? static::DEFAULT_FROM_NAME);

        foreach($this->adjustAddress($mail['to'] ?? []) as $item) {
            $this->mailer->addAddress($item['address'], $item['name'] ?? "");
        }

        foreach($this->adjustAddress($mail['cc'] ?? []) as $item) {
            $this->mailer->addCC($item['address'], $item['name'] ?? "");
        }

        foreach($this->adjustAddress($mail['bcc'] ?? []) as $item) {
            $this->mailer->addBCC($item['address'], $item['name'] ?? "");
        }

        $this->handler->setFormatter(new HtmlFormatter);
        $this->logger->pushHandler($this->handler);

        $this->logger->pushProcessor(new IntrospectionProcessor);
        $this->logger->pushProcessor(new MemoryUsageProcessor);
        $this->logger->pushProcessor(new WebProcessor);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request);
    }

    protected function adjustAddress($address) {
        return is_string($address) ? [ [ 'address' => $address ] ] : $address;
    }
}
