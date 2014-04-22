# 介绍

HttpClient是一个基于PHP的cURL扩展的HTTP客户端库。它有三个类, `HttpClient::execute()`
 方法接收一个`HttpClientRequest`对象, 返回一个`HttpClientResponse`对象. 绝大部分的cURL参数都可以通过
 `HttpClientRequest` 类来配置(例如`GET参数`、`POST参数`、`来源`、`cookies`、`user-agent`等等)，
  而HTTP响应的大部分消息都可以通过`HttpClientResponse`对象来获取, 比如cookies、正文、HTTP头部等等。

# 使用示例

``` php
<?php
use mr5\HttpClient\HttpClient;
use mr5\HttpClient\HttpClientRequest;
use mr5\HttpClient\HttpClientResponse;

$request = new HttpClientRequest();
$request->setUrl('https://accounts.google.com/ServiceLogin');
// 添加到URL的参数(GET参数).
$request->setGetParams(array('hl'=>'zh-CN', 'continue'=>'http://www.google.com.hk'));
// post参数
$request->setPostParams(array('username'=>'some username', 'password'=>'mypassword'));
$request->setMethod(HttpClientRequest::METHOD_POST);

$response = HttpClient::execute($request);

var_dump($response->getHttpStatusCode());
var_dump($response->getHeaders());
var_dump($response->getBody());
var_dump($response->getCookies());
```

# More Information

更多信息请参考源代码中的注释。

# License
MIT

