<?php

namespace TheBugs\Email;

class SwiftMailer implements MailerInterface
{
    protected $emailConfiguration;
    
    protected $transport;
    
    protected $from;
    
    protected $to;
    
    public function __construct(EmailConfiguration $configuration) {
        $this->emailConfiguration = $configuration;
        
        $this->transport = ( new \Swift_SmtpTransport($this->emailConfiguration->smtpHost, $this->emailConfiguration->smtpPort) )
            ->setUsername($this->emailConfiguration->smtpUsername)
            ->setPassword($this->emailConfiguration->smtpPassword);
    }
    
    public function send(string $subject, string $message, bool $html = true) : bool
    {
        $swiftObj = ( new \Swift_Message($subject) )
            ->setFrom( $this->from )
            ->setTo( $this->to )
            ->setBody( $message, $html ? 'text/html' : 'text/plain' );
        
        return ( new \Swift_Mailer($this->transport) )->send($swiftObj);
    }
    
    public function setFrom($from) : MailerInterface
    {
        $this->from = $from;
        return $this;
    } 
    
    public function setTo($to) : MailerInterface
    {
        $this->to = $to;
        return $this;
    }
    
}