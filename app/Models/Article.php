<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * 此文章相关的参数
     */
    public function params()
    {
        return $this->hasMany(ArticleParam::class);
    }
}
