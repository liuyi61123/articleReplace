<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleParagraphsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_paragraphs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('段落名称');
            $table->string('category')->comment('类别');
            $table->string('identifier')->comment('标识符');
            $table->mediumText('content')->comment('内容');
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
        Schema::dropIfExists('article_paragraphs');
    }
}
