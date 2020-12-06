<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Baidu\Cloud\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Larva\Baidu\Cloud\Bce;

/**
 * 更新CDN 源站配置
 * @author Tongle Xu <xutongle@gmail.com>
 */
class CdnOriginUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * 任务可以尝试的最大次数。
     *
     * @var int
     */
    public $tries = 3;

    /**
     * 任务可以执行的最大秒数 (超时时间)。
     *
     * @var int
     */
    public $timeout = 60;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $peer;

    /**
     * Create a new job instance.
     *
     * @param string $domain
     * @param string $peer
     */
    public function __construct($domain, $peer)
    {
        $this->domain = $domain;
        $this->peer = $peer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $cacheKey = ":cdn:{$this->domain}:origin";
        if ($this->peer != Cache::get($cacheKey)) {
            Cache::forever($cacheKey, $this->peer);
            /** @var \Larva\Baidu\Cloud\Services\Cdn $cdn */
            $cdn = Bce::get('cdn');
            $origin = [
                [
                    'peer' => $this->peer,
                ]
            ];
            $cdn->setDomainOrigin($this->domain, $origin);
        }
    }
}
