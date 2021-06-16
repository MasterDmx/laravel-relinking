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
            $table->string('from_context', 255);
            $table->string('from_id', 100);
            $table->string('to_context', 255);
            $table->string('context_id', 100);
            $table->double('relevance')->unsigned()->default(0);
            $table->timestamps();

            $table->unique(['from_context', 'from_id', 'to_context', 'to_id'], 'contexts_unique_index');
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
