<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * 此模板相关的文章
     */
    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
