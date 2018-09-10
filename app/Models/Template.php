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
     * 应该被转换成原生类型的属性。
     *
     * @var array
     */
    protected $casts = [
        'images' => 'array',
        'paragraphs' => 'array',
    ];

    /**
     * 此模板相关的文章
     */
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

}
