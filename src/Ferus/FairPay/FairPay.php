<?php

namespace Ferus\FairPay;

use Symfony\Component\Yaml\Parser;

if (!function_exists('curl_init')) {
    throw new \Exception('FairPay needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
    throw new \Exception('FairPay needs the JSON PHP extension.');
}


class FairPay
{
    /**
     * @var array
     */
    private $config;

    public function __construct()
    {
        $yaml = new Parser;
        $this->config = $yaml->parse(file_get_contents(__DIR__ . '/config.yml'));;
    }
} 