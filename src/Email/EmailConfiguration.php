<?php

namespace TheBugs\Email;

class EmailConfiguration
{
    const AUTH_TYPE_SMTP = 1;
    
    protected $type;
    
    public $smtpHost = "";
    
    public $smtpUsername = "";
    
    public $smtpPassword = "";
    
    public $smtpPort = 25;
    
    public $smtpUseTLS = false;
    
    public function __construct($type = self::AUTH_TYPE_SMTP)
    {
        $this->type = $type;
    }
    
}