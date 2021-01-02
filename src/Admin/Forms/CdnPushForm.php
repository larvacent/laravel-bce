<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Baidu\Cloud\Admin\Forms;

use Dcat\Admin\Widgets\Form;
use Larva\Baidu\Cloud\Jobs\CdnPushObjectCacheJob;

/**
 * CDN资源预热
 * @author Tongle Xu <xutongle@gmail.com>
 */
class CdnPushForm extends Form
{
    /**
     * 处理表单请求.
     *
     * @param array $input
     *
     * @return \Dcat\Admin\Http\JsonResponse
     */
    public function handle(array $input)
    {
        $urls = explode(PHP_EOL, trim($input['urls']));
        CdnPushObjectCacheJob::dispatch($urls);
        return $this->response()->success('任务委派成功!')->refresh();
    }

    /**
     * 构建表单.
     */
    public function form()
    {
        $this->textarea('urls')->required()->help('页面地址');
    }

    /**
     * 返回表单数据.
     *
     * @return array
     */
    public function default()
    {
        return [
            'urls' => '',
        ];
    }
}
