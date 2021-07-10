<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRelinkingSearchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('linkable_entities', function (Blueprint $table) {

            $table->string('linkable_type', 255);
            $table->unsignedBigInteger('linkable_id');
            $table->mediumText('search');
            $table->timestamps();
        });

        DB::statement('ALTER TABLE relinking_search ADD FULLTEXT search(search)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('relinking_search');
    }
}
