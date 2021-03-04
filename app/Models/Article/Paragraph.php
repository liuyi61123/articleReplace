<?php

namespace App\Models\Article;

use Illuminate\Database\Eloquent\Model;

class Paragraph extends Model
{
    protected $table = 'article_paragraphs';

    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = [];
}
