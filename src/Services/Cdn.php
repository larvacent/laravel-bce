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
     * 查询用户名下所有域名
     * @param string $rule
     * @param string $status
     * @return array
     */
    public function userDomains($rule, $status = 'ALL')
    {
        return $this->get('/v2/user/domains', [
            'status' => $status,
            'rule' => $rule,
        ]);
    }

    /**
     * 查询用户的域名列表
     * @return array
     */
    public function domains()
    {
        return $this->get('/v2/domain');
    }

    /**
     * 查询域名是否可以被添加
     * @param string $domain
     * @return array
     */
    public function domainValid($domain)
    {
        return $this->get("/v2/domain/{$domain}/valid");
    }

    /**
     * 创建一个加速域名
     * @param string $domain
     * @param $origin
     * @param $defaultHost
     * @param $form
     * @return array
     */
    public function domainCreate($domain, $origin, $defaultHost, $form)
    {
        return $this->put("/v2/domain/{$domain}", [
            'origin' => $origin,
            'defaultHost' => $defaultHost,
            'form' => $form
        ]);
    }

    /**
     * 启用域名加速
     * @param string $domain
     * @return array
     */
    public function domainEnable($domain)
    {
        return $this->post("/v2/domain/{$domain}", [
            'enable' => ''
        ]);
    }

    /**
     * 禁用域名加速
     * @param string $domain
     * @return array
     */
    public function domainDisable($domain)
    {
        return $this->post("/v2/domain/{$domain}", [
            'disable' => ''
        ]);
    }

    /**
     * 删除域名加速
     * @param string $domain
     * @return array|mixed
     */
    public function domainDelete($domain)
    {
        return $this->delete("/v2/domain/{$domain}");
    }

    /**
     * 查询域名是否备案
     * @param string $domain
     * @return array|mixed
     */
    public function domainICP($domain)
    {
        return $this->get("/v2/domain/{$domain}/icp");
    }

    /**
     * 获取指定加速域名配置的基本信息
     * @param string $domain
     * @return array
     */
    public function getDomainConfig($domain)
    {
        return $this->get("/v2/domain/{$domain}/config");
    }

    /**
     * 修改加速域名文件类型的缓存策略
     * @param string $domain
     * @param array $cacheTTL
     * @return array
     */
    public function cacheTTL($domain,$cacheTTL)
    {
        return $this->putJSON("/v2/domain/{$domain}/config", [
            'cacheTTL' => $cacheTTL
        ]);
    }

    /**
     * 提交purge任务
     * @param array $tasks
     * @return array
     */
    public function cachePurge(array $tasks)
    {
        return $this->post("/v2/cache/purge", [
            'tasks' => $tasks
        ]);
    }

    /**
     * 提交prefetch任务
     * @param array $tasks
     * @return array
     */
    public function cachePrefetch($tasks)
    {
        return $this->post("/v2/cache/prefetch", [
            'tasks' => $tasks
        ]);
    }

}