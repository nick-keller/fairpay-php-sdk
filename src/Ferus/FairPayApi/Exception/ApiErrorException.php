<?php


namespace Ferus\FairPayApi\Exception;


class ApiErrorException extends \Exception
{
    public $returned_value;

    function __construct($message, $code, $returned_value = null)
    {
        parent::__construct($message, $code);
        $this->returned_value = $returned_value;
    }


} 