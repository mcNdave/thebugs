<?php

namespace TheBugs\Exception;

class JavascriptException extends \Exception {

    protected $url;

    protected $stacktrace;

    public function __construct(string $message, int $code, Exception $previous = null, string $file, int $line, string $url, array $trace)
    {
        $this->message = $message;
        $this->code = $code;
        $this->previous = $previous;
        $this->file = $file;
        $this->line = $line;
        $this->url = $url;
        $this->stacktrace = $trace;
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->url}:{$this->code}]: {$this->message}\n" . json_encode($this->stacktrace);
    }

}
