<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Website extends Model
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
        'config' => 'array',
        'urls' => 'array',
    ];

    /**
     * 关联的分类
     */
    public function category()
    {
        return $this->belongsTo(WebsiteCategory::class,'category_id');
    }
}
