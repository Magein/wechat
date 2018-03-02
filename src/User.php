<?php

namespace Magein\Wechat;

/**
 * 用户类
 * Class User
 * @package Magein\Wechat
 */
class User
{
    /**
     * @var string
     */
    private $module = 'user';

    /**
     * @var array
     */
    private $urlMapping = [
        'getUserInfo' => 'info',    // 创建标签
        'getList' => 'get',    // 获取用户列表
        'setUserRemark' => 'info/updateremark', // 设置用户备注姓名
        'getUserListByTagId' => 'tag/get',  // 删除标签
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

        $url = 'https://api.weixin.qq.com/cgi-bin/' . $this->module . '/' . $this->urlMapping[$url];

        return $url . '?' . http_build_query($this->urlParam);
    }

    /**
     * @param string $nextOpenid
     * @return mixed|null
     */
    public function getList(string $nextOpenid = '')
    {
        $this->urlParam['next_openid'] = $nextOpenid;

        $data = $this->doCurl(__FUNCTION__);

        return $data;
    }

    /**
     * @param string $openid
     * @param string $remark
     * @return mixed|null
     */
    public function setUserRemark(string $openid, string $remark)
    {
        $postData['openid'] = $openid;
        $postData['remark'] = $remark;

        $data = $this->doCurl(__FUNCTION__, $postData);

        return $data;
    }

    /**
     * @param string $openid
     * @param string $lang
     * @return mixed|null
     */
    public function getUserInfo(string $openid, string $lang = 'zh_CN')
    {
        $this->urlParam['openid'] = $openid;
        $this->urlParam['lang'] = $lang;

        $data = $this->doCurl(__FUNCTION__);

        return $data;
    }

    /**
     * @param string $tagid
     * @param string $nextOpenid
     * @return mixed|null
     */
    public function getUserListByTagId(string $tagid, string $nextOpenid = '')
    {
        $this->urlParam['tagid'] = $tagid;
        $this->urlParam['next_openid'] = $nextOpenid;

        $data = $this->doCurl(__FUNCTION__);

        return $data;
    }
}