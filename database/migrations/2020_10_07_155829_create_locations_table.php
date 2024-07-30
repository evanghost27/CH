<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location_types', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('name');
            $table->string('names');
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

        Schema::create('locations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('name');
            $table->string('summary', 300)->nullable()->default(null); 

            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);

            $table->string('image_extension', 191)->nullable()->default(null); 
            $table->string('thumb_extension', 191)->nullable()->default(null); 
            $table->integer('sort')->unsigned()->default(0); 

            $table->integer('parent_id')->unsigned()->nullable()->default(null);
            $table->integer('type_id')->unsigned();
            $table->integer('display_style')->unsigned()->default(0);

            $table->foreign('type_id')->references('id')->on('location_types')->onDelete('cascade');

            $table->boolean('is_active')->default(1);

            $table->boolean('is_character_home')->default(0);
            $table->boolean('is_user_home')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('home_id')->unsigned()->nullable()->default(null);
            $table->foreign('home_id')->references('id')->on('locations')->onDelete('set null');
            $table->dateTime('home_changed')->nullable()->default(null);
        });

        Schema::table('characters', function (Blueprint $table) {
            $table->integer('home_id')->unsigned()->nullable()->default(null);
            $table->foreign('home_id')->references('id')->on('locations')->onDelete('set null');
            $table->dateTime('home_changed')->nullable()->default(null);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['home_id']);
            $table->dropColumn('home_id');
            $table->dropColumn('home_changed');
        });

        Schema::table('characters', function (Blueprint $table) {
            $table->dropForeign(['home_id']);
            $table->dropColumn('home_id');
            $table->dropColumn('home_changed');
        });
        
        Schema::dropIfExists('locations');
        Schema::dropIfExists('location_types');

    }
}
