<?php


namespace Ferus\FairPayApi\Exception;


class CurlExecException extends \Exception
{
    public $info;

    public function __construct($message, $info){
        parent::__construct($message);

        $this->info = $info;
    }
} 