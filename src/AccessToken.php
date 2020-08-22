<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Baidu\Cloud;

use Illuminate\Support\Facades\Cache;
use Larva\Supports\Traits\HasHttpRequest;

/**
 * Class AccessToken
 * @author Tongle Xu <xutongle@gmail.com>
 */
class AccessToken
{
    use HasHttpRequest;

    private $app_id;
    private $app_key;
    private $secret_key;

    /**
     * @var string
     */
    protected $endpointToGetToken = 'https://openapi.baidu.com/oauth/2.0/token';

    /**
     * Constructor.
     * @param string $appId
     * @param string $appKey
     * @param string $secretKey
     */
    public function __construct($appId, $appKey, $secretKey)
    {
        $this->app_id = $appId;
        $this->app_key = $appKey;
        $this->secret_key = $secretKey;
    }

    /**
     * 获取凭证
     * @return array
     */
    protected function getCredentials(): array
    {
        return [
            'grant_type' => 'client_credentials',
            'client_id' => $this->app_key,
            'client_secret' => $this->secret_key,
        ];
    }

    /**
     * 获取缓存Key
     * @return string
     */
    protected function getCacheKey(): string
    {
        return 'baidu_aip_' . $this->app_id;
    }

    /**
     * @return array
     */
    public function getRefreshedToken(): array
    {
        return $this->getToken(true);
    }

    /**
     * @param bool $refresh
     * @return array
     */
    public function getToken(bool $refresh = false): array
    {
        $cacheKey = $this->getCacheKey();
        if (!$refresh && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        $token = $this->requestToken($this->getCredentials());
        $this->setToken($token['access_token'], $token['expires_in'] ?? 7200);
        return $token;
    }

    /**
     * @param string $token
     * @param int $lifetime
     * @return AccessToken
     */
    public function setToken(string $token, int $lifetime = 7200)
    {
        Cache::put($this->getCacheKey(), ['access_token' => $token, 'expires_in' => $lifetime,], now()->addSeconds($lifetime));
        if (!Cache::has($this->getCacheKey())) {
            throw new \RuntimeException('Failed to cache access token.');
        }
        return $this;
    }

    /**
     * 刷新 Token
     * @return $this
     */
    public function refresh()
    {
        $this->getToken(true);
        return $this;
    }

    /**
     * 请求 AccessToken
     * @param array $credentials
     * @return array
     */
    public function requestToken(array $credentials)
    {
        return $this->post($this->endpointToGetToken, $credentials);
    }
}