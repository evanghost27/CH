<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCharactersToAwardTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // This simply moves all existing award credits to the data column. Why bother with a command when you can do it in the migration!
        foreach(App\Models\Award\Award::all() as $award){
            $data = $award->data;
            $data['credits'] = [];
            if(isset($award->reference_url)) $data['credits'][]     = [ 'name' => 'Reference',  'url' => $award->reference_url, 'id' => null,               'role' => null ];
            if(isset($award->artist_id)) $data['credits'][]         = [ 'name' => null,         'url' => null,                  'id' => $award->artist_id,  'role' => null ];
            if(isset($award->artist_url)) $data['credits'][]        = [ 'name' => 'Artist',     'url' => $award->artist_url,    'id' => null,               'role' => null ];
            $award->update(['data' => $data]);
        }

        Schema::table('awards', function (Blueprint $table) {
            $table->boolean('is_featured')->default(0);                                     // Whether this is separated from the rest to be featured on a character or user. See extension config for count.
            $table->boolean('is_user_owned')->after('data')->default(1);                    // Whether this award is owned by a user. Defaulted to 1 for ease of version upgrade.
            $table->boolean('is_character_owned')->after('is_user_owned')->default(0);      // Whether this award is owned by a character.
            $table->integer('user_limit')->unsigned()->default(0);                          // Max of this award a user may have at a time without being manually added.
            $table->integer('character_limit')->unsigned()->default(0);                     // Max of this award a character may have at a time without being manually added.            $table->dropColumn('extension');
            $table->string('extension', 5)->after('has_image')->nullable()->default('png'); // Allows for gif, etc, award images

            // Add Release code a la Mercury
            $table->boolean('is_released')->default(1);
            $table->boolean('allow_transfer')->default(1);

            // Removing Artist/References to make way for them to be in the data column.
            $table->dropColumn('reference_url');
            $table->dropColumn('artist_url');
            $table->dropColumn('artist_alias'); // This was never actually in the form and therefore was never actually used.
            $table->dropColumn('artist_id');
        });


        Schema::table('award_categories', function (Blueprint $table) {
            $table->dropColumn('is_character_owned');                       // This is now covered by awards individually
            $table->dropColumn('character_limit');                          // This is now covered by awards individually
        });

        // If someone wants to take on the idea of award tags on their own, go ahead. I'm not doing it anytime soon. - Uri :)
        Schema::dropIfExists('award_tags');
        Schema::dropIfExists('character_awards_log');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('awards', function (Blueprint $table) {
            $table->dropColumn('is_featured');
            $table->dropColumn('is_user_owned');
            $table->dropColumn('is_character_owned');
            $table->dropColumn('user_limit');
            $table->dropColumn('character_limit');
            $table->dropColumn('is_released');
            $table->dropColumn('extension');
            $table->dropColumn('allow_transfer');
            // Return credit columns. Doesn't bother moving them from data column to these to account for changes.
            $table->string('reference_url', 200)->nullable();
            $table->string('artist_alias')->nullable();
            $table->string('artist_url')->nullable();
            $table->integer('artist_id')->unsigned()->nullable();
        });

        Schema::table('award_categories', function (Blueprint $table) {
            $table->boolean('is_character_owned')->default(0);
            $table->integer('character_limit')->unsigned()->default(0);
        });

        Schema::create('award_tags', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('award_id')->unsigned();
            $table->string('tag')->index();

            $table->text('data')->nullable()->default(null);
            $table->boolean('is_active')->default(0);
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
}
