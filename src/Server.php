<?php

namespace Magein\Wechat;

/**
 * 服务器端配置
 * Class Server
 * @package Magein\Wechat
 */
class Server
{
    /**
     * @var
     */
    private $config;

    /**
     * Server constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * 配置服务器验证方法
     * @param array $request
     * @param string $token
     * @return bool
     */
    public function checkSignature(array $request, string $token)
    {
        $signature = $request['signature'];
        $param['timestamp'] = $request['timestamp'];
        $param['nonce'] = $request['nonce'];
        $param['token'] = $token;
        sort($param, SORT_STRING);
        $tmpStr = sha1(implode($param));
        if ($signature == $tmpStr) {
            return true;
        }

        return false;
    }

    /**
     * @param string $url
     * @param array $postData
     * @param array $options
     * @return mixed|null
     */
    private function doCurl(string $url, array $postData = [], array $options = [])
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if ($postData) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData, JSON_UNESCAPED_UNICODE));
        }

        if ($options) {
            curl_setopt_array($ch, $options);
        }

        $data = curl_exec($ch);

        if (curl_errno($ch)) {
            return null;
        }

        curl_close($ch);

        return $data;
    }

    /**
     * @param string|null $appid
     * @param string|null $secret
     * @return null
     */
    public function getAccessToken(string $appid = null, string $secret = null)
    {
        if (isset($_COOKIE['access_token'])) {
            return $_COOKIE['access_token'];
        }

        $appid = $appid ?: $this->config['appid'];
        $secret = $secret ?: $this->config['secret'];

        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $secret;

        $data = $this->doCurl($url);

        $data = json_decode($data, true);

        if (isset($data['access_token'])) {
            setcookie('access_token', $data['access_token'], $data['expires_in']);
            return $data['access_token'];
        }

        return '';
    }

    public function listen()
    {

    }
}