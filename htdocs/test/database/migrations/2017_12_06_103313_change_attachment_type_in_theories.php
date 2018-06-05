<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChangeAttachmentTypeInTheories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        DB::statement("ALTER TABLE theories ALTER COLUMN attachment TYPE JSON USING json_object(array['url_pdf', attachment])");
        Schema::table('theories', function (Blueprint $table) {
            $table->renameColumn('attachment', 'text_pdf');
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
            $table->renameColumn('text_pdf', 'attachment');
        });
        DB::statement("ALTER TABLE theories ALTER COLUMN attachment TYPE TEXT USING attachment::json->'url_pdf'");
    }
}
