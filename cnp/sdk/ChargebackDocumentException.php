<?php
/**
 * Created by PhpStorm.
 * User: hvora
 * Date: 4/27/18
 * Time: 9:51 AM
 */

namespace cnp\sdk;


use Exception;

class ChargebackDocumentException extends \Exception
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return "ChargebackDocumentException : [{$this->code}]: {$this->message}\n";
    }
}