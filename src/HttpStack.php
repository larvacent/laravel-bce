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
        $authString = "bce-auth-v1/{$accessId}/{$timestamp}/{$expirationPeriodInSeconds}";
        $headers = ['x-bce-date' => $timestamp];
        foreach ($request->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $headers[$name] = $value;
            }
        }

        $canonicalURI = $this->getCanonicalURIPath($request->getUri()->getPath());
        $canonicalQueryString = $this->getCanonicalQueryString(\GuzzleHttp\Psr7\parse_query($request->getUri()->getQuery()));

        $canonicalHeaders = $this->getCanonicalHeaders($headers);
        $canonicalRequest = $request->getMethod() . "\n" . $canonicalURI . "\n" . $canonicalQueryString . "\n" . $canonicalHeaders;
        $headersToSign = array_keys($headers);
        ksort($headersToSign);
        $signedHeaders = strtolower(trim(implode(";", $headersToSign)));

        // 任务三：生成派生签名密钥(signingKey)
        $signingKey = hash_hmac('sha256', $authString, $this->config['accessKey']);
        // 任务四：生成签名摘要(signature)，并拼接最终的认证字符串(authorization)
        $signature = hash_hmac('sha256', $canonicalRequest, $signingKey);
        $headers['Authorization'] = "$authString/$signedHeaders/$signature";
        return \GuzzleHttp\Psr7\modify_request($request, ['set_headers' => $headers]);
    }

    /**
     * Normalize a string for use in url path. The algorithm is:
     * <p>
     *
     * <ol>
     *   <li>Normalize the string</li>
     *   <li>replace all "%2F" with "/"</li>
     *   <li>replace all "//" with "/%2F"</li>
     * </ol>
     *
     * <p>
     * Bos object key can contain arbitrary characters, which may result double
     * slash in the url path. Apache Http client will replace "//" in the path
     * with a single '/', which makes the object key incorrect. Thus we replace
     * "//" with "/%2F" here.
     *
     * @param $path string the path string to normalize.
     * @return string the normalized path string.
     * @see #normalize(string)
     */
    private function urlEncodeExceptSlash($path)
    {
        return str_replace("%2F", "/", rawurlencode($path));
    }

    /**
     * @param $path string
     * @return string
     */
    private function getCanonicalURIPath($path)
    {
        if (empty($path)) {
            return '/';
        } else {
            if ($path[0] == '/') {
                return $this->urlEncodeExceptSlash($path);
            } else {
                return '/' . $this->urlEncodeExceptSlash($path);
            }
        }
    }

    /**
     * @param array $params
     * @return string
     */
    private function getCanonicalQueryString($params)
    {
        ksort($params);
        return http_build_query($params, null, '&', PHP_QUERY_RFC3986);
    }

    /**
     * @param $headers array
     * @return string
     */
    private function getCanonicalHeaders($headers)
    {
        if (count($headers) == 0) {
            return '';
        }
        $headerStrings = [];
        foreach ($headers as $k => $v) {
            if ($k === null) {
                continue;
            }
            if ($v === null) {
                $v = '';
            }
            $headerStrings[] = rawurlencode(strtolower(trim($k))) . ':' . rawurlencode(trim($v));
        }
        sort($headerStrings);
        return implode("\n", $headerStrings);
    }
}
