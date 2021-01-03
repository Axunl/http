<?php

namespace http\client;

/**
 * Class HttpClient
 * @package axunl\http
 */
abstract class HttpClient
{
    /**
     * @var string
     */
    const GET = 'get';

    /**
     * @var string
     */
    const POST = 'post';

    /**
     * @var string
     */
    const PUT = 'put';

    /**
     * @var string
     */
    const DELETE = 'delete';

    /**
     * @var string
     */
    const PATCH = 'patch';

    /**
     * @var string
     */
    const JSON = 'json';

    /**
     * 请求url
     * @var string
     */
    protected $url;

    /**
     * 头部
     * @var array
     */
    protected $headers = [];

    /**
     * 请求参数
     * @var array
     */
    protected $params = [];

    /**
     * 请求方法
     * @var string
     */
    protected $method;

    /**
     * 超时时间(s)
     * @var int
     */
    protected $timeout = 5;

    /**
     * 失败重连次数
     * @var int
     */
    protected $failReplyNum = 1;

    /**
     * 异常处理
     * @var callable
     */
    protected $handle;

    /**
     * HttpClient constructor.
     */
    private function __construct()
    {

    }

    /**
     * @return string
     */
    protected function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return HttpClient
     */
    protected function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getHeaders($name = '')
    {
        return $name ? $this->headers[$name] : $this->headers;
    }

    /**
     * @param array $headers
     * @return HttpClient
     */
    public function setHeaders($headers)
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getParams($name = '')
    {
        return $name ? $this->params[$name] : $this->params;
    }

    /**
     * @param array $params
     * @return HttpClient
     */
    public function setParams($params)
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * @param string $method
     * @return HttpClient
     */
    protected function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     * @return HttpClient
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @return int
     */
    public function getFailReplyNum()
    {
        return $this->failReplyNum;
    }

    /**
     * @param int $failReplyNum
     * @return HttpClient
     */
    public function setFailReplyNum($failReplyNum)
    {
        $this->failReplyNum = $failReplyNum;
        return $this;
    }

    /**
     * @return callable
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @param callable $handle
     * @return HttpClient
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
        return $this;
    }

    /**
     * isHttp
     * @return bool
     */
    public function isssl()
    {
        return substr($this->url, 0, 8) === "https://";
    }

    /**
     * get
     * @param $url
     * @param array $params
     * @param array $headers
     * @return mixed
     * @throws HttpClientException
     */
    public function get($url, $params = [], $headers = [])
    {
        $_params = [];
        // 含有?的url处理
        if (mb_strpos($url, '?') !== false) {
            $data = explode('?', $url);
            $url = $data[0];
            $_params = array_merge($_params, explode('&', $data[1]));
        }
        foreach ($params as $k => $v) {
            $_params[] = $k . '=' . $v;
        }
        $url .= '?' . implode('&', $_params);
        $this->method = self::GET;
        return $this->request($url, $_params, $headers);
    }

    /**
     * post
     * @param $url
     * @param array $params
     * @param array $headers
     * @return mixed
     * @throws HttpClientException
     */
    public function post($url, $params = [], $headers = [])
    {
        $this->method = self::POST;
        return $this->request($url, $params, $headers);
    }

    /**
     * put
     * @param $url
     * @param array $params
     * @param array $headers
     * @return mixed
     * @throws HttpClientException
     */
    public function put($url, $params = [], $headers = [])
    {
        $this->method = self::PUT;
        return $this->request($url, $params, $headers);
    }

    /**
     * delete
     * @param $url
     * @param array $params
     * @param array $headers
     * @return mixed
     * @throws HttpClientException
     */
    public function delete($url, $params = [], $headers = [])
    {
        $this->method = self::DELETE;
        return $this->request($url, $params, $headers);
    }

    /**
     * delete
     * @param $url
     * @param array $params
     * @param array $headers
     * @return mixed
     * @throws HttpClientException
     */
    public function patch($url, $params = [], $headers = [])
    {
        $this->method = self::PATCH;
        return $this->request($url, $params, $headers);
    }

    /**
     * json
     * @param $url
     * @param array $params
     * @param array $headers
     * @return mixed
     * @throws HttpClientException
     */
    public function json($url, $params = [], $headers = [])
    {
        $this->method = self::JSON;
        return $this->request($url, $params, $headers);
    }

    /**
     * request
     * @param $url
     * @param $params
     * @param $headers
     * @return mixed
     * @throws HttpClientException
     */
    protected function request($url, $params = [], $headers = [])
    {
        $this->setUrl($url)
            ->setParams($params)
            ->setHeaders($headers);
        $failReplyNum = 0;
        while ($failReplyNum <= $this->getFailReplyNum()) {
            $response = $this->_request();
            $failReplyNum++;
            if ($response->code === 200) {
                $this->params = [];
                return json_decode($response->data, false) ?: $response->data;
                break;
            }
        }
        // 错误处理
        $this->params = [];
        if (isset($response) && $this->handle) {
            ($this->handle)($response);
        } else {
            throw new HttpClientException('request error');
        }
    }


    /**
     * @param $class
     * @return HttpClient
     * @throws HttpClientException
     */
    public static function factory($class)
    {
        try {
            $class = (new  $class);
            if ($class instanceof self) {
                return $class;
            }
            throw new HttpClientException('');
        } catch (\Exception $exception) {
            throw new  HttpClientException('must instanceof ' . __CLASS__);
        }
    }

    /**
     * @return Response
     */
    abstract protected function _request();
}