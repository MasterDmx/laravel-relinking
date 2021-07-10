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
            $table->string('linkable_type', 255);
            $table->unsignedBigInteger('linkable_id');
            $table->string('link_type', 255);
            $table->unsignedBigInteger('link_id');
            $table->double('relevance')->unsigned()->default(0);
            $table->timestamps();

            $table->unique(['linkable_type', 'linkable_id', 'link_type', 'link_id'], 'linkable_unique_index');
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
