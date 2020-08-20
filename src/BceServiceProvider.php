<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Baidu\Cloud;

use Illuminate\Support\ServiceProvider;

/**
 * Class BceServiceProvider
 * @author Tongle Xu <xutongle@gmail.com>
 */
class BceServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->setupConfig();

        $this->app->singleton('bce', function () {
            return new BceManage($this->app);
        });
    }

    /**
     * Setup the config.
     */
    protected function setupConfig()
    {
        $source = realpath($raw = __DIR__ . '/../config/bce.php') ?: $raw;

        if ($this->app->runningInConsole()) {
            $this->publishes([
                $source => config_path('bce.php'),
            ], 'bce-config');
        }

        $this->mergeConfigFrom($source, 'bce');
    }
}