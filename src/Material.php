<?php

namespace Magein\Wechat;

/**
 * 永久素材类
 * Class Material
 * @package Magein\Wechat
 */
class Material
{
    /**
     * @var string
     */
    private $module = 'material';

    /**
     *  图片永久素材
     */
    const MATERIAL_IMAGE = 'image';

    /**
     * 语音永久素材
     */
    const MATERIAL_VOICE = 'voice';

    /**
     * 视频永久素材
     */
    const MATERIAL_VIDEO = 'video';

    /**
     * 略缩图永久素材
     */
    const MATERIAL_THUMB = 'thumb';

    /**
     * @var array
     */
    private $urlMapping = [
        'upload' => 'add_material',    // 上传永久素材
        'get' => 'get_material',    // 获取永久素材
        'getCount' => 'get_materialcount', // 获取永久素材统计
        'getList' => 'batchget_material',  // 或许永久素材列表
        'news' => 'add_news',   // 新增图文永久素材
        'delete' => 'del_material',    // 删除永久素材
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
     * 直接返回请求的结果，获取图片永久素材的时候输出的是图片资源,所以不要将结果直接使用json_decode操作
     * @param string $url
     * @param array $postData
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
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
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
     * @return array
     */
    public function news(array $postData)
    {
        if (isset($postData['title'])) {
            $postData = [$postData];
        }

        $postData['articles'] = $postData;

        $data = $this->doCurl(__FUNCTION__, $postData);

        return $data;
    }

    public function uploadNewImage(string $path)
    {
        $file = new \CURLFile($path);
        $file->setMimeType(mime_content_type($path));
        $postData['media'] = $file;

        $data = $this->doCurl(__FUNCTION__, $postData);

        return $data;
    }

    /**
     * @param string $path
     * @param string $type
     * @return array
     */
    public function upload(string $path, $type = self::MATERIAL_IMAGE)
    {
        $file = new \CURLFile($path);
        $file->setMimeType(mime_content_type($path));

        $postData['media'] = $file;

        $this->urlParam['type'] = $type;

        $data = $this->doCurl(__FUNCTION__, $postData);

        return $data;
    }

    /**
     * @param string $mediaId
     * @return array
     */
    public function get(string $mediaId)
    {
        $postData['media_id'] = $mediaId;

        $data = $this->doCurl(__FUNCTION__, $postData);

        return $data;
    }

    /**
     * @param string $type
     * @param int $offset
     * @param int $count
     * @return array
     */
    public function getList(string $type = self::MATERIAL_IMAGE, int $offset = 0, int $count = 20)
    {
        $postData['type'] = $type;
        $postData['offset'] = $offset;
        $postData['count'] = $count;

        $data = $this->doCurl(__FUNCTION__, $postData);

        return $data;
    }

    /**
     * @return array
     */
    public function getCount()
    {
        $data = $this->doCurl(__FUNCTION__);

        return $data;
    }

    /**
     * @param string $mediaId
     * @return array
     */
    public function delete(string $mediaId)
    {
        $postData['media_id'] = $mediaId;

        $data = $this->doCurl(__FUNCTION__, $postData);

        return $data;
    }
}