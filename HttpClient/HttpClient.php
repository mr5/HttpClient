<?php
// +----------------------------------------------------------------------
// | HttpClient类库 HTTP client Library http://github.com/mr5/HttpClient
// +----------------------------------------------------------------------
// | Author: Mr.5 <mr5.simple@gmail.com>
// +----------------------------------------------------------------------
// + Datetime: 14-4-20 下午3:53
// +----------------------------------------------------------------------
// + HttpClient.php
// +----------------------------------------------------------------------

namespace HttpClient;


class HttpClient
{
    /**
     * 通过`HttpClientRequest`执行CURL操作, `HttpClientRequest`可以配置绝大多数的CURL参数。
     * Execute cURL with `HttpClientRequest`, most cURL options can config by `HttpClientRequest`
     * @param HttpClientRequest $httpRequest
     * @return HttpClientResponse
     */
    static public function execute(HttpClientRequest $httpRequest)
    {
        $url = $httpRequest->getUrl();

        // 追加GET参数到URL
        // Append `GET` params to URL
        if($httpRequest->getGetParams() && count($httpRequest->getGetParams()) > 0) {
            $url = self::urlAddParams($url, $httpRequest->getGetParams());

        }
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_TIMEOUT, $httpRequest->getTimeout());


        if($httpRequest->getMaxRedirects() < 1) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        } else {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, $httpRequest->getMaxRedirects());
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // 设置HTTP BASE AUTH
        $baseAuthInfo = $httpRequest->getBaseAuth();
        if(is_array($baseAuthInfo) && $baseAuthInfo['username']  && $baseAuthInfo['password']) {
            curl_setopt($ch, CURLOPT_USERPWD, $baseAuthInfo['username'].':'.$baseAuthInfo['password']);
        }

        // 设置自定义http method name
        if(!in_array($httpRequest->getMethod(), array(HttpClientRequest::METHOD_GET, HttpClientRequest::METHOD_POST))) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $httpRequest->getMethod());
        }

        // 设置post参数
        if($httpRequest->getPostParams()) {
            curl_setopt($ch, CURLOPT_POST, true);
            $_postFields = $httpRequest->getPostParams();
            if(is_array($_postFields)) {
                $_postFields = http_build_query($httpRequest->getPostParams());
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $_postFields);
        }

        // 设置COOKIE信息
        if($httpRequest->getCookies()) {
            $_cookies = $httpRequest->getCookies();
            curl_setopt($ch, CURLOPT_COOKIE, is_array($_cookies) ? self::buildHttpCookies($_cookies) : $_cookies);
        }

        // 设置header信息
        $_headers = array('Expect:');
        if($httpRequest->getHeaders()) {
            $_headers = array_merge($_headers, self::buildHttpHeaders($httpRequest->getHeaders()));
        }
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $_headers);

        // 无body
        // `CURLOPT_NOBODY` will be set `true` when the method is `HttpClientRequest::METHOD_HEAD`
        if($httpRequest->getMethod() == HttpClientRequest::METHOD_HEAD) {
            curl_setopt($ch, CURLOPT_NOBODY, true);
        }

        // 二进制传输
        // Set Binary
        if($httpRequest->isBinary()) {
            curl_setopt($ch,CURLOPT_BINARYTRANSFER, true);
        }

        // 设置用户代理(USER-AGENT)
        // Set USER-AGENT
        if($httpRequest->getUserAgent()) {
            curl_setopt($ch,CURLOPT_USERAGENT, $httpRequest->getUserAgent());
        }

        // 设置来源网址
        // Set referer
        if($httpRequest->getReferer()) {
            curl_setopt($ch,CURLOPT_REFERER, $httpRequest->getReferer());
        }
        
        $httpResponse = new HttpClientResponse();

        list($header_string, $body) = explode("\r\n\r\n", curl_exec($ch), 2);
        $httpResponse->setHeaders(self::parseHttpHeaders($header_string));
        $httpResponse->setCookies(self::parseHttpCookies($httpResponse->getHeader('Set-Cookie')));
        $httpResponse->setBody($body);
        $httpResponse->setTimeCost(curl_getinfo($ch, CURLINFO_CONNECT_TIME));
        $httpResponse->setHttpStatusCode(curl_getinfo($ch,CURLINFO_HTTP_CODE));

        curl_close($ch);
        return $httpResponse;
    }
    static public function urlAddParams($url, $params)
    {
        $urlParsed = parse_url($url);

        // 如果没有query并且没有以 / 结尾，则在url的尾部加上 /
        // Append `/` to URL when the URL has no `query string` and it's not ends with `/`
        if(!$urlParsed['path'] && !self::endsWith($url, '/')) {
            $url .= '/';
        }

        if(!$urlParsed['query']) {
            $url .= '?';
        } else {
            $url .= '&';
        }

        $url .= http_build_query($params);
        return $url;
    }
    /**
     * 使用GET方法快速获取指定URL的响应正文
     * Fetch a given URL with `GET` method
     * @param $url
     * @param array|string $params
     * @param int $timeout
     * @return string
     */
    static public function fetch($url, $params = array(), $timeout=30)
    {
        $httpRequest = new HttpClientRequest();
        $httpRequest->setUrl($url);
        $httpRequest->setMethod(HttpClientRequest::METHOD_GET);
        $httpRequest->setGetParams($params);
        $httpRequest->setTimeout($timeout);

        $httpResponse = self::execute($httpRequest);
        return $httpResponse->getBody();
    }

    /**
     * 使用`POST`方法提交数据到指定URL
     * Post some data to a given URL.
     * @param string        $url
     * @param array|string  $postParams Post body, It's the `query string` when string type given
     * @param array|string  $getParams  Params append to URL
     * @param int $timeout
     * @return string
     */
    static public function post($url, $postParams = NULL, $getParams = NULL, $timeout = 30) {
        $httpRequest = new HttpClientRequest();
        $httpRequest->setUrl($url);
        $httpRequest->setMethod(HttpClientRequest::METHOD_POST);
        $httpRequest->setGetParams($getParams);
        $httpRequest->setPostParams($postParams);
        $httpRequest->setTimeout($timeout);

        $httpResponse = self::execute($httpRequest);
        return $httpResponse->getBody();
    }
    /**
     * 将Headers字符串信息解析成数组
     * Parse HTTP headers string to key-value array
     * @param string $headersStr
     * @return array
     */
    static public function parseHttpHeaders($headersStr)
    {
        $headers = explode("\r\n", $headersStr);
        $items = array();
        if(strpos('http', strtolower($headers[0]))) {
            $items['_protocol'] = $headers[0];
            unset($headers[0]);
        }

        foreach($headers as $header) {
            $header = trim($header);
            list($k, $v) = explode(':', $header);
            $items[trim($k)] = trim($v);
        }
        return $items;
    }

    /**
     * 将headers键值对数组转成cURL所需要的数组格式
     * Build headers key-value array to format of php cURL method
     * @param array $headersArr
     * @param bool $makeArray       [option]是否需要生成数组，默认为true，当为false时，会生成使用\r\n分割每条header的字符串。
     * @return array|string
     */
    static public function buildHttpHeaders($headersArr, $makeArray = TRUE)
    {
        $_headers = array();
        foreach($headersArr AS $_headerKey => $_headerValue) {
            $_headers[] = $_headerKey.':'.$_headerValue;
        }
        if(!$makeArray) {
            $_headers = implode("\r\n", $_headers);
        }
        return $_headers;
    }
    /**
     * 将cookies字符串解析成数组
     * Parse cookies string to array
     * @param $cookiesStr
     * @return array
     */
    static public function parseHttpCookies($cookiesStr)
    {
        $cookies = explode(";", $cookiesStr);
        $cookiesArray = array();
        foreach($cookies as $cookie) {
            $cookie = trim($cookie);
            list($k, $v) = explode('=', $cookie);
            $cookiesArray[trim($k)] = trim($v);
        }
        return $cookiesArray;
    }

    /**
     * 将cookies数组解析成字符串
     * Build cookies array to string
     * @param array $cookiesArr
     * @return string
     */
    static public function buildHttpCookies($cookiesArr)
    {
        $cookiesStr = '';
        foreach($cookiesArr AS $_k=>$v) {
            if($cookiesStr != '') {
                $cookiesStr .= '; ';
            }
            $cookiesStr .= $_k.'='.$v;
        }
        return $cookiesStr;
    }

    /**
     * 判断`$haystack`是否以`$needles`结尾
     * Determine if the given haystack ends with a given needle.
     * @param string $haystack
     * @param string $needles
     * @return bool
     */
    static private function  endsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle == substr($haystack, -strlen($needle))) return true;
        }
        return FALSE;
    }
}