<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePracticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('practices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('discipline_id');
            $table->integer('section_id')->nullable();
            $table->integer('treory_id')->nullable();
            $table->string('name');
            $table->text('text');
            $table->integer('order');
            $table->enum('type', \App\Practice::getTypes());
            $table->enum('answer_type', \App\Practice::getAnswerTypes());
            $table->integer('xp_gain')->default(0);
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
        Schema::dropIfExists('practices');
    }
}
