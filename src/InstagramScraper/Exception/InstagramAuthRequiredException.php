<?php

namespace InstagramScraper\Exception;

class InstagramAuthRequiredException extends \Exception
{
    public function __construct($message = "", $code = 403, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
