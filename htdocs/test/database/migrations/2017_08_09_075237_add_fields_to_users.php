<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('city_id')->nullable();
            $table->integer('school_id')->nullable();
            $table->string('grade')->nullable();
            $table->string('grade_name')->nullable();
            $table->integer('gender')->nullable();
            $table->integer('speciality')->nullable();
            $table->string('passport_file')->nullable();
            $table->string('empl_history_file')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('active')->default(true);
            $table->date('birth_date')->nullable();
            $table->integer('desired_hours_to_spend')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
//            $table->dropColumn('city');
//            $table->dropColumn('school_id');
//            $table->dropColumn('grade');
//            $table->dropColumn('grade_name');
//            $table->dropColumn('gender');
//            $table->dropColumn('speciality');
//            $table->dropColumn('passport');
//            $table->dropColumn('empl_history');
//            $table->dropColumn('phone');
//            $table->dropColumn('active');
//            $table->dropColumn('desired_hours_to_spend');
        });
    }
}
