<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastRenewedAtToDisciplineUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('discipline_users', function (Blueprint $table) {
            $table->datetime('last_renewed_at')->nullable();
            $table->datetime('last_renewed_by_main_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('discipline_users', function (Blueprint $table) {
            $table->dropColumn('last_renewed_at');
            $table->dropColumn('last_renewed_by_main_at');
        });
    }
}
