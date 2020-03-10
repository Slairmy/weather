<?php
/**
 * Created by PhpStorm
 * User: slairmy
 * Date: 2020/2/26
 * Time: 2:25 下午
 */
namespace Slairmy\Weather\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Mockery\Matcher\AnyArgs;
use PHPUnit\Framework\TestCase;
use Slairmy\Weather\Exceptions\HttpException;
use Slairmy\Weather\Exceptions\InvalidArgumentException;
use Slairmy\Weather\Weather;

class WeatherTest extends TestCase
{

    public function testGetWeather()
    {
        // 创建模拟接口响应值
        $response = new Response(200, [], '{"success": true}');
        // 创建模拟client
        $client = \Mockery::mock(Client::class);

        // 指定将会发生的行为
        $client->allows()->get('https://restapi.amap.com/v3/weather/weatherInfo', [
            'query' => [
                'key'           => 'mock-key',
                'city'          => '深圳',
                'extensions'    => 'base',
                'output'        => 'json'
            ]
        ])->andReturn($response);


        $w = \Mockery::mock(Weather::class, ['mock-key'])->makePartial();
        $w->allows()->getHttpClient()->andReturn($client);



        // 调用 getHttpClient 方法, 并断言返回值为模拟的返回值
        $this->assertSame(['success' => true], $w->getWeather('深圳', 'base', 'json'));

        // xml 格式结果
        $response = new Response(200, [], '<hello>content</hello>');
        $client = \Mockery::mock(Client::class);

        // 指定将会发生的行为
        $client->allows()->get('https://restapi.amap.com/v3/weather/weatherInfo', [
            'query' => [
                'key'           => 'mock-key',
                'city'          => '深圳',
                'output'        => 'xml',
                'extensions'    => 'all'
            ]
        ])->andReturn($response);

        $w = \Mockery::mock(Weather::class, ['mock-key'])->makePartial();
        $w->allows()->getHttpClient()->andReturn($client);

        // 调用 getHttpClient 方法, 并断言返回值为模拟的返回值
        $this->assertSame('<hello>content</hello>', $w->getWeather('深圳', 'all', 'xml'));
    }

    /**
     * @desc testGetHttpClient
     *
     */
    public function testGetHttpClient()
    {
        $w = new Weather('mock-key');

        $this->assertInstanceOf(ClientInterface::class, $w->getHttpClient());
    }

    /**
     * @desc testSetGuzzleOptions
     *
     */
    public function testSetGuzzleOptions()
    {
        $w = new Weather('mock-key');

        $this->assertNull($w->getHttpClient()->getConfig('timeout'));

        $w->setGuzzleOptions(['timeout' => 5000]);
        $this->assertSame(5000, $w->getHttpClient()->getConfig('timeout'));
    }

    /**
     * @desc testGetWeatherWithGuzzleRuntimeException
     * 异常断言
     */
    public function testGetWeatherWithGuzzleRuntimeException()
    {
        $client = \Mockery::mock(Client::class);

        // 指定即将发生的行为
        $client->allows()->get(new AnyArgs())
                         ->andThrow(new \Exception('request timeout'));

        // 将 `getHttpClient` 方法替换为上面创建的 http client 为返回值的模拟方法。
        $w = \Mockery::mock(Weather::class, ['mock-key'])->makePartial();
        $w->allows()->getHttpClient()->andReturn($client);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('request timeout');

        $w->getWeather('深圳');
    }

    /**
     * @desc testGetWeatherWithInvalidType
     * @throws InvalidArgumentException
     * @throws \Slairmy\Weather\Exceptions\HttpException
     */
    public function testGetWeatherWithInvalidType()
    {
        $weather = new Weather('mock-key');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid type value(base/all): foo");

        $weather->getWeather('深圳', 'foo');
        $this->fail("Failed to assert getWeather throw exception with invalid type");

    }

    /**
     * @desc testGetWeatherWithInvalidFormat
     * @throws InvalidArgumentException
     * @throws \Slairmy\Weather\Exceptions\HttpException
     */
    public function testGetWeatherWithInvalidFormat()
    {
        $weather = new Weather('mock-key');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid response type(json/xml): bson");

        $weather->getWeather('深圳', 'base', 'bson');
        $this->fail("Failed to assert getWeather throw exception with invalid format");
    }

}