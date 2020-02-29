<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebsiteCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('website_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->string('label')->comment('网站分类名称');
            $table->unsignedInteger('level')->comment('当前类目层级');
            $table->string('path')->comment('该类目所有父类目 id');
            $table->unsignedInteger('parent_id')->comment('自表关联');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('website_categories');
    }
}
