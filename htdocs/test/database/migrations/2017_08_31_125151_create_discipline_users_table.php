<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDisciplineUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discipline_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('discipline_id');
            $table->integer('user_id');
            $table->integer('desired_hours_to_spend')->nullable();
            $table->integer('desired_score_to_get')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discipline_users');
    }
}
