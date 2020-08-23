<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Baidu\Cloud\Services;

use GuzzleHttp\HandlerStack;
use Illuminate\Support\Str;
use Larva\Baidu\Cloud\BaseClient;
use Larva\Baidu\Cloud\HttpStack;

/**
 * Class Sms
 * @author Tongle Xu <xutongle@gmail.com>
 */
class Sms extends BaseClient
{
    /**
     * @return string
     */
    public function getBaseUri()
    {
        return 'https://smsv3.bj.baidubce.com';
    }

    /**
     * @return HandlerStack
     */
    public function getHttpStack()
    {
        $stack = HandlerStack::create();
        $middleware = new HttpStack([
            'accessId' => $this->accessId,
            'accessKey' => $this->accessKey,
        ]);
        $stack->push($middleware);
        return $stack;
    }

    /**
     * 短信发送
     * @param string $mobile
     * @param string $template
     * @param string $signatureId
     * @param array $contentVar
     * @param string|null $custom
     * @param string|null $userExtId
     * @return array
     */
    public function send($mobile, $template, $signatureId, array $contentVar, $custom = null, $userExtId = null)
    {
        $params = ['mobile' => $mobile, 'template' => $template, 'contentVar' => $contentVar, 'signatureId' => $signatureId];
        if ($custom) $params['custom'] = $custom;
        if ($userExtId) $params['userExtId'] = $userExtId;
        return $this->postJSON('/api/v3/sendSms?clientToken=' . Str::random(), $params);
    }

    /**
     * 申请签名
     * @param string $content 签名内容
     * @param string $contentType 签名类型
     * @param string|null $description 对于签名的描述
     * @param string|null $countryType 签名适用的国家类型
     * @param string|null $signatureFileBase64 签名的证明文件经过base64编码后的字符串
     * @param string|null $signatureFileFormat 签名证明文件的格式
     * @return array
     */
    public function signatureApply($content, $contentType = 'DOMESTIC', $description = null, $countryType = null, $signatureFileBase64 = null, $signatureFileFormat = null)
    {
        $params = ['content' => $content, 'contentType' => $contentType];
        if ($description) $params['description'] = $description;
        if ($countryType) $params['countryType'] = $countryType;
        if ($signatureFileBase64) $params['signatureFileBase64'] = $signatureFileBase64;
        if ($signatureFileFormat) $params['signatureFileFormat'] = $signatureFileFormat;
        return $this->postJSON('/sms/v3/signatureApply?clientToken=' . Str::random(), $params);
    }

    /**
     * 变更签名
     * @param string $signatureId 签名ID
     * @param string $content 签名内容
     * @param string $contentType 签名类型
     * @param string|null $description 对于签名的描述
     * @param string|null $signatureFileBase64 签名的证明文件经过base64编码后的字符串
     * @param string|null $signatureFileFormat 签名证明文件的格式
     * @return array
     */
    public function changeSignatureApply($signatureId, $content, $contentType = 'DOMESTIC', $description = null, $signatureFileBase64 = null, $signatureFileFormat = null)
    {
        $params = ['content' => $content, 'contentType' => $contentType];
        if ($description) $params['description'] = $description;
        if ($signatureFileBase64) $params['signatureFileBase64'] = $signatureFileBase64;
        if ($signatureFileFormat) $params['signatureFileFormat'] = $signatureFileFormat;
        return $this->putJSON("/sms/v3/signatureApply/{$signatureId}", $params);
    }

    /**
     * 获取签名
     * @param string $signatureId
     * @return array
     */
    public function getSignatureApply($signatureId)
    {
        return $this->getJSON("/sms/v3/signatureApply/{$signatureId}");
    }

    /**
     * 删除签名
     * @param string $signatureId
     * @return array
     */
    public function deleteSignatureApply($signatureId)
    {
        return $this->delete("/sms/v3/signatureApply/{$signatureId}");
    }

    /**
     * 创建模板
     * @param string $name
     * @param string $content
     * @param string $smsType
     * @param string $countryType
     * @param string $description
     * @return array
     */
    public function template($name, $content, $smsType, $countryType, $description)
    {
        $params = ['name' => $name, 'content' => $content, 'smsType' => $smsType, 'countryType' => $countryType, 'description' => $description];
        return $this->postJSON('/sms/v3/template?clientToken=' . Str::random(), $params);
    }

    /**
     * 修改模板
     * @param string $templateId
     * @param string $name
     * @param string $content
     * @param string $smsType
     * @param string $countryType
     * @param string|null $description
     * @return array
     */
    public function changeTemplate($templateId, $name, $content, $smsType, $countryType, $description = null)
    {
        $params = ['name' => $name, 'content' => $content, 'smsType' => $smsType, 'countryType' => $countryType];
        if ($description) $params['description'] = $description;
        return $this->putJSON("/sms/v3/template/{$templateId}", $params);
    }

    /**
     * 获取模板
     * @param string $templateId
     * @return array
     */
    public function getTemplate($templateId)
    {
        return $this->getJSON("/sms/v3/template/{$templateId}");
    }

    /**
     * 删除模板
     * @param string $templateId
     * @return array
     */
    public function deleteTemplate($templateId)
    {
        return $this->delete("/sms/v3/template/{$templateId}");
    }

    /**
     * 查配额
     * @return array
     */
    public function quota()
    {
        return $this->getJSON("/sms/v3/quota?userQuery=1");
    }

    /**
     * 变更配额
     * @param array $params
     * @return array
     */
    public function changeQuota($params)
    {
        return $this->putJSON("/sms/v3/quota", $params);
    }
}