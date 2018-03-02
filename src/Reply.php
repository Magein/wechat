<?php

namespace Magein\Wechat;

/**
 * 消息回复类
 * Class Reply
 * @package Magein\Wechat
 */
class Reply
{

    /**
     * @var string
     */
    private $module = 'message';

    /**
     * @var array
     */
    private $urlMapping = [
        'send' => 'custom/send',    // 创建标签
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
     * @param array $postData
     * @param bool $service
     * @return mixed|null|string
     */
    public function send(array $postData = [], bool $service = false)
    {
        if ($service) {

            $data = $this->doCurl(__FUNCTION__, $postData);

            return $data;

        } else {

            return $this->toXml($postData);
        }
    }

    /**
     * @param array $data
     * @return string
     */
    private function toXml(array $data): string
    {
        $xmlString = '';

        if ($data) {
            foreach ($data as $name => $value) {
                if (is_string($value)) {
                    $value = '<![CDATA[' . $value . ']]>';
                } elseif (is_array($value)) {
                    foreach ($value as $key => $item) {
                        if (is_string($item)) {
                            $value = '<' . $key . '>' . '<![CDATA[' . $item . ']]>' . '<' . $key . '>';
                        }
                    }
                }
                $xmlString .= '<' . $name . '>' . $value . '</' . $name . '>';
            }
        }

        echo '<xml>' . $xmlString . '</xml>';

        return true;
    }
}