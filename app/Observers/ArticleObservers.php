<?php

namespace App\Observers;

use App\Models\Article;
use App\Jobs\GenerateArticle;

class ArticleObserver
{
    /**
     * 监听文章保存后的事件
     *
     * @param  \App\Models\Article  $article
     * @return void
     */
    public function saved(Article $article)
    {
        // GenerateArticle::dispatch($article);
    }
}
