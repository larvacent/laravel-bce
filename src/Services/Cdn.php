<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Baidu\Cloud\Services;

use GuzzleHttp\HandlerStack;
use Larva\Baidu\Cloud\BaseClient;
use Larva\Baidu\Cloud\HttpStack;

/**
 * CDN
 * @author Tongle Xu <xutongle@gmail.com>
 */
class Cdn extends BaseClient
{
    /**
     * @return string
     */
    public function getBaseUri()
    {
        return 'https://cdn.baidubce.com';
    }

    /**
     * @return HandlerStack
     */
    public function getHttpStack()
    {
        $stack = HandlerStack::create();
        $middleware = new HttpStack([
            'accessId' => $this->accessId,
            'accessKey' => $this->accessKey,
        ]);
        $stack->push($middleware);
        return $stack;
    }

    /**
     * 获取CDN域名
     * @return array
     */
    public function domains()
    {
        return $this->get('/v2/domain');
    }
}