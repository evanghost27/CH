<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAwardProgression extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('award_progressions', function (Blueprint $table) {
            $table->integer('award_id');
            $table->string('type');
            $table->integer('type_id');
            $table->integer('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::dropIfExists('award_progressions');
    }
}
