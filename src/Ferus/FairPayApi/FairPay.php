<?php

namespace Ferus\FairPayApi;

require_once __DIR__ . '/Exception/ApiErrorException.php';
require_once __DIR__ . '/Exception/CurlExecException.php';

use Ferus\FairPayApi\Exception\ApiErrorException;
use Ferus\FairPayApi\Exception\CurlExecException;

if (!function_exists('curl_init')) {
    throw new \Exception('FairPay needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
    throw new \Exception('FairPay needs the JSON PHP extension.');
}


class FairPay
{
    /**
     * @var string
     */
    private $endpoint = 'https://bde.esiee.fr/fairpay/api';

    /**
     * @var string
     */
    private $api_key;

    private $curl_params = array(
        CURLOPT_RETURNTRANSFER => true,
    );

    function __construct($api_key = '')
    {
        $this->api_key = $api_key;
    }

    /**
     * @param string $endpoint
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * @param string $api_key
     */
    public function setApiKey($api_key)
    {
        $this->api_key = $api_key;
    }

    /**
     * $data parameters will be placed in route if there names match "{param_name}" in the $path parameter.
     * Otherwise they will be sent as get parameter ?param_name=value or post fields
     *
     * @param string $path route of the api action : /students/{query}, /balance... route parameters must be of the standard symfony route format : {param_name}
     * @param string $methode get | post
     * @param array $data an associative array of data
     * @return array|Object
     * @throws Exception\CurlExecException when curl does not work
     * @throws Exception\ApiErrorException when the api return an error
     */
    public function api($path, $methode = 'get', array $data = array())
    {
        $url = $this->endpoint . $path;
        $non_url_data = array();

        foreach($data as $param_name => $value){
            if(strpos($url, "{{$param_name}}"))
                $url = str_replace("{{$param_name}}", urlencode($value), $url);
            else
                $non_url_data[$param_name] = $value;
        }

        if($methode == 'get' && count($non_url_data)){
            $url .= '?';

            foreach($non_url_data as $param_name => $value)
                $non_url_data[$param_name] = urlencode($param_name).'='.urlencode($value);

            $url .= implode('&', $non_url_data);

        }

        $curl = curl_init($url);

        foreach($this->curl_params as $param_name => $value)
            curl_setopt($curl, $param_name, $value);

        if($methode == 'post'){
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        $curl_response = curl_exec($curl);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($curl_response === false) {
            $info = curl_getinfo($curl);
            curl_close($curl);

            throw new CurlExecException('Error occured during curl exec. Additioanl info: ' . var_export($info));
        }

        curl_close($curl);
        $decoded = json_decode($curl_response);

        if($http_status > 299){
            if($decoded != null && array_key_exists('message', $decoded))
                throw new ApiErrorException($decoded->message, $http_status, $decoded);
            throw new ApiErrorException(null, $http_status, $decoded);
        }

        return $decoded;
    }

    public function getStudent($query)
    {
        return $this->api('/students/{query}', 'get', array('query' => $query));
    }

    public function searchStudents($query)
    {
        return $this->api('/students/{query}/search', 'get', array('query' => $query));
    }

    public function getStudents()
    {
        return $this->api('/students');
    }

    public function getBalance()
    {
        return floatval($this->api('/balance', 'get', array('api_key' => $this->api_key))->balance);
    }

    public function cash($client_id, $amount, $cause)
    {
        return $this->api('/cash', 'post', array(
            'api_key' => $this->api_key,
            'client_id' => $client_id,
            'amount' => $amount,
            'cause' => $cause,
        ));
    }

    public function deposit($client_id, $amount)
    {
        return $this->api('/deposit', 'post', array(
            'api_key' => $this->api_key,
            'client_id' => $client_id,
            'amount' => $amount,
        ));
    }
} 