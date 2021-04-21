<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelinkingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relinking', function (Blueprint $table) {
            $table->id();
            $table->string('for_context_alias', 255);
            $table->string('for_context_id', 100);
            $table->string('context_alias', 255);
            $table->string('context_id', 100);
            $table->double('relevance')->unsigned()->default(0);
            $table->timestamps();

            $table->unique(['for_context_alias', 'for_context_id', 'context_alias', 'context_id'], 'contexts_unique_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('relinking');
    }
}
