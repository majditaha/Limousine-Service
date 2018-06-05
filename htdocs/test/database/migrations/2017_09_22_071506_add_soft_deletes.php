<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDeletes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('disciplines', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('theories', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('practices', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('disciplines', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('theories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('practices', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
