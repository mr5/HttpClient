<?php
// +----------------------------------------------------------------------
// | HttpClient类库 HTTP client Library http://github.com/mr5/HttpClient
// +----------------------------------------------------------------------
// | Author: Mr.5 <mr5.simple@gmail.com>
// +----------------------------------------------------------------------
// + Datetime: 14-4-20 下午3:53
// +----------------------------------------------------------------------
// + HttpClientResponse.class.php
// +----------------------------------------------------------------------

namespace mr5\HttpClient;


class HttpClientResponse
{
    /**
     * @var array
     */
    protected $headers = array();
    /**
     * HTTP状态码
     * HTTP status code
     * @var int
     */
    protected $httpStatusCode = 0;
    /**
     * HTTP响应正文
     * HTTP response content body
     * @var string
     */
    protected $body = NULL;
    /**
     * cookies array
     * @var array
     */
    protected $cookies = array();
    /**
     * 这个http请求所消耗的时间，单位是秒
     * Time cost of this connection
     * @var float
     */
    protected $timeCost = -1;

    /**
     * @param float $timeCost
     */
    public function setTimeCost($timeCost)
    {
        $this->timeCost = $timeCost;
    }

    /**
     * @return int
     */
    public function getTimeCost()
    {
        return $this->timeCost;
    }
    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param int $httpStatusCode
     */
    public function setHttpStatusCode($httpStatusCode)
    {
        $this->httpStatusCode = $httpStatusCode;
    }

    /**
     * @return int
     */
    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }

    /**
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * 获取指定键的header值
     * Get header value by key
     * @param $key
     * @return mixed
     */
    public function getHeader($key)
    {
        return $this->headers[$key];
    }


    /**
     * 获取指定键的cookie值
     * get cookie value by  key
     * @param $key
     */
    public function getCookie($key)
    {
        return $this->cookies[$key];
    }
    /**
     * @param array $cookies
     */
    public function setCookies($cookies)
    {
        $this->cookies = $cookies;
    }

    /**
     * @return array
     */
    public function getCookies()
    {
        return $this->cookies;
    }

} 