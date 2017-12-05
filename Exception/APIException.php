<?php

namespace Harmony2\Exception;

class APIException extends \Exception
{
    /**
     * @var string
     */
    private $details;

    public function __construct($message, $code = 0, $details = '')
    {
        $this->setDetails($details);
        parent::__construct($message, $code);
    }

    public function setDetails($details)
    {
        $this->details = (string) $details;

        return $this;
    }

    public function getDetails()
    {
        return $this->details;
    }
}
