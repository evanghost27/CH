<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdventTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advent_calendars', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('name', 64);
            $table->string('display_name', 64);
            $table->string('summary', 256)->nullable()->default(null);

            // Advent calendars are expected to be impermanent, so they need a start and end time.
            // They can't be interacted with outside of these times, but aren't really displayed either,
            // so this should suffice.
            $table->timestamp('start_at')->nullable()->default(null);
            $table->timestamp('end_at')->nullable()->default(null);

            // Data on items and quantities for each day.
            $table->text('data')->nullable()->default(null);
        });

        // Table for advent calendar participants.
        // We need to be able to store if/when a user has claimed the day's item, so
        // we'll create a row each time a user participates in a different calendar.
        // This will also be used to populate the "logs" for individual advent calendars.
        Schema::create('advent_participants', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('user_id')->unsigned()->default(0)->index();
            $table->integer('advent_id')->unsigned()->default(0)->index();

            // Claim status is tracked per prize, since there may be an arbitrary duration/
            // number of prizes.
            $table->integer('day')->unsigned()->default(0);
            $table->timestamp('claimed_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('advent_calendars');
        Schema::dropIfExists('advent_participants');
    }
}
