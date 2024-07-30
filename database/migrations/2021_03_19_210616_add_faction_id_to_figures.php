<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFactionIdToFigures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('figures', function (Blueprint $table) {
            //
            $table->integer('faction_id')->nullable()->default(null)->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('figures', function (Blueprint $table) {
            //
            $table->dropIndex(['faction_id']);
            $table->dropColumn('faction_id');
        });
    }
}
