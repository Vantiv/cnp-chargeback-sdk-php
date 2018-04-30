<?php
/**
 * Created by PhpStorm.
 * User: hvora
 * Date: 4/27/18
 * Time: 9:50 AM
 */

namespace cnp\sdk;


use Exception;

class ChargebackWebException extends \Exception
{
    public $errorList;

    public function __construct($message, $code = 0, $errorList = null, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errorList = $errorList;
    }

    public function __toString()
    {
        return "ChargebackWebException : [{$this->code}]: {$this->message}\n";
    }
}