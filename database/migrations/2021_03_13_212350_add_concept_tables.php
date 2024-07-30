<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConceptTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('concept_categories', function (Blueprint $table) {
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

        Schema::create('concepts', function (Blueprint $table) {
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
            $table->foreign('category_id')->references('id')->on('concept_categories')->onDelete('cascade');

            $table->boolean('is_active')->default(1);

            $table->timestamps();
            $table->softDeletes();
        });

        // Associated items for concepts
        Schema::create('concept_items', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('concept_id')->unsigned();
            $table->integer('item_id')->unsigned();
        });

        // Associated locations for concepts
        Schema::create('concept_locations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('concept_id')->unsigned();
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
        Schema::dropIfExists('concept_locations');
        Schema::dropIfExists('concept_items');
        Schema::dropIfExists('concepts');
        Schema::dropIfExists('concept_categories');
    }
}
