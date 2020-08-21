<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Baidu\Cloud;

use Psr\Http\Message\RequestInterface;

/**
 * Class HttpStack
 * @author Tongle Xu <xutongle@gmail.com>
 */
class HttpStack
{
    /** @var array Configuration settings */
    private $config = [
        'accessId' => '',
        'accessKey' => ''
    ];

    /**
     * Constructor.
     * @param array $config
     */
    public function __construct($config)
    {
        if (!empty($config)) {
            foreach ($config as $key => $value) {
                $this->config[$key] = $value;
            }
        }
    }

    /**
     * Called when the middleware is handled.
     *
     * @param callable $handler
     *
     * @return \Closure
     */
    public function __invoke(callable $handler)
    {
        return function ($request, array $options) use ($handler) {
            $request = $this->onBefore($request);
            return $handler($request, $options);
        };
    }

    /**
     * 请求前调用
     * @param RequestInterface $request
     * @return RequestInterface
     */
    private function onBefore(RequestInterface $request)
    {
        // 任务一：创建前缀字符串(authStringPrefix)
        $accessId = $this->config['accessId'];
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');
        $expirationPeriodInSeconds = 1800;
        $authString = `bce-auth-v1/{$accessId}/{$timestamp}/{$expirationPeriodInSeconds}`;

        // 任务二：创建规范请求(canonicalRequest)，确定签名头域(signedHeaders)
        $method = $request->getMethod();
        $canonicalURI = $request->getUri();
        $canonicalQueryString = $request->getUri()->getQuery();

        $canonicalHeaders = 'x-bce-date:' . $timestamp;
        $canonicalRequest = $method . "\n" . $canonicalURI . "\n" . $canonicalQueryString . "\n" . $canonicalHeaders;
        $canonicalRequest = $this->percentEncode(($canonicalRequest));
        $signedHeaders = 'x-bce-date'; // 可根据Header部分确定签名头域（signedHeaders)。签名头域是指签名算法中涉及到的HTTP头域列表。

        // 任务三：生成派生签名密钥(signingKey)
        $signingKey = hash_hmac('sha256', $authString, $this->config['accessKey']);

        // 任务四：生成签名摘要(signature)，并拼接最终的认证字符串(authorization)
        $signature = hash_hmac('sha256', $canonicalRequest, $signingKey);

        $authorization = "$authString/$signedHeaders/$signature";

        $headers = [
            'x-bce-date' => $timestamp,
            'Authorization' => $authorization
        ];
        return \GuzzleHttp\Psr7\modify_request($request, ['set_headers' => $headers]);
    }

    /**
     * @param string $str
     * @return string
     */
    protected function percentEncode($str)
    {
        return str_replace("%2F", "/", rawurlencode($str));
    }
}