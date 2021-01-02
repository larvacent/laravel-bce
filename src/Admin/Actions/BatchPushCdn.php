<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Baidu\Cloud\Admin\Actions;

use Dcat\Admin\Grid\BatchAction;
use Illuminate\Http\Request;
use Larva\Baidu\Cloud\Jobs\CdnRefreshObjectCachesJob;

/**
 * 批量审核通过
 * @author Tongle Xu <xutongle@gmail.com>
 */
class BatchPushCdn extends BatchAction
{
    protected $title = '<i class="feather icon-aperture"></i> '.'CDN预热';

    protected $model;

    /**
     * BatchRestore constructor.
     * @param string|null $model
     */
    public function __construct(string $model = null)
    {
        $this->model = $model;
        parent::__construct($this->title);
    }

    public function handle(Request $request)
    {
        $model = $request->get('model');
        foreach ((array)$this->getKey() as $key) {
            $link = $model::query()->findOrFail($key)->link;
            CdnRefreshObjectCachesJob::dispatch($link);
        }
        return $this->response()->success('已通过')->refresh();
    }

    public function confirm()
    {
        return ['确定吗？'];
    }

    public function parameters()
    {
        return [
            'model' => $this->model,
        ];
    }
}
