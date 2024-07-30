<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorldExpansionAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('world_attachments', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('attacher_id')->unsigned()->index();
            $table->string('attacher_type');
            $table->integer('attachment_id')->unsigned()->index();
            $table->string('attachment_type');                          // AKA Figure, Items, etc.
            $table->text('data')->nullable()->default(null);            // In case of specific notes
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('world_attachments');
    }
}
