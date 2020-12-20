<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Article\Article;

class GenerateArticle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $article;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    /**
     * 队列标签
     *
     * @return array
     */
    public function tags()
    {
        return ['article', 'GenerateArticle:'.$this->article->id];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->article->generate();
    }
}
