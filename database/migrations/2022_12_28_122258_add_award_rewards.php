<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAwardRewards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('award_rewards', function (Blueprint $table) {
            $table->integer('award_id');
            $table->string('type');
            $table->integer('type_id');
            $table->integer('quantity');
        });

        Schema::table('awards', function (Blueprint $table) {
            $table->boolean('allow_reclaim')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('award_rewards');

        Schema::table('awards', function (Blueprint $table) {
            $table->dropColumn('allow_reclaim');
        });
    }
}
