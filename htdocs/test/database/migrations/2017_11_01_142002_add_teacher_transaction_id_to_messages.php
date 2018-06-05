<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTeacherTransactionIdToMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->renameColumn('transaction_id', 'user_transaction_id');
        });
        Schema::table('messages', function (Blueprint $table) {
            $table->integer('teacher_transaction_id')->nullable()->after('user_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('teacher_transaction_id');
        });
        Schema::table('messages', function (Blueprint $table) {
            $table->renameColumn('user_transaction_id', 'transaction_id');
        });
    }
}
