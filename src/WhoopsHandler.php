<?php

use Whoops\Run as Whoops;
use Whoops\Handler\PrettyPageHandler as Handler;

$whoops = new Whoops();
$whoops->allowQuit(false);
$whoops->writeToOutput(false);
$whoops->pushHandler(new Handler());

IF CLI {
    $handler->handleUnconditionally(true);
}

$body = $whoops->handleException($this->exception);
