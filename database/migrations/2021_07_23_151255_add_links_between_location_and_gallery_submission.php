<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLinksBetweenLocationAndGallerySubmission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('galleries', function (Blueprint $table) {
            $table->boolean('location_selection')->default(0);
        });

        Schema::table('gallery_submissions', function (Blueprint $table) {
            $table->integer('location_id')->after('prompt_id')->unsigned()->nullable()->default(null);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gallery_submissions', function (Blueprint $table) {
            $table->dropColumn('location_id');
        });
        Schema::table('galleries', function (Blueprint $table) {
            $table->dropColumn('location_selection');
        });
    }
}
