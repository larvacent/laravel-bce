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

    /**
     * 文章标签接口
     * @param string $title
     * @param null|string $content
     * @return array
     */
    public function keyword($title, $content = null)
    {
        if (!$content) $content = $title;
        $content = strip_tags($content);
        return $this->postJSON('/rpc/2.0/nlp/v1/keyword', [
            'title' => $title,
            'content' => $content
        ]);
    }

    /**
     * 文章分类接口
     * @param string $title
     * @param null|string $content
     * @return array
     */
    public function topic($title, $content = null)
    {
        if (!$content) $content = $title;
        return $this->postJSON('/rpc/2.0/nlp/v1/topic', [
            'title' => $title,
            'content' => $content
        ]);
    }

    /**
     * 文本纠错
     * @param string $text
     * @return array
     */
    public function ecnet($text)
    {
        return $this->postJSON('/rpc/2.0/nlp/v1/ecnet', [
            'text' => $text
        ]);
    }

    /**
     * 新闻摘要接口
     * @param string $content
     * @param string|null $title
     * @param int $maxSummaryLen 此数值将作为摘要结果的最大长度。例如：原文长度1000字，本参数设置为150，则摘要结果的最大长度是150字；推荐最优区间：200-500字
     * @return array
     */
    public function newsSummary($content, $maxSummaryLen = 200, $title = null)
    {
        $params = ['content' => $content, 'max_summary_len' => $maxSummaryLen];
        if ($title) $query['title'] = $title;
        return $this->postJSON('/rpc/2.0/nlp/v1/topic', $params);
    }

    /**
     * 文本审核
     * @param string $text
     * @return array
     */
    public function textCensor($text)
    {
        return $this->post('/rest/2.0/solution/v1/text_censor/v2/user_defined', ['text' => $text]);
    }

    /**
     * 图片审核
     * @param array $params
     * @return array
     */
    public function imgCensor($params)
    {
        if (!$params['imgType']) $params['imgType'] = 0;
        return $this->post('/rest/2.0/solution/v1/img_censor/v2/user_defined', $params);
    }

    /**
     * 语音审核
     * @param array $params
     * @return array
     */
    public function voiceCensor($params)
    {
        if (!$params['rawText']) $params['rawText'] = true;
        if (!$params['split']) $params['split'] = false;
        return $this->post('/rest/2.0/solution/v1/voice_censor/v2/user_defined', $params);
    }

    /**
     * 短视频审核
     * @param array $params
     * @return array
     */
    public function videoCensor($params)
    {
        return $this->post('/rest/2.0/solution/v1/video_censor/v2/user_defined', $params);
    }

    /**
     * 依存句法分析
     * @param string $text 待分析文本，长度不超过256字节
     * @param int $mode 模型选择。默认值为0，可选值mode=0（对应web模型）；mode=1（对应query模型）
     * @return array
     */
    public function depparser($text, $mode = 1)
    {
        return $this->postJSON('/rpc/2.0/nlp/v1/depparser', [
            'text' => $text,
            'mode' => $mode
        ]);
    }

    /**
     * 词法分析
     * @param string $text
     * @return array
     */
    public function lexer($text)
    {
        return $this->postJSON('/rpc/2.0/nlp/v1/lexer', [
            'text' => $text,
        ]);
    }

    /**
     * 词法分析定制版
     * @param string $text
     * @return array
     */
    public function lexerCustom($text)
    {
        return $this->postJSON('/rpc/2.0/nlp/v1/lexer_custom', [
            'text' => $text,
        ]);
    }

    /**
     * 通过标题和内容获取标签
     * @param string $title
     * @param string $content
     * @return array
     */
    public function tags($title, $content)
    {
        $words = $this->keyword($title, $content);
        $tags = [];
        if (isset($words['items']) && is_array($words['items'])) {
            foreach ($words['items'] as $tag) {
                if ($tag['score'] >= 0.7) {
                    $tags[] = $tag['tag'];
                }
            }
        }
        return $tags;
    }

    /**
     * 分词
     * @param string $text
     * @return array
     */
    public function wordSegmentation($text)
    {
        $words = $this->lexer($text);
        $keywords = [];
        if (isset($words['items']) && is_array($words['items'])) {
            foreach ($words['items'] as $item) {
                if (!empty($item['pos']) && in_array($item['pos'], ['v', 'vd', 'p', 'w', 'a', 'c', 'u', 'f', 'r'])) {
                    continue;
                }
                $keywords[] = $item['item'];
            }
        }
        return $keywords;
    }
}