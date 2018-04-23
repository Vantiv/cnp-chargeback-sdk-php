<?php
/**
 * Created by PhpStorm.
 * User: hvora
 * Date: 4/23/18
 * Time: 1:07 PM
 */

namespace cnp\sdk;


use Exception;

class ChargebackException extends \Exception
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return "ChargebackException : [{$this->code}]: {$this->message}\n";
    }
}