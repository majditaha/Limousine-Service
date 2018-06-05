<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPdfToPractices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('practices', function (Blueprint $table) {
            $table->string('text_pdf')->nullable()->after('text');
            $table->string('hint_pdf')->nullable()->after('hint');
            $table->string('solution_pdf')->nullable()->after('solution');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('practices', function (Blueprint $table) {
            $table->dropColumn('text_pdf');
            $table->dropColumn('hint_pdf');
            $table->dropColumn('solution_pdf');
        });
    }
}
