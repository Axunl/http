<?php

namespace axunl\httpClient\implement;

use axunl\httpClient\HttpClient;
use axunl\httpClient\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

/**
 * GuzzleHttpClient
 * Class GuzzleHttpClient
 * @package axunl\httpClient\implement
 * @link https://guzzle-cn.readthedocs.io/zh_CN/latest/quickstart.html
 */
class GuzzleHttpClient extends HttpClient
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param $key
     * @param $value
     * @return GuzzleHttpClient
     */
    public function setOptions($key, $value)
    {
        $this->options[$key] = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function _request()
    {
        $this->client = new Client();
        switch ($this->getMethod()) {
            case self::GET:
                $this->request = new Request(self::GET, $this->url);
                break;
            case self::POST:
                $this->request = new Request(self::POST, $this->url);
                $this->setOptions(RequestOptions::FORM_PARAMS, $this->params);
                break;
            case self::PUT:
                $this->request = new Request(self::PUT, $this->url);
                $this->setOptions(RequestOptions::FORM_PARAMS, $this->params);
                break;
            case self::DELETE:
                $this->request = new Request(self::DELETE, $this->url);
                $this->setOptions(RequestOptions::FORM_PARAMS, $this->params);
                break;
            case self::PATCH:
                $this->request = new Request(self::PATCH, $this->url);
                $this->setOptions(RequestOptions::FORM_PARAMS, $this->params);
                break;
            case self::JSON:
                $this->request = new Request(self::POST, $this->url);
                $this->setOptions(RequestOptions::JSON, $this->params);
                break;
            default:
                break;
        }
        $this->setBaseOptions();
        return $this->deal();
    }

    /**
     * @return Response
     */
    private function deal()
    {
        return $this->client->sendAsync($this->request, $this->getOptions())
            ->then(
                function (ResponseInterface $response) {
                    return new Response($this->url, $response->getHeaders(), $response->getStatusCode(), $response->getBody()->getContents());
                },
                function (RequestException $response) {
                    return new Response($this->url, $response->getRequest()->getHeaders(), $response->getCode(), $response->getRequest()->getBody()->getContents());
                }
            )
            ->wait();
    }

    /**
     * 基础的options
     * @return $this
     */
    private function setBaseOptions()
    {
        $this->setOptions(RequestOptions::TIMEOUT, $this->timeout)
            ->setOptions(RequestOptions::HEADERS, $this->headers)
            ->setOptions(RequestOptions::SYNCHRONOUS, true);//设置成 true 来通知HTTP处理器你要等待响应，这有利于优化。
        // 自动判断ssl
        if ($this->isssl()) {
            $this->setOptions(RequestOptions::VERIFY, false);
        }
        return $this;
    }
}