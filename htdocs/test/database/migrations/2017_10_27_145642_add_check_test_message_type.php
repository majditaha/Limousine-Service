<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCheckTestMessageType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void 
     */
    
    public function up()
    {
        $types = [];
        foreach (\App\Message::getTypes() as $type) {
            $types[] = "'{$type}'::CHARACTER VARYING";
        }
        $types = implode(', ', $types);

//        DB::statement('ALTER TABLE messages DROP CONSTRAINT messages_type_check;');
//        DB::statement("ALTER TABLE messages ADD CONSTRAINT messages_type_check CHECK (type::TEXT = ANY (ARRAY[{$types}]::TEXT[]))");
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
