<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAwardsCategoriesLogsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // Creating awards tables

        // All of this is based off the existing items tables. With additions from other extensions (like the data function from item entry expansion) but removed the ability for users to transfer from user to user.
        Schema::create('award_categories', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('name');
            $table->string('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);
            $table->integer('sort')->unsigned()->default(0);
            $table->integer('character_limit')->unsigned()->default(0);
            $table->boolean('has_image')->default(0);
            $table->boolean('is_character_owned')->default(0);

         });

        Schema::create('awards', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('award_category_id')->unsigned()->nullable()->default(null);
            $table->string('name');
            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);
            $table->boolean('has_image')->default(0);

            $table->string('data', 1024)->nullable(); // includes rarity and availability information.
            $table->string('reference_url', 200)->nullable();
            $table->string('artist_alias')->nullable();
            $table->string('artist_url')->nullable();

            $table->foreign('award_category_id')->references('id')->on('award_categories');

         });

        Schema::create('awards_log', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('award_id')->unsigned();
            $table->integer('quantity')->unsigned()->default(1);
            $table->integer('stack_id')->unsigned()->nullable();

            $table->integer('sender_id')->unsigned()->nullable();
            $table->integer('recipient_id')->unsigned()->nullable();
            $table->string('log'); // Actual log text
            $table->string('log_type'); // Indicates what type of transaction the item was used in
            $table->string('data', 1024)->nullable(); // Includes information like staff notes, etc.
            $table->timestamp('created_at')->nullable()->default(null);
            $table->enum('sender_type', ['User', 'Character'])->nullable()->default(null);
            $table->enum('recipient_type', ['User', 'Character'])->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);

        });

        // Now to create character specific awards, based on the character items extension
        ///////////////////////
        /////////

        Schema::create('character_awards', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('award_id')->unsigned();
            $table->integer('character_id')->unsigned();

            $table->integer('count')->unsigned()->default(1);

            $table->string('data', 1024)->nullable(); // includes information like staff notes, etc.

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('award_id')->references('id')->on('awards');
            $table->foreign('character_id')->references('id')->on('characters');
        });

        Schema::create('character_awards_log', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('award_id')->unsigned();
            $table->integer('count')->unsigned()->default(1);
            $table->integer('stack_id')->unsigned()->nullable();

            $table->integer('sender_id')->unsigned()->nullable();
            $table->integer('recipient_id')->unsigned()->nullable();
            $table->string('log'); // Actual log text
            $table->string('log_type'); // Indicates what type of transaction the item was used in
            $table->string('data', 1024)->nullable(); // Includes information like staff notes, etc.

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
        Schema::dropIfExists('character_awards_log');
        Schema::dropIfExists('awards_log');
        Schema::dropIfExists('character_awards');
        Schema::dropIfExists('awards');
        Schema::dropIfExists('award_categories');
    }
}
