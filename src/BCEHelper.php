<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Baidu\Cloud;

/**
 * 快速操作助手
 * @author Tongle Xu <xutongle@gmail.com>
 */
class BCEHelper
{
    /**
     * 抽取关键词
     * @param string $title
     * @param null|string $content
     * @return array
     */
    public static function keywordsExtraction($title, $content = null)
    {
        return \Larva\Baidu\Cloud\Bce::get('nlp')->keywords($title, $content);
    }
}
