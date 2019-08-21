<?php

namespace TheBugs\Email;

interface MailerInterface
{     
    public function send(string $subject, string $message, bool $html = true) : bool;
    
    public function setFrom($from) : self;
    
    public function setTo($to) : self;
}