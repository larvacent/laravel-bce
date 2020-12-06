<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Baidu\Cloud\Services;

use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\URL;
use Larva\Baidu\Cloud\NlpStack;
use Larva\Baidu\Cloud\BaseClient;

/**
 * 自然语言处理
 * @author Tongle Xu <xutongle@gmail.com>
 */
class Nlp extends BaseClient
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
        $middleware = new NlpStack([
            'appid' => $this->accessId,
            'apiKey' => $this->accessKey,
            'secretKey' => $this->secretKey
        ]);
        $stack->push($middleware);
        return $stack;
    }

    /**
     * 词法分析
     * @param string $text
     * @return array
     */
    public function lexicalAnalysis($text)
    {
        return $this->postJSON('/rpc/2.0/nlp/v1/lexer?charset=UTF-8', [
            'text' => $text,
        ]);
    }

    /**
     * 词法分析定制版
     * @param string $text
     * @return array
     */
    public function lexicalAnalysisCustom($text)
    {
        return $this->postJSON('/rpc/2.0/nlp/v1/lexer_custom?charset=UTF-8', [
            'text' => $text,
        ]);
    }

    /**
     * 词向量表示
     * @param string $word
     * @return array
     */
    public function wordEmbedding($word)
    {
        return $this->postJSON('/rpc/2.0/nlp/v2/word_emb_vec?charset=UTF-8', [
            'word' => $word,
        ]);
    }

    /**
     * 词义相似度
     * @param string $word_1
     * @param string $word_2
     * @return array
     */
    public function wordSimilarity($word_1, $word_2)
    {
        return $this->postJSON('/rpc/2.0/nlp/v2/word_emb_sim?charset=UTF-8', [
            'word_1' => $word_1,
            'word_2' => $word_2,
        ]);
    }

    /**
     * 依存句法分析
     * @param string $text
     * @param int $mode
     * @return array
     */
    public function dependencyParsing($text, $mode = 0)
    {
        return $this->postJSON('/rpc/2.0/nlp/v1/depparser?charset=UTF-8', [
            'text' => $text,
            'mode' => $mode
        ]);
    }

    /**
     * 短文本相似度
     * @param string $text_1
     * @param string $text_2
     * @return array
     */
    public function textSimilarity($text_1, $text_2)
    {
        return $this->postJSON('/rpc/2.0/nlp/v2/simnet?charset=UTF-8', [
            'text_1' => $text_1,
            'text_2' => $text_2
        ]);
    }

    /**
     * 自动摘要
     * @param string $content
     * @param string|null $title
     * @param int $maxSummaryLen 此数值将作为摘要结果的最大长度。例如：原文长度1000字，本参数设置为150，则摘要结果的最大长度是150字；推荐最优区间：200-500字
     * @return array
     */
    public function autoSummarization($content, $maxSummaryLen = 200, $title = null)
    {
        $params = ['content' => $content, 'max_summary_len' => $maxSummaryLen];
        if ($title) $query['title'] = $title;
        return $this->postJSON('/rpc/2.0/nlp/v1/news_summary?charset=UTF-8', $params);
    }

    /**
     * 文本分类接口
     * @param string $title
     * @param null|string $content
     * @return array
     */
    public function textClassification($title, $content = null)
    {
        if (!$content) $content = $title;
        return $this->postJSON('/rpc/2.0/nlp/v1/topic?charset=UTF-8', [
            'title' => $title,
            'content' => $content
        ]);
    }

    /**
     * 关键词提取
     * @param string $title
     * @param null|string $content
     * @return array
     */
    public function keywordsExtraction($title, $content = null)
    {
        if (!$content) $content = $title;
        $content = strip_tags($content);
        return $this->postJSON('/rpc/2.0/nlp/v1/keyword?charset=UTF-8', [
            'title' => $title,
            'content' => $content
        ]);
    }

    /**
     * 文本纠错
     * @param string $text
     * @return array
     */
    public function textCorrection($text)
    {
        return $this->postJSON('/rpc/2.0/nlp/v1/ecnet?charset=UTF-8', [
            'text' => $text
        ]);
    }

    /**
     * 情感分析
     * @param string $text
     * @return array
     */
    public function sentimentAnalysis($text)
    {
        return $this->postJSON('/rpc/2.0/nlp/v1/sentiment_classify?charset=UTF-8', [
            'text' => $text
        ]);
    }

    /**
     * 情感分析定制版
     * @param string $text
     * @return array
     */
    public function sentimentAnalysisCustom($text)
    {
        return $this->postJSON('/rpc/2.0/nlp/v1/sentiment_classify_custom?charset=UTF-8', [
            'text' => $text
        ]);
    }

    /**
     * DNN语言模型接口
     * @param string $text
     * @return array
     */
    public function DNN($text)
    {
        return $this->postJSON('/rpc/2.0/nlp/v2/dnnlm_cn?charset=UTF-8', [
            'text' => $text,
        ]);
    }

    /**
     * 评论观点
     * @param string $text
     * @param int $type 评论行业类型，默认为4（餐饮美食）
     * @return array
     */
    public function commentTag($text, $type = 4)
    {
        return $this->postJSON('/rpc/2.0/nlp/v2/comment_tag?charset=UTF-8', [
            'text' => $text,
            'type' => $type
        ]);
    }

    /**
     * 评论观点定制版
     * @param string $text
     * @param int $type
     * @return array
     */
    public function commentTagCustom($text, $type = 4)
    {
        return $this->postJSON('/rpc/2.0/nlp/v2/comment_tag_custom?charset=UTF-8', [
            'text' => $text,
            'type' => $type
        ]);
    }

    /**
     * 对话情绪识别
     * @param string $text
     * @param string $scene default（默认项-不区分场景），talk（闲聊对话-如度秘聊天等），task（任务型对话-如导航对话等），
     * customer_service（客服对话-如电信/银行客服等）
     * @return array
     */
    public function emotion($text, $scene = 'default')
    {
        return $this->postJSON('/rpc/2.0/nlp/v1/emotion?charset=UTF-8', [
            'text' => $text,
            'scene' => $scene
        ]);
    }

    /**
     * 多实体情感倾向分析
     * @param string $title
     * @param string $content
     * @param int $type 新闻类型，目前支持3种文章类型，1：娱乐；2：财经；3：体育
     * @param string $repository
     * @return array
     */
    public function entitySentimentAnalysis($title, $content, $type, $repository = '')
    {
        return $this->postJSON('/rpc/2.0/nlp/v1/entity_level_sentiment?charset=UTF-8', [
            'title' => $title,
            'content' => $content,
            'type' => $type,
            'repository' => $repository
        ]);
    }

    /**
     * 地址识别
     * @param string $text
     * @param int $confidence
     * @return array
     */
    public function address($text, $confidence = 50)
    {
        return $this->postJSON('/rpc/2.0/nlp/v1/address?charset=UTF-8', [
            'text' => $text,
            'confidence' => $confidence,
        ]);
    }

    /**
     * 消费者评论分析
     * @param string $text
     * @param int $type 不同评论类型，1-15
     * @return array
     */
    public function commentAnalysis($text, $type)
    {
        return $this->postJSON('/rpc/2.0/nlp/v1/ecomment?charset=UTF-8', [
            'text' => $text,
            'type' => $type,
        ]);
    }

    /**
     * 热点发现
     * @param string $domain
     * @return array
     */
    public function hotList($domain)
    {
        return $this->postJSON('/rpc/2.0/creation/v1/hot_list/domain?charset=UTF-8', [
            'domain' => $domain,
        ]);
    }

    /**
     * 事件脉络
     * @param string $content
     * @return array
     */
    public function veinList($content)
    {
        return $this->postJSON('/rpc/2.0/creation/v1/event/vein_list?charset=UTF-8', [
            'content' => $content,
        ]);
    }

    /**
     * 主题短语生成
     * @param string $title
     * @param string $summary
     * @return array
     */
    public function topicPhrase($title, $summary)
    {
        return $this->postJSON('/rpc/2.0/creation/v1/topic_phrase?charset=UTF-8', [
            'title' => $title,
            'summary' => $summary
        ]);
    }

    /**
     * 资讯地域识别
     * @param string $title
     * @param string $content
     * @return array
     */
    public function newsLocationExtract($title, $content)
    {
        return $this->postJSON('/rpc/2.0/creation/v1/location_extract?charset=UTF-8', [
            'title' => $title,
            'content' => $content
        ]);
    }

    /**
     * 智能对联
     * @param string $text
     * @param int $index
     * @return array
     */
    public function couplets($text, $index = 0)
    {
        return $this->postJSON('/rpc/2.0/creation/v1/couplets?charset=UTF-8', [
            'text' => $text,
            'index' => $index
        ]);
    }

    /**
     * 智能写诗
     * @param string $text
     * @param int $index
     * @return array
     */
    public function poem($text, $index = 0)
    {
        return $this->postJSON('/rpc/2.0/creation/v1/poem?charset=UTF-8', [
            'text' => $text,
            'index' => $index
        ]);
    }

    /**
     * 图文生成视频
     * @param string $newsUrl 新闻链接：https://baijiahao.baidu.com/ 作为域名
     * @param int $ttsPer 发音人，支持范围如下。0:成熟女声;1:温润男声、3:磁性男声、4:乖巧女童、100:甜美女生、103:可爱女生、106:成熟男声
     * @param int $duration 目标视频时长，单位：秒，要求40 - 120秒
     * @param array $extraConfig 自定义参数
     * @return array
     */
    public function createVidPress($newsUrl, $ttsPer = 100, $duration = 60, $extraConfig = [])
    {
        return $this->postJSON('/rest/2.0/nlp/v1/create_vidpress?charset=UTF-8', [
            'news_url' => $newsUrl,
            'tts_per' => $ttsPer,
            'duration' => $duration,
            'extra_config' => $extraConfig
        ]);
    }

    /**
     * 关键词提取
     * @param string $title 标题
     * @param string|null $content 内容可为空
     * @return array
     */
    public function keywords($title, $content = null)
    {
        $words = $this->keywordsExtraction($title, $content);
        $keywords = [];
        if (isset($words['items']) && is_array($words['items'])) {
            foreach ($words['items'] as $tag) {
                if ($tag['score'] >= 0.7 && mb_strlen($tag['tag'] > 1)) {
                    $keywords[] = $tag['tag'];
                }
            }
        }
        return $keywords;
    }

    //内容审核

    /**
     * 文本审核
     * @param string $text
     * @return array
     */
    public function textCensor($text)
    {
        return $this->post('/rest/2.0/solution/v1/text_censor/v2/user_defined?charset=UTF-8', [
            'text' => $text,
        ]);
    }

    /**
     * 图片审核
     * @param string $image 待审核图像Base64编码字符串或者图像的Url地址
     * @param int $imgType 图片类型0:静态图片（PNG、JPG、JPEG、BMP、GIF（仅对首帧进行审核）、Webp、TIFF），1:GIF动态图片
     * @return array
     */
    public function imageCensor($image, $imgType = 0)
    {
        if (!URL::isValidUrl($image)) {
            $data = ['image' => $image, 'imgType' => $imgType];
        } else {
            $data = ['imgUrl' => $image, 'imgType' => $imgType];
        }
        return $this->post('/rest/2.0/solution/v1/img_censor/v2/user_defined?charset=UTF-8', $data);
    }

    /**
     * 语音审核
     * @param string $voice
     * @param string $fmt 语音文件格式
     * @param boolean $rawText 是否返回语音识别结果 true:是;false:否
     * @param boolean $split true:拆句;false:不拆句返回整段文本
     * @return array
     */
    public function voiceCensor($voice, $fmt, $rawText = true, $split = false)
    {
        if (!URL::isValidUrl($voice)) {
            $data = ['base64' => $voice, 'fmt' => $fmt, 'rawText' => $rawText, 'split' => $split];
        } else {
            $data = ['url' => $voice, 'fmt' => $fmt, 'rawText' => $rawText, 'split' => $split];
        }
        return $this->post('/rest/2.0/solution/v1/voice_censor/v2/user_defined?charset=UTF-8', $data);
    }

    /**
     * 视频审核
     * @param string $name 视频名称
     * @param string $videoUrl 视频Url
     * @param mixed $extId 本地视频ID
     * @return array
     */
    public function videoCensor($name, $videoUrl, $extId)
    {
        return $this->post('/rest/2.0/solution/v1/video_censor/v2/user_defined?charset=UTF-8', [
            'name' => $name, 'videoUrl' => $videoUrl, 'extId' => $extId
        ]);
    }
}
