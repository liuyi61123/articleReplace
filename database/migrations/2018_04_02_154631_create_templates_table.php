<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('模板名称');
            $table->text('fixed_paragraphs')->nullable()->comment('固定段落列表');
            $table->text('custom_paragraphs')->nullable()->comment('自定义段落列表');
            $table->text('fixed_params')->nullable()->comment('固定参数');
            $table->text('custom_params')->nullable()->comment('自定义参数');
            $table->text('images')->nullable()->comment('图片列表');
            $table->text('content')->comment('模板内容');
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
        Schema::dropIfExists('templates');
    }
}
