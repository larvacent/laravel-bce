<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Baidu\Cloud;

use Closure;
use InvalidArgumentException;

/**
 * Class BceManage
 * @author Tongle Xu <xutongle@gmail.com>
 */
class BceManage
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * @var string AccessKey ID
     */
    protected $accessId;

    /**
     * @var string AccessKey
     */
    protected $accessKey;

    /**
     * The array of resolved services drivers.
     *
     * @var BceInterface[]
     */
    protected $services = [];

    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * Create a new filesystem manager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->accessId = $this->app['config']["bce.access_id"];
        $this->accessKey = $this->app['config']["bce.access_key"];
    }

    /**
     * Get the bce service configuration.
     *
     * @param  string $name
     * @return array
     */
    protected function getConfig($name)
    {
        $config = $this->app['config']["bce.services.{$name}"];
        if (empty ($config['access_id'])) {
            $config['access_id'] = $this->accessId;
        }
        if (empty ($config['access_key'])) {
            $config['access_key'] = $this->accessKey;
        }
        return $config;
    }

    /**
     * Attempt to get the disk from the local cache.
     *
     * @param  string $name
     * @return BceInterface
     */
    public function get($name)
    {
        return $this->services[$name] ?? $this->resolve($name);
    }

    /**
     * Set the given service instance.
     *
     * @param  string $name
     * @param  mixed $service
     * @return $this
     */
    public function set($name, $service)
    {
        $this->services[$name] = $service;
        return $this;
    }

    /**
     * Resolve the given disk.
     *
     * @param  string $name
     * @return BceInterface
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (isset($this->customCreators[$config['driver']])) {
            return $this->callCustomCreator($config);
        }

        $driverMethod = 'create' . ucfirst($config['driver']) . 'Service';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        } else {
            throw new InvalidArgumentException("Driver [{$config['driver']}] is not supported.");
        }
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param  string $driver
     * @param  \Closure $callback
     * @return $this
     */
    public function extend($driver, Closure $callback)
    {
        $this->customCreators[$driver] = $callback;
        return $this;
    }

    /**
     * Call a custom driver creator.
     *
     * @param  array $config
     * @return BceInterface
     */
    protected function callCustomCreator(array $config)
    {
        return $this->customCreators[$config['driver']]($this->app, $config);
    }

    /**
     * 创建CDN服务
     * @param array $config
     * @return BceInterface
     */
    public function createCdnService(array $config)
    {
        return new Services\Cdn(['accessId' => $config['access_id'], 'accessKey' => $config['access_key']]);
    }
}