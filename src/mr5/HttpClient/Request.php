<?php
// +----------------------------------------------------------------------
// | HttpClient类库 HTTP client Library http://github.com/mr5/HttpClient
// +----------------------------------------------------------------------
// | Author: Mr.5 <mr5.simple@gmail.com>
// +----------------------------------------------------------------------
// + Datetime: 14-4-20 下午3:53
// +----------------------------------------------------------------------
// + Request.php
// +----------------------------------------------------------------------

namespace mr5\HttpClient;


class Request
{
    /**
     * @const string
     */
    const METHOD_GET  = 'GET';
    /**
     * @const string
     */
    const METHOD_POST = 'POST';
    /**
     * @const string
     */
    const METHOD_HEAD = 'HEAD';
    /**
     * @const string
     */
    const METHOD_DELETE = 'DELETE';
    /**
     * @const string
     */
    const METHOD_PUT = 'PUT';
    /**
     * @const string
     */
    const METHOD_OPTIONS = 'OPTIONS';
    /**
     * USER-AGENT信息
     * @var string
     */
    protected $userAgent = NULL;
    /**
     * cookie信息数组
     * @var array
     */
    protected $cookies = array();
    /**
     * 头部信息
     * @var array
     */
    protected $headers = array();
    /**
     * 超时时间
     * @var int
     */
    protected $timeout = 30;
    /**
     * HTTP method
     * @var string
     */
    protected $method = self::METHOD_GET;
    /**
     * 请求地址
     * url for request
     * @var string
     */
    protected $url = NULL;
    /**
     * 来源
     * @var string
     */
    protected $referer = NULL;
    /**
     * 基础认证信息
     * @var array
     */
    protected $baseAuth = array();
    /**
     * 重定向最大次数
     * @var int
     */
    protected $maxRedirects = 4;
    /**
     * post参数
     * @var array|string
     */
    protected $postParams = NULL;
    /**
     * get参数，将被追加到url中，可以跟postParams共存
     * params in query string, it can be used together with `postParams`
     * @var array|string
     */
    protected $getParams = NULL;
    /**
     * 是否二进制传输
     * is it binary transformation
     * @var bool
     */
    protected $binary = FALSE;

    /**
     * @param boolean $binary
     */
    public function setBinary($binary)
    {
        $this->binary = $binary;
    }

    /**
     * @return boolean
     */
    public function isBinary()
    {
        return $this->binary;
    }

    /**
     * 重新设置所有的get参数
     * @param array|string $getParams
     */
    public function setGetParams($getParams)
    {
        $this->getParams = $getParams;
    }

    /**
     * 获取所有的get参数
     * @return array|string
     */
    public function getGetParams()
    {
        return $this->getParams;
    }

    /**
     * 添加GET参数
     * @param string $key
     * @param string $value
     */
    public function addGetParam($key, $value)
    {
        $this->getParams[$key] = $value;
    }

    /**
     * 获取指定键的get参数
     * @param string $key
     * @return string mixed
     */
    public function getGetParam($key)
    {
        return $this->getParams[$key];
    }
    /**
     * 重新设置POST参数，可以是字符串或数组
     * @param array|string $postParams
     */
    public function setPostParams($postParams)
    {
        $this->postParams = $postParams;
    }

    /**
     * 获取所有的POST参数
     * @return array|string
     */
    public function getPostParams()
    {
        return $this->postParams;
    }

    /**
     * 添加POST参数
     * @param string $key
     * @param string $value
     */
    public function addPostParam($key, $value)
    {
        $this->postParams[$key] = $value;
    }

    /**
     * 获取指定键的post参数
     * @param string $key
     * @return mixed
     */
    public function getPostParam($key)
    {
        return $this->postParams[$key];
    }
    /**
     * @param string $username
     * @param string $password
     */
    public function setBaseAuth($username, $password)
    {
        $this->baseAuth = array(
            'username' => $username,
            'password' => $password
        );
    }

    /**
     * @return array
     */
    public function getBaseAuth()
    {
        return $this->baseAuth;
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
     * @param int $maxRedirects
     */
    public function setMaxRedirects($maxRedirects)
    {
        $this->maxRedirects = $maxRedirects;
    }

    /**
     * @return int
     */
    public function getMaxRedirects()
    {
        return $this->maxRedirects;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $method = strtoupper($method);
        if(!in_array($method,
            array(self::METHOD_GET,
                self::METHOD_POST,
                self::METHOD_DELETE,
                self::METHOD_HEAD,
                self::METHOD_PUT))) {
            $method = self::METHOD_GET;
        }
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }



    /**
     * @param string $referer
     */
    public function setReferer($referer)
    {
        $this->referer = $referer;
    }

    /**
     * @return string
     */
    public function getReferer()
    {
        return $this->referer;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param string $url
     * @param array $getParams
     */
    public function setUrl($url, $getParams=NULL)
    {
        $this->url = $url;
        if(is_array($getParams) && count($getParams) > 0) {
            $this->setGetParams($getParams);
        }
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $userAgent
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * 重置所有配置信息
     * reset all `Request` properties to default
     */
    public function reset()
    {
        $this->userAgent = NULL;
        $this->cookies = array();
        $this->headers = array();
        $this->timeout = 30;
        $this->method = self::METHOD_GET;
        $this->url = NULL;
        $this->referer = NULL;
        $this->baseAuth = array();
        $this->maxRedirects = 4;
        $this->postParams = NULL;
        $this->getParams = NULL;
    }
}