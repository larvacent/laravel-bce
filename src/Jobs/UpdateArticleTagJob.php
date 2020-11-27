<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Baidu\Cloud\Jobs;

use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Larva\Baidu\Cloud\Bce;
use Larva\Baidu\Cloud\Jobs\Middleware\BceNLPRateLimited;

/**
 * 抽取文章标签
 * @author Tongle Xu <xutongle@gmail.com>
 */
class UpdateArticleTagJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任务可以执行的最大秒数 (超时时间)。
     *
     * @var int
     */
    public $timeout = 60;

    /**
     * @var Article
     */
    public $article;

    /**
     * Create a new job instance.
     *
     * @param Article $article
     */
    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    /**
     * 获取任务应该通过的中间件
     *
     * @return array
     */
    public function middleware()
    {
        return [new BceNLPRateLimited];
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $tags = Bce::get('nlp')->keywords($this->article->title, strip_tags($this->article->content));
        if (!empty($tags) && is_array($tags)) {
            $this->article->addTags($tags);
            $this->article->saveQuietly();
        } else {
            throw new \Exception('QPS quota exceeded.');
        }
    }
}