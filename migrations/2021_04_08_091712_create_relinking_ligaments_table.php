<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelinkingLigamentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relinking_ligaments', function (Blueprint $table) {
            $table->id();
            $table->string('context', 30);
            $table->string('item_id', 40);
            $table->bigInteger('link_id')->index()->unsigned();
            $table->double('relevance')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('relinking_ligaments');
    }
}
