<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndicesToAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('api_token');
            $table->index('city_id');
            $table->index('school_id');
        });
        Schema::table('variants', function (Blueprint $table) {
            $table->index('discipline_id');
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->index('from_user_id');
            $table->index('to_user_id');
            $table->index('discipline_id');
            $table->index('section_id');
            $table->index('practice_id');
        });
        Schema::table('theory_progresses', function (Blueprint $table) {
            $table->index('theory_id');
            $table->index('user_id');
        });
        Schema::table('theories', function (Blueprint $table) {
            $table->index('section_id');
        });
        Schema::table('subtypes', function (Blueprint $table) {
            $table->index('section_id');
        });
        Schema::table('sections', function (Blueprint $table) {
            $table->index('discipline_id');
        });
        Schema::table('section_progresses', function (Blueprint $table) {
            $table->index('section_id');
            $table->index('user_id');
        });
        Schema::table('schools', function (Blueprint $table) {
            $table->index('city_id');
        });
        Schema::table('purchases', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('plan_id');
        });
        Schema::table('practices', function (Blueprint $table) {
            $table->index('discipline_id');
            $table->index('section_id');
            $table->index('theory_id');
            $table->index('variant_id');
            $table->index('subtype_id');
        });
        Schema::table('practice_users', function (Blueprint $table) {
            $table->index('practice_id');
            $table->index('user_id');
        });
        Schema::table('practice_progresses', function (Blueprint $table) {
            $table->index('practice_id');
            $table->index('user_id');
        });
        Schema::table('pages', function (Blueprint $table) {
            $table->index('menu_item_id');
        });
        Schema::table('messages', function (Blueprint $table) {
            $table->index('from_user_id');
            $table->index('to_user_id');
            $table->index('message_answered_id');
            $table->index('practice_id');
            $table->index('theory_id');
            $table->index('teacher_id');
            $table->index('teacher_transaction_id');
        });
        Schema::table('menu_items', function (Blueprint $table) {
            $table->index('parent_id');
        });
        Schema::table('files', function (Blueprint $table) {
            $table->index('owner_type');
            $table->index('owner_id');
        });
        Schema::table('discipline_users', function (Blueprint $table) {
            $table->index('discipline_id');
            $table->index('user_id');
        });
        Schema::table('answers', function (Blueprint $table) {
            $table->index('practice_id');
        });
        Schema::table('answer_users', function (Blueprint $table) {
            $table->index('answer_id');
            $table->index('user_id');
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
            $table->dropIndex(['api_token']);
            $table->dropIndex(['city_id']);
            $table->dropIndex(['school_id']);
        });
        Schema::table('variants', function (Blueprint $table) {
            $table->dropIndex(['discipline_id']);
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['from_user_id']);
            $table->dropIndex(['to_user_id']);
            $table->dropIndex(['discipline_id']);
            $table->dropIndex(['section_id']);
            $table->dropIndex(['practice_id']);
        });
        Schema::table('theory_progresses', function (Blueprint $table) {
            $table->dropIndex(['theory_id']);
            $table->dropIndex(['user_id']);
        });
        Schema::table('theories', function (Blueprint $table) {
            $table->dropIndex(['section_id']);
        });
        Schema::table('subtypes', function (Blueprint $table) {
            $table->dropIndex(['section_id']);
        });
        Schema::table('sections', function (Blueprint $table) {
            $table->dropIndex(['discipline_id']);
        });
        Schema::table('section_progresses', function (Blueprint $table) {
            $table->dropIndex(['section_id']);
            $table->dropIndex(['user_id']);
        });
        Schema::table('schools', function (Blueprint $table) {
            $table->dropIndex(['city_id']);
        });
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['plan_id']);
        });
        Schema::table('practices', function (Blueprint $table) {
            $table->dropIndex(['discipline_id']);
            $table->dropIndex(['section_id']);
            $table->dropIndex(['theory_id']);
            $table->dropIndex(['variant_id']);
            $table->dropIndex(['subtype_id']);
        });
        Schema::table('practice_users', function (Blueprint $table) {
            $table->dropIndex(['practice_id']);
            $table->dropIndex(['user_id']);
        });
        Schema::table('practice_progresses', function (Blueprint $table) {
            $table->dropIndex(['practice_id']);
            $table->dropIndex(['user_id']);
        });
        Schema::table('pages', function (Blueprint $table) {
            $table->dropIndex(['menu_item_id']);
        });
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['from_user_id']);
            $table->dropIndex(['to_user_id']);
            $table->dropIndex(['message_answered_id']);
            $table->dropIndex(['practice_id']);
            $table->dropIndex(['theory_id']);
            $table->dropIndex(['teacher_id']);
            $table->dropIndex(['teacher_transaction_id']);
        });
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropIndex(['parent_id']);
        });
        Schema::table('files', function (Blueprint $table) {
            $table->dropIndex(['owner_type']);
            $table->dropIndex(['owner_id']);
        });
        Schema::table('discipline_users', function (Blueprint $table) {
            $table->dropIndex(['discipline_id']);
            $table->dropIndex(['user_id']);
        });
        Schema::table('answers', function (Blueprint $table) {
            $table->dropIndex(['practice_id']);
        });
        Schema::table('answer_users', function (Blueprint $table) {
            $table->dropIndex(['answer_id']);
            $table->dropIndex(['user_id']);
        });
    }
}
