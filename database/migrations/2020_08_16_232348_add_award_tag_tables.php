<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAwardTagTables extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        // Creates tags for awards that are the exact same as tags for items. Not sure how they'll be implemented but the code will be there if someone wants to build on it
        Schema::create('award_tags', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('award_id')->unsigned();
            $table->string('tag')->index();

            $table->text('data')->nullable()->default(null);
            $table->boolean('is_active')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::dropIfExists('award_tags');
    }
}
