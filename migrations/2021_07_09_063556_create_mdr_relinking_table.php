<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMdrRelinkingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mdr_relinking', function (Blueprint $table) {
            $table->unsignedBigInteger('linkable_id');
            $table->unsignedBigInteger('link_id');
            $table->double('relevance')->unsigned()->default(0);
            $table->timestamps();

            $table->primary(['linkable_id', 'link_id'], 'linkable_unique_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mdr_relinking');
    }
}
