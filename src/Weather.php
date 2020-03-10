<?php
/**
 * Created by PhpStorm
 * User: slairmy
 * Date: 2020/2/26
 * Time: 1:32 下午
 */
namespace Slairmy\Weather;

use GuzzleHttp\Client;
use Slairmy\Weather\Exceptions\HttpException;
use Slairmy\Weather\Exceptions\InvalidArgumentException;

class Weather
{
    /**
     * @var gaode weather key
     */
    protected $key;

    /**
     * @var array http option
     */
    protected $guzzleOption = [];

    /**
     * Weather constructor.
     * @param $key
     */
    public function __construct ($key)
    {
        $this->key = $key;
    }

    /**
     * @desc getHttpClient
     * @return Client
     */
    public function getHttpClient()
    {
        return new Client($this->guzzleOption);
    }

    /**
     * @desc setGuzzleOptions
     * @param array $options
     */
    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOption = $options;
    }

    /**
     * @desc getWeather
     * @param string $city
     * @param string $type
     * @param string $format
     * @return mixed|string
     * @throws InvalidArgumentException
     * @throws HttpException
     */
    public function getWeather(string $city, string $type = 'base', string $format = 'json')
    {
        $url = "https://restapi.amap.com/v3/weather/weatherInfo";

        if (!in_array(strtolower($type), ['base', 'all'])) {
            throw new InvalidArgumentException("Invalid type value(base/all): " . $type);
        }

        if (!in_array(strtolower($format), ['json', 'xml'])) {
            throw new InvalidArgumentException("Invalid response type(json/xml): " . $format);
        }

        $options = [
            'key'           =>  $this->key,
            'city'          =>  $city,
            'extensions'    =>  $type,
            'output'        =>  $format
        ];

        try {
            $response = $this->getHttpClient()->get($url, ['query' => $options])->getBody()->getContents();
            return 'json' === $format ? json_decode($response, true) : $response;
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

}