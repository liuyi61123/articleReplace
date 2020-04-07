<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebsitePushesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('website_pushes', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('is_automatic')->default(false)->comment('是否每天定时执行');
            $table->unsignedTinyInteger('status')->default(0)->comment('0未运行，1运行中，2已停止，3执行完成');
            $table->unsignedInteger('delay')->default(0)->comment('延时秒数');
            $table->string('name')->comment('任务名称');
            $table->text('config')->comment('配置信息');
            $table->text('error')->nullable()->comment('错误信息');
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
        Schema::dropIfExists('website_pushes');
    }
}
