<?php

namespace Magein\Wechat;

/**
 * 临时素材类
 * Class Media
 * @package Magein\Wechat
 */
class Media
{
    /**
     * @var string
     */
    private $module = 'media';

    /**
     *  图片素材
     */
    const MATERIAL_IMAGE = 'image';

    /**
     * 语音素材
     */
    const MATERIAL_VOICE = 'voice';

    /**
     * 视频素材
     */
    const MATERIAL_VIDEO = 'video';

    /**
     * 略缩图素材
     */
    const MATERIAL_THUMB = 'thumb';

    /**
     * @var array
     */
    private $urlMapping = [
        'upload' => 'upload',    // 上传临时素材
        'get' => 'get',    // 获取临时素材
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
     * @param string $url
     * @param array $postData
     * @param array $options
     * @return array
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
            return [];
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
        $this->urlParam['media_id'] = $mediaId;

        $data = $this->doCurl(__FUNCTION__);

        return $data;
    }
}