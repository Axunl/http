# HttpClient

> 极易上手的Http客户端

## 示例

```php
// 1、设置请求的url链接
$url = 'https://www.baidu.com';
// 2、设置请求参数
$params = [
    'foo' => 'foo',
    'bar' => 'bar'
];
// 3、从容器中取出Http客户端
$client = (new \axunl\httpClient\Container())->simple;
// 4、发送请求
$data = $client->get($url);
```

## 支持请求的方式

- get
- post
- put
- patch
- delete
- json

## 为请求设置异常处理

```php
$client = (new \axunl\httpClient\Container())->simple;
$client->setHandle(function (\axunl\httpClient\Response $response) {
// write your code
});
```

## 致谢

如果觉得好用，可以fork或者star本仓库