## Weather
基于高德开放平台的PHP天气组件

## 安装
`composer require slairmy/weather -vvv`

## 配置
高德开放平台天气应用APP KEY

## 使用
```
use Slairmy\Weather\Weather
$key = "{YOUR APP KEY}"
$weather = new Weather($key);

获取实时天气
$weather->getWeather("深圳")

获取最近的天气状况
$weather->getWeather("深圳", "all")

获取XML格式信息
$weather->getWeather("深圳", "all", "xml")

```

## 参数说明

```
array|string getWeather(string $city, string $type = 'base', string $format = 'json')
```

## 在laravel中使用
`config/services.php`中添加如下配置

```php
    'weather' => [
        'key' => env('WEATHER_API_KEY')
    ]
```

`.env`中添加配置`WEATHER_API_KEY={YOUR APP KEY}`

## License
MIT
