<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTypeEnumInPractices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    
    public function up()
    {
        $types = [];
        foreach (\App\Practice::getTypes() as $type) {
            $types[] = "'{$type}'::CHARACTER VARYING";
        }

        $types = implode(', ', $types);

//        DB::statement('ALTER TABLE practices DROP CONSTRAINT practices_type_check;');
//        DB::statement("ALTER TABLE practices ADD CONSTRAINT practices_type_check CHECK (type::TEXT = ANY (ARRAY[{$types}]::TEXT[]))");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
