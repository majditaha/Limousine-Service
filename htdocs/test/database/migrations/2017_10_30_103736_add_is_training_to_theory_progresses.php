<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsTrainingToTheoryProgresses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('theory_progresses', function (Blueprint $table) {
            $table->boolean('is_training')->default(false)->after('theory_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('theory_progresses', function (Blueprint $table) {
            $table->dropColumn('is_training');
        });
    }
}
