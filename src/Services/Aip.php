<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Baidu\Cloud\Services;

use GuzzleHttp\HandlerStack;
use Larva\Baidu\Cloud\AipStack;
use Larva\Baidu\Cloud\BaseClient;

/**
 * Class Aip
 * @author Tongle Xu <xutongle@gmail.com>
 */
class Aip extends BaseClient
{
    /**
     * @var string secret key
     */
    protected $secretKey;

    /**
     * @return string
     */
    public function getBaseUri()
    {
        return 'https://aip.baidubce.com';
    }

    /**
     * @return HandlerStack
     */
    public function getHttpStack()
    {
        $stack = HandlerStack::create();
        $middleware = new AipStack([
            'appid' => $this->accessId,
            'apiKey' => $this->accessKey,
            'secretKey' => $this->secretKey
        ]);
        $stack->push($middleware);
        return $stack;
    }
}