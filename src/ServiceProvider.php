<?php
/**
 * Created by PhpStorm
 * User: slairmy
 * Date: 2020/3/10
 * Time: 4:28 下午
 */

namespace Slairmy\Weather;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton(Weather::class, function (){
            return new Weather(config('services.weather.key'));
        });

        $this->app->alias(Weather::class, 'weather');
    }

    public function provides()
    {
        return [Weather::class, 'weather'];
    }
}