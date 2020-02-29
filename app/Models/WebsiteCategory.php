<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteCategory extends Model
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
    ];

    /**
     * 关联的父类
     */
    public function parent()
    {
        return $this->belongsTo(WebsiteCategory::class);
    }

    /**
     * 自表关联的子级
     */
    public function children()
    {
        return $this->hasMany(WebsiteCategory::class, 'parent_id');
    }


    protected static function boot()
    {
        parent::boot();
        // 监听 Category 的创建事件，用于初始化 path 和 level 字段值
        static::creating(function (WebsiteCategory $category) {
            // 如果创建的是一个根类目
            if (is_null($category->parent_id)||($category->parent_id == 0)) {
                // 将层级设为 0
                $category->level = 0;
                $category->parent_id = 0;
                // 将 path 设为 -
                $category->path  = '-';
            } else {
                // 将层级设为父类目的层级 + 1
                $category->level = $category->parent->level + 1;
                // 将 path 值设为父类目的 path 追加父类目 ID 以及最后跟上一个 - 分隔符
                $category->path  = $category->parent->path.$category->parent_id.'-';
            }
        });
    }

     // 定一个一个访问器，获取所有祖先类目的 ID 值
     public function getPathIdsAttribute()
     {
         // trim($str, '-') 将字符串两端的 - 符号去除
         // explode() 将字符串以 - 为分隔切割为数组
         // 最后 array_filter 将数组中的空值移除
         return array_filter(explode('-', trim($this->path, '-')));
     }

     // 定义一个访问器，获取所有祖先类目并按层级排序
     public function getAncestorsAttribute()
     {
         return ProductCategory::query()
             // 使用上面的访问器获取所有祖先类目 ID
             ->whereIn('id', $this->path_ids)
             // 按层级排序
             ->orderBy('level')
             ->get();
     }

     // 定义一个访问器，获取以 - 为分隔的所有祖先类目名称以及当前类目的名称
     public function getFullNameAttribute()
     {
         return $this->ancestors  // 获取所有祖先类目
                     ->pluck('label') 
                     ->push($this->label) 
                     ->implode(' - '); // 用 - 符号将数组的值组装成一个字符串
     }
    /**
     * 
     */
    public static function tree($data)
    {
        $items = array();
        foreach($data as $v){
            $items[$v['id']] = $v;
        }
        $tree = array();
        foreach($items as $k => $item){
            if(isset($items[$item['parent_id']])){
                $items[$item['parent_id']]['children'][] = &$items[$k];
            }else{
                $tree[] = &$items[$k];
            }
        }
        return $tree;
    }
}
