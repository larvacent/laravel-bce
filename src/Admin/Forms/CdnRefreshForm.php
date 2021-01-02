<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Baidu\Cloud\Admin\Forms;

use Dcat\Admin\Widgets\Form;
use Larva\Baidu\Cloud\Jobs\CdnRefreshObjectCachesJob;

/**
 * Class CdnRefreshForm
 * @author Tongle Xu <xutongle@gmail.com>
 */
class CdnRefreshForm extends Form
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
        $type = trim($input['type']);
        CdnRefreshObjectCachesJob::dispatch($urls, $type);
        return $this->response()->success('任务委派成功!')->refresh();
    }

    /**
     * 构建表单.
     */
    public function form()
    {
        $this->select('type', '类型')->required()->options(['file' => '文件', 'directory' => '目录']);
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
            'type' => 'file'
        ];
    }
}
