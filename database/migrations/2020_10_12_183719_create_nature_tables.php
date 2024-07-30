<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNatureTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fauna_categories', function (Blueprint $table) {
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

        Schema::create('flora_categories', function (Blueprint $table) {
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

        // Animals etc
        Schema::create('faunas', function (Blueprint $table) {
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
            $table->foreign('category_id')->references('id')->on('fauna_categories')->onDelete('cascade');

            $table->boolean('is_active')->default(1);

            $table->timestamps();
            $table->softDeletes();
        });

        // Plants etc
        Schema::create('floras', function (Blueprint $table) {
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
            $table->foreign('category_id')->references('id')->on('flora_categories')->onDelete('cascade');

            $table->boolean('is_active')->default(1);

            $table->timestamps();
            $table->softDeletes();
        });

        // Associated items for Fauna
        Schema::create('fauna_items', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('fauna_id')->unsigned();
            $table->integer('item_id')->unsigned();
        });

        // Associated items for Flora
        Schema::create('flora_items', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('flora_id')->unsigned();
            $table->integer('item_id')->unsigned();
        });

        // Associated locations for Fauna
        Schema::create('fauna_locations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('fauna_id')->unsigned();
            $table->integer('location_id')->unsigned();
        });

        // Associated locations for Flora
        Schema::create('flora_locations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('flora_id')->unsigned();
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
        Schema::dropIfExists('fauna_locations');
        Schema::dropIfExists('flora_locations');
        Schema::dropIfExists('fauna_items');
        Schema::dropIfExists('flora_items');
        Schema::dropIfExists('floras');
        Schema::dropIfExists('faunas');
        Schema::dropIfExists('fauna_categories');
        Schema::dropIfExists('flora_categories');
    }
}
