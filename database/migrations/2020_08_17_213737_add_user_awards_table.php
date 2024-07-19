<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserAwardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //oopsie! I forgot to add the user_awards table
        // welp, here it is!
            Schema::create('user_awards', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('award_id')->unsigned();
            $table->integer('user_id')->unsigned();

            $table->integer('count')->unsigned()->default(0);

            $table->string('data', 1024)->nullable();
                
            $table->integer('trade_count')->unsigned()->default(0);
            $table->unsignedInteger('submission_count')->default(0);
            $table->enum('holding_type', ['Update', 'Trade'])->nullable()->default(null);
            $table->integer('holding_id')->unsigned()->nullable()->default(null); //might remove these later gonna keep it in to test for now
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
            $table->softDeletes();
            $table->unsignedInteger('update_count')->nullable()->default(null);
        

            $table->foreign('award_id')->references('id')->on('awards');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_awards');
    }
}
