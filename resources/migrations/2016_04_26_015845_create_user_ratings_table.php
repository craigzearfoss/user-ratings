<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_ratings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('namespace');
            $table->integer('entity_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->text('comment')->nullable()->default(null);
            $table->boolean('like')->default(false);
            $table->boolean('dislike')->default(false);
            $table->boolean('favorite')->default(false);
            $table->integer('rating')->nullable()->default(null);
            $table->timestamps();

            $table->unique(array('namespace', 'entity_id', 'user_id'));

            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_ratings');
    }
}
