# httpClient
## 概述
php模拟http请求的客户端
## 快速开始
```php
$client = \axunl\http\client\HttpClient::factory(\axunl\http\client\implement\SimpleHttpClient::class);
$url = '';
$params = [
    'foo' => 'bar'
];
$headers = [
    'header' => ''
];
$res = $client->get($url, $params, $headers);
```

- 内置SimpleHttpClient(需安装curl扩展)与GuzzleHttpClient(需引入GuzzleHttp包)
- 支持GET、POST、PUT、DELETE、PATCH、JSON请求

## 自定义exception-handle
```php
$client = \axunl\http\client\HttpClient::factory(\axunl\http\client\implement\SimpleHttpClient::class);
$client->setHandle(function ($response) {
# todo
});
```
- 像这样的，在http请求时出现异常会走到自定义异常的处理中而并不是直接抛出一个异常

## 自定义httpClient类

``` php

class MyHttpClient extends \axunl\http\client\HttpClient
{

    /**
     * @inheritDoc
     */
    protected function _request()
    {
        // TODO: Implement _request() method.
    }
}
```
- 需要实现_request方法并返回一个Response的对象(具体参照SimpleHttpClient类)即可扩展httpClient类

