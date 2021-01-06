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

    /**
     * 词法分析
     * @param string $text
     * @return array
     */
    public static function keywordsExtraction2($text)
    {
        $words = \Larva\Baidu\Cloud\Bce::get('nlp')->lexicalAnalysis($text);
        $keywords = [];
        if (isset($words['items']) && is_array($words['items'])) {
            foreach ($words['items'] as $item) {
                if (!empty($item['pos']) && in_array($item['pos'], ['v', 'vd', 'p', 'w', 'a', 'c', 'u', 'f', 'r']) && mb_strlen($item['item'] > 1)) {
                    continue;
                }
                $keywords[] = $item['item'];
            }
        }
        return $keywords;
    }
}
