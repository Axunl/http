<?php

namespace axunl\http\client\implement;

use axunl\http\client\HttpClient;
use axunl\http\client\Response;

/**
 * 基于curl的httpClient实现
 * Class SimpleHttpClient
 * @package axunl\http\implement
 */
class SimpleHttpClient extends HttpClient
{
    /**
     * options
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
     * @param array $options
     * @return SimpleHttpClient
     */
    public function setOptions($options)
    {
        $this->options[] = $options;
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function _request()
    {
        $this->setBaseOptions()
            ->setOptions([CURLOPT_URL, $this->getUrl()]);
        switch ($this->getMethod()) {
            case self::GET:
                $this->setOptions([CURLOPT_HTTPGET, 1]);
                break;
            case self::POST:
                $this->setOptions([CURLOPT_POST, 1])
                    ->setOptions([CURLOPT_POSTFIELDS, http_build_query($this->params)]);
                break;
            case self::PUT:
                $this->setOptions([CURLOPT_CUSTOMREQUEST, 'PUT'])
                    ->setOptions([CURLOPT_POSTFIELDS, http_build_query($this->params)]);
                break;
            case self::DELETE:
                $this->setOptions([CURLOPT_CUSTOMREQUEST, 'DELETE'])
                    ->setOptions([CURLOPT_POSTFIELDS, http_build_query($this->params)]);
                break;
            case self::PATCH:
                $this->setOptions([CURLOPT_CUSTOMREQUEST, 'PATCH'])
                    ->setOptions([CURLOPT_POSTFIELDS, http_build_query($this->params)]);
                break;
            case self::JSON:
                $this->setOptions([CURLOPT_HTTPHEADER, ['Content-type: application/json']])
                    ->setOptions([CURLOPT_POST, 1])
                    ->setOptions([CURLOPT_POSTFIELDS, json_encode($this->params, 256)]);
                break;
            default:
                break;
        }
        return $this->_setHeaders()->deal();
    }

    /**
     * setHeader
     */
    private function _setHeaders()
    {
        $_header = [];
        foreach ($this->getHeaders() as $k => $v) {
            $_header[] = $k . ':' . $v;
        }
        $_header[] = 'Content-type:application/x-www-form-urlencoded';
        $this->setOptions([CURLOPT_HTTPHEADER, $_header]);
        return $this;
    }

    /**
     * @return Response
     */
    private function deal()
    {
        $ch = curl_init();
        foreach ($this->getOptions() as $v) {
            curl_setopt($ch, $v[0], $v[1]);
        }
        $res = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $header = curl_getinfo($ch);//获取头信息
        curl_close($ch);
        return new Response($this->url, $header, $code, $res);
    }

    /**
     * 基础的options
     * @return $this
     */
    private function setBaseOptions()
    {
        $this->setOptions([CURLOPT_TIMEOUT, $this->timeout])
            ->setOptions([CURLOPT_RETURNTRANSFER, 1]);
        // 自动判断ssl
        if ($this->isssl()) {
            $this->setOptions([CURLOPT_SSL_VERIFYPEER, 0])
                ->setOptions([CURLOPT_SSL_VERIFYHOST, 0]);
        }
        return $this;
    }
}