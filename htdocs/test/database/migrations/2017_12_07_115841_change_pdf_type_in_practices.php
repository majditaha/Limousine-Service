<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChangePdfTypeInPractices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        DB::statement("ALTER TABLE practices ALTER COLUMN text_pdf TYPE JSON USING json_object(array['url_pdf', text_pdf])");
//        DB::statement("ALTER TABLE practices ALTER COLUMN hint_pdf TYPE JSON USING json_object(array['url_pdf', hint_pdf])");
//        DB::statement("ALTER TABLE practices ALTER COLUMN solution_pdf TYPE JSON USING json_object(array['url_pdf', solution_pdf])");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE theories ALTER COLUMN text_pdf TYPE TEXT USING text_pdf::json->'url_pdf'");
        DB::statement("ALTER TABLE theories ALTER COLUMN hint_pdf TYPE TEXT USING hint_pdf::json->'url_pdf'");
        DB::statement("ALTER TABLE theories ALTER COLUMN solution_pdf TYPE TEXT USING solution_pdf::json->'url_pdf'");
    }
}
