<?php


namespace Magein\Wechat;

/**
 * 自定义菜单类
 * Class Menu
 * @package Magein\Wechat
 */
class Menu
{
    /**
     * @var string
     */
    private $module = 'menu';

    /**
     * @var array
     */
    private $urlMapping = [
        'create' => 'create',    // 创建菜单
        'addConditional' => 'addconditional', // 创建个性化菜单（针对不同的用户展示不同的菜单，可以设计筛选用户的条件）
        'get' => 'get',    // 获取菜单
        'delete' => 'delete', // 删除菜单
        'deleteConditional' => 'delconditional', // 删除个性化菜单
        'getUserListByTagId' => 'tag/get',  // 删除标签
        'getCurrentMenu' => '', // 获取当前使用的菜单（通过接口设置或者在微信平台设置）
        'tryMatch' => 'trymatch',// 测试匹配结果
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
     * 个性化菜单
     * @param array $button 菜单按钮配置
     * @param array $matchRule 用户匹配规则，可设置组、性别、客户端版本、国家、省市区等
     * @return mixed|null
     */
    public function addConditional(array $button, array $matchRule)
    {
        $postData['button'] = $button;
        $postData['matchrule'] = $matchRule;

        $data = $this->doCurl(__FUNCTION__, $postData);

        return $data;
    }

    /**
     * @param $button
     * @return mixed|null
     */
    public function create(array $button)
    {
        $postData['button'] = $button;

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
     * @return mixed|null
     */
    public function getCurrentMenu()
    {
        $this->module = 'get_current_selfmenu_info';

        $data = $this->doCurl(__FUNCTION__);

        return $data;
    }

    /**
     * @return mixed|null
     */
    public function delete()
    {
        $data = $this->doCurl(__FUNCTION__);

        return $data;
    }

    /**
     * @param string $menuid
     * @return mixed|null
     */
    public function deleteConditional(string $menuid)
    {
        $postData['menuid'] = $menuid;

        $data = $this->doCurl(__FUNCTION__, $postData);

        return $data;
    }

    /**
     * @param string $userId
     * @return mixed|null
     */
    public function tryMatch(string $userId)
    {
        $postData['user_id'] = $userId;

        $data = $this->doCurl(__FUNCTION__, $postData);

        return $data;
    }
}