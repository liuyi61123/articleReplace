<?php

namespace App\Models\Article;

use Illuminate\Database\Eloquent\Model;

class Param extends Model
{
    protected $table = 'article_params';

    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = [];
}
