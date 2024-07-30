<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoryTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Categories of historical events
        Schema::create('event_categories', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();

            $table->string('name');
            $table->string('summary', 300)->nullable()->default(null); 

            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);

            $table->string('image_extension', 191)->nullable()->default(null); 
            $table->string('thumb_extension', 191)->nullable()->default(null); 
            $table->integer('sort')->unsigned()->default(0); 

            $table->boolean('is_active')->default(1);

            $table->timestamps();
            $table->softDeletes();
        });

        // Categories of historical figures
        Schema::create('figure_categories', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();

            $table->string('name');
            $table->string('summary', 300)->nullable()->default(null); 

            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);

            $table->string('image_extension', 191)->nullable()->default(null); 
            $table->string('thumb_extension', 191)->nullable()->default(null); 
            $table->integer('sort')->unsigned()->default(0); 

            $table->boolean('is_active')->default(1);

            $table->timestamps();
            $table->softDeletes();
        });

        //  Events
        Schema::create('events', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();

            $table->string('name');
            $table->string('summary', 300)->nullable()->default(null); 

            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);

            $table->string('image_extension', 191)->nullable()->default(null); 
            $table->string('thumb_extension', 191)->nullable()->default(null); 
            $table->integer('sort')->unsigned()->default(0); 

            $table->integer('category_id')->nullable()->default(null)->unsigned();
            $table->foreign('category_id')->references('id')->on('event_categories')->onDelete('cascade');

            $table->boolean('is_active')->default(1);

            $table->dateTime('occur_start')->nullable()->default(null);
            $table->dateTime('occur_end')->nullable()->default(null);

            $table->timestamps();
            $table->softDeletes();
        });

        //  Figures like gods, kings, etc
        Schema::create('figures', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();

            $table->string('name');
            $table->string('summary', 300)->nullable()->default(null); 

            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);

            $table->string('image_extension', 191)->nullable()->default(null); 
            $table->string('thumb_extension', 191)->nullable()->default(null); 
            $table->integer('sort')->unsigned()->default(0); 

            $table->integer('category_id')->nullable()->default(null)->unsigned();
            $table->foreign('category_id')->references('id')->on('figure_categories')->onDelete('cascade');

            $table->boolean('is_active')->default(1);

            $table->dateTime('birth_date')->nullable()->default(null);
            $table->dateTime('death_date')->nullable()->default(null);

            $table->timestamps();
            $table->softDeletes();
        });

        // Associated items for Figures
        Schema::create('event_figures', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('event_id')->unsigned();
            $table->integer('figure_id')->unsigned();
        });

        // Associated items for Figures (think mythical swords etc)
        Schema::create('figure_items', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('figure_id')->unsigned();
            $table->integer('item_id')->unsigned();
        });

        // Associated locations for Figures
        Schema::create('event_locations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('event_id')->unsigned();
            $table->integer('location_id')->unsigned();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_locations');
        Schema::dropIfExists('event_figures');
        Schema::dropIfExists('figure_items');
        Schema::dropIfExists('events');
        Schema::dropIfExists('figures');
        Schema::dropIfExists('event_categories');
        Schema::dropIfExists('figure_categories');
    }
}
