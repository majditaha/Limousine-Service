<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeTextNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('theories', function (Blueprint $table) {
            $table->text('text')->nullable()->change();
        });

        Schema::table('practices', function (Blueprint $table) {
            $table->text('text')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('theories', function (Blueprint $table) {
            $table->text('text')->change();
        });

        Schema::table('practices', function (Blueprint $table) {
            $table->text('text')->change();
        });
    }
}
