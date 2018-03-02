<?php

namespace Magein\Wechat;

/**
 * 用户标签类
 * Class Tags
 * @package Magein\Wechat
 */
class Tags
{
    /**
     * @var string
     */
    private $module = 'tags';

    /**
     * @var array
     */
    private $urlMapping = [
        'create' => 'create',    // 创建标签
        'get' => 'get',    // 获取标签
        'update' => 'update', // 更新标签
        'delete' => 'delete',  // 删除标签
        'batchBindTag' => 'members/batchtagging',    // 批量绑定
        'batchRemoveTag' => 'members/batchuntagging',    // 批量解除
        'getUserTags' => 'getidlist', //获取用户绑定的标签
        'getBlackList' => 'members/getblacklist', // 获取拉黑用户
        'batchBlack' => 'members/batchblacklist', //批量拉黑
        'batchRemoveBlack' => 'members/batchunblacklist', // 批量取消拉黑
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
     * 微信接口暂不支持批量创建
     * 这里建议一个一个创建，如果不考虑创建失败带来的影响，可以使用逻辑批量创建
     * @param string $name
     * @return mixed|null
     */
    public function create(string $name)
    {
        $postData['tag'] = ['name' => $name];

        $data = $this->doCurl(__FUNCTION__, $postData);

        return $data;
    }

    /**
     * @return mixed|null
     */
    public function get()
    {
        $data = $this->doCurl(__FUNCTION__);

        return $data;
    }

    /**
     * @param int $id
     * @param string $name
     * @return mixed|null
     */
    public function update(int $id, string $name)
    {
        $postData['tag'] = [
            'id' => $id,
            'name' => $name
        ];

        $data = $this->doCurl(__FUNCTION__, $postData);

        return $data;
    }

    /**
     * @param int $id
     * @return mixed|null
     */
    public function delete(int $id)
    {
        $postData['tag'] = [
            'id' => $id
        ];

        $data = $this->doCurl(__FUNCTION__, $postData);

        return $data;
    }

    /**
     * @param int $tagid
     * @param array $openids
     * @return mixed|null
     */
    public function batchBindTag(int $tagid, array $openids = [])
    {
        $postData['openid_list'] = $openids;
        $postData['tagid'] = $tagid;

        $data = $this->doCurl(__FUNCTION__, $postData);

        return $data;
    }

    /**
     * @param int $tagid
     * @param array $openids
     * @return mixed|null
     */
    public function batchRemoveTag(int $tagid, array $openids = [])
    {
        $postData['openid_list'] = $openids;
        $postData['tagid'] = $tagid;

        $data = $this->doCurl(__FUNCTION__, $postData);

        return $data;
    }

    /**
     * @param string $openid
     * @return mixed|null
     */
    public function getUserTags(string $openid)
    {
        $postData['openid'] = $openid;

        $data = $this->doCurl(__FUNCTION__, $postData);

        return $data;
    }

    /**
     * @param string $beginOpenid
     * @return mixed|null
     */
    public function getBlackList(string $beginOpenid)
    {
        $postData['begin_openid'] = $beginOpenid;

        $data = $this->doCurl(__FUNCTION__, $postData);

        return $data;
    }

    /**
     * @param array $openids
     * @return mixed|null
     */
    public function batchBlack(array $openids)
    {
        $postData['openid_list'] = $openids;

        $data = $this->doCurl(__FUNCTION__, $postData);

        return $data;
    }

    /**
     * @param array $openids
     * @return mixed|null
     */
    public function batchRemoveBlack(array $openids)
    {
        $postData['openid_list'] = $openids;

        $data = $this->doCurl(__FUNCTION__, $postData);

        return $data;
    }
}