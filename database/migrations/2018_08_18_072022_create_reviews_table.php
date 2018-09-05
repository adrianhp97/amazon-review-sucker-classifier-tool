<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('report_id');
            $table->string('asin');
            $table->string('title');
            $table->string('title_link');
            $table->integer('score');
            $table->date('date');
            $table->string('author');
            $table->string('author_link');
            $table->string('number_of_comments');
            $table->boolean('photos_or_video');
            $table->boolean('verified');
            $table->boolean('child_product')->default(false);
            $table->boolean('child_asin')->nullable();
            $table->timestamps();
            $table->foreign('report_id')->references('id')->on('reports');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
