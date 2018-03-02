<?php


namespace Magein\Wechat;


class Template
{
    /**
     * @var string
     */
    private $module = 'template';

    /**
     * @var array
     */
    private $urlMapping = [
        'setIndustry' => 'api_set_industry',    // 设置行业
        'getIndustryList' => 'get_industry',    // 获取用户列表
        'getTemplateId' => 'api_add_template', // 设置获取模板ID
        'getList' => 'get_all_private_template',  // 获取模板列表
        'delete' => 'del_private_template',  // 获取模板
        'send' => 'template/send',    // 发送模板消息
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
     * 设置行业，一月修改一次
     * @param int $industryId1
     * @param int $industryId2
     * @return mixed|null
     */
    public function setIndustry(int $industryId1, int $industryId2)
    {
        $postData['industry_id1'] = $industryId1;
        $postData['industry_id2'] = $industryId2;

        $data = $this->doCurl(__FUNCTION__, $postData);

        return $data;
    }

    /**
     * 获取行业
     * @return mixed|null
     */
    public function getIndustryList()
    {
        $data = $this->doCurl(__FUNCTION__);

        return $data;
    }

    /**
     * 获取模板id
     * @param string $templateIdShort
     * @return mixed|null
     */
    public function getTemplateId(string $templateIdShort)
    {
        $postData['template_id_short'] = $templateIdShort;

        $data = $this->doCurl(__FUNCTION__, $postData);

        return $data;
    }

    /**
     * @return mixed|null
     */
    public function getList()
    {
        $data = $this->doCurl(__FUNCTION__);

        return $data;
    }

    /**
     * @param string $templateId
     * @return mixed|null
     */
    public function delete(string $templateId)
    {
        $postData['template_id'] = $templateId;

        $data = $this->doCurl(__FUNCTION__);

        return $data;
    }

    /**
     * @param array $postData
     * @return mixed|null
     */
    public function send(array $postData)
    {
        $this->module = 'message';

        $data = $this->doCurl(__FUNCTION__, $postData);

        return $data;
    }
}