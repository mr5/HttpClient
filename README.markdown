# INTRODUCTION

[中文文档](README_zh.markdown)

HttpClient is a library for HTTP client library which based on cURL. It has 3 class only, the `HttpClient::execute()`
 method accept a `HttpClientRequest` object, and return a `HttpClientResponse` object. Most of cURL options can be allocated
 with `HttpClientRequest` class(such as `getParams`, `postParams`, `referer`, `cookies`, `user-agent` and so on),
 and the `HttpClientResponse` provide most of HTTP response info, such as cookies, body, headers.

# USAGE

``` php
<?php
use HttpClient\HttpClient;
use HttpClient\HttpClientRequest;
use HttpClient\HttpClientResponse;

$request = new HttpClientRequest();
$request->setUrl('https://accounts.google.com/ServiceLogin');
// Params append to url.
$request->setGetParams(array('hl'=>'zh-CN', 'continue'=>'http://www.google.com.hk'));
$request->setPostParams(array('username'=>'some username', 'password'=>'mypassword'));
$request->setMethod(HttpClientRequest::METHOD_POST);

$response = HttpClient::execute($request);

var_dump($response->getHttpStatusCode());
var_dump($response->getHeaders());
var_dump($response->getBody());
var_dump($response->getCookies());
```

# MORE INFORMATION

For more information, please read the comments in source code  .

# LICENSE

MIT

