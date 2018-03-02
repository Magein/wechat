<?php

namespace Magein\Wechat;

/**
 * 客服账号类
 * Class Service
 * @package Magein\Wechat
 */
class Service
{
    /**
     * @var string
     */
    private $module = 'customservice';

    /**
     * @var array
     */
    private $urlMapping = [
        'add' => 'kfaccount/add',    // 添加客服账号
        'update' => 'kfaccount/update', // 修改客服账号
        'delete' => 'kfaccount/del',  // 删除客户账号
        'getList' => 'getkflist',    // 获取客服账号列表

    ];

    /**
     * @var array
     */
    private $urlParam;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * Material constructor.
     * @param string $accessToken
     */
    public function __construct(string $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * 直接返回请求的结果，获取图片素材的时候输出的是图片资源,所以不要将结果直接使用json_decode操作
     * @param string $url
     * @param array $postData 不支持二维数组发送
     * @param array $options
     * @return mixed|null
     */
    private function doCurl(string $url, array $postData = [], array $options = [])
    {
        $url = $this->transUrl($url);

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
     * @param string $url
     * @return string
     */
    private function transUrl(string $url): string
    {
        $this->urlParam['access_token'] = $this->accessToken;

        $url = 'https://api.weixin.qq.com/' . $this->module . '/' . $this->urlMapping[$url];

        return $url . '?' . http_build_query($this->urlParam);
    }

    /**
     * @param string $kfAccount
     * @param string $nickName
     * @param string $password
     * @return mixed|null
     */
    public function add(string $kfAccount, string $nickName, string $password)
    {
        $postData['kf_account'] = $kfAccount;
        $postData['nickname'] = $nickName;
        $postData['password'] = $password;

        $data = $this->doCurl(__FUNCTION__, $postData);

        return $data;
    }

    /**
     * @param string $kfAccount
     * @param string $nickName
     * @param string $password
     * @return mixed|null
     */
    public function update(string $kfAccount, string $nickName, string $password)
    {
        $postData['kf_account'] = $kfAccount;
        $postData['nickname'] = $nickName;
        $postData['password'] = $password;

        $data = $this->doCurl(__FUNCTION__, $postData);

        return $data;
    }

    /**
     * @param string $kfAccount
     * @param string $nickName
     * @param string $password
     * @return mixed|null
     */
    public function delete(string $kfAccount, string $nickName, string $password)
    {
        $postData['kf_account'] = $kfAccount;
        $postData['nickname'] = $nickName;
        $postData['password'] = $password;

        $data = $this->doCurl(__FUNCTION__, $postData);

        return $data;
    }

    /**
     * @param string $kfAccount
     * @param string $path 请使用绝对路径，格式为jpg
     * @return mixed|null
     */
    public function setHead(string $kfAccount, string $path)
    {
        $this->urlParam['kf_account'] = $kfAccount;

        $postData = new \CURLFile($path);

        $data = $this->doCurl(__FUNCTION__, $postData);

        return $data;
    }

    /**
     * @return mixed|null
     */
    public function getList()
    {
        $this->module = 'cgi-bin/customservice';

        $data = $this->doCurl(__FUNCTION__);

        return $data;
    }

}