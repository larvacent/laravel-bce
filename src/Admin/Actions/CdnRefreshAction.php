<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Baidu\Cloud\Admin\Actions;

use Dcat\Admin\Actions\Action;
use Dcat\Admin\Traits\HasPermissions;
use Dcat\Admin\Widgets\Modal;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Larva\Baidu\Cloud\Admin\Forms\CdnRefreshForm;

/**
 * Class CdnPush
 * @author Tongle Xu <xutongle@gmail.com>
 */
class CdnRefreshAction extends Action
{
    /**
     * @return string
     */
    protected $title = 'CDN刷新';

    /**
     * @return string|void
     */
    public function render()
    {
        $modal = Modal::make()
            ->title($this->title())
            ->body(CdnRefreshForm::make())
            ->lg()
            ->button(
                <<<HTML
<button class="btn btn-primary grid-refresh btn-mini btn-outline">{$this->title}</button>
HTML
            );

        return $modal->render();
    }

    /**
     * @param Model|Authenticatable|HasPermissions|null $user
     *
     * @return bool
     */
    protected function authorize($user): bool
    {
        return true;
    }
}
