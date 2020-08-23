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
 * Class AipStack
 * @author Tongle Xu <xutongle@gmail.com>
 */
class AipStack
{
    /** @var array Configuration settings */
    private $config = [
        'appid' => '',
        'apiKey' => '',
        'secretKey' => ''
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
        $params = \GuzzleHttp\Psr7\parse_query($request->getUri()->getQuery());
        $params['access_token'] =$this->getAccessToken();
        $params['charset'] = 'UTF-8';
        $body = http_build_query($params, '', '&');
        return \GuzzleHttp\Psr7\modify_request($request, ['query' => $body]);
    }

    /**
     * 获取授权令牌
     * @return string
     */
    private function getAccessToken()
    {
        $accessToken = new \Larva\Baidu\Cloud\AccessToken($this->config['appid'], $this->config['apiKey'], $this->config['secretKey']);
        $token = $accessToken->getToken();
        return $token['access_token'];
    }
}