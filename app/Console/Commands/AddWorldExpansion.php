<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Config;
use DB;
use Carbon\Carbon;

class AddWorldExpansion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-world-expansion';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds the world expansion site settings and pages.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('********************************');
        $this->info('* ADD WORLD EXPANSION SETTINGS *');
        $this->info('********************************'."\n");

        $this->line("Adding world expansion settings...existing entries will be skipped.\n");

        if(!DB::table('site_settings')->where('key', 'WE_change_timelimit')->exists()) {
            DB::table('site_settings')->insert([
                [
                    'key' => 'WE_change_timelimit',
                    'value' => 0,
                    'description' => 'Is there a limit to how often users can change their location? 0: No Limit. 1: Yearly. 2: Quarterly. 3: Monthly. 4: Weekly. 5: Daily.'
                ]

            ]);
            $this->info("Added:   WE_change_timelimit / Default: 0");
        }
        else $this->line("Skipped: WE_change_timelimit");

        if(!DB::table('site_settings')->where('key', 'WE_user_locations')->exists()) {
            DB::table('site_settings')->insert([
                [
                    'key' => 'WE_user_locations',
                    'value' => 0,
                    'description' => '0: Users do not have locations. 1: Users can freely change locations. 2: Only admins can freely change user locations.'
                ]

            ]);
            $this->info("Added:   WE_user_locations / Default: 0");
        }
        else $this->line("Skipped: WE_user_locations");

        if(!DB::table('site_settings')->where('key', 'WE_user_factions')->exists()) {
            DB::table('site_settings')->insert([
                [
                    'key' => 'WE_user_factions',
                    'value' => 0,
                    'description' => '0: Users do not have factions. 1: Users can freely change factions. 2: Only admins can freely change user factions.'
                ]

            ]);
            $this->info("Added:   WE_user_factions / Default: 0");
        }
        else $this->line("Skipped: WE_user_factions");

        if(!DB::table('site_settings')->where('key', 'WE_character_locations')->exists()) {
            DB::table('site_settings')->insert([
                [
                    'key' => 'WE_character_locations',
                    'value' => 0,
                    'description' => '0: Characters do not have locations. 1: Characters\' locations are the same as their owners. 2: Users can edit their own character locations. 3: Only admins can edit character locations.'
                ]

            ]);
            $this->info("Added:   WE_character_locations / Default: 0");
        }
        else $this->line("Skipped: WE_character_locations");

        if(!DB::table('site_settings')->where('key', 'WE_character_factions')->exists()) {
            DB::table('site_settings')->insert([
                [
                    'key' => 'WE_character_factions',
                    'value' => 0,
                    'description' => '0: Characters do not have factions. 1: Characters\' factions are the same as their owners. 2: Users can edit their own character factions. 3: Only admins can edit character factions.'
                ]

            ]);
            $this->info("Added:   WE_character_factions / Default: 0");
        }
        else $this->line("Skipped: WE_character_factions");
        
        if(!DB::table('site_settings')->where('key', 'WE_glossary')->exists()) {
            DB::table('site_settings')->insert([
                [
                    'key' => 'WE_glossary',
                    'value' => 0,
                    'description' => '0: Glossary Page is not shown to users. 1: Glossary Page is shown to users.'
                ]

            ]);
            $this->info("Added:   WE_glossary / Default: 0");
        }
        else $this->line("Skipped: WE_glossary");

        $this->line("\nWorld Expansion settings up to date!");

        //
        $pages = Config::get('lorekeeper.text_pages');


        $this->line("\n");
        $this->info('******************');
        $this->info('* ADD WORLD PAGE *');
        $this->info('******************'."\n");

        $this->line("Adding site page...existing entries will be skipped.\n");


        if(!DB::table('site_pages')->where('key', 'world')->exists()) {
            DB::table('site_pages')->insert([
                [
                    'key' => 'world',
                    'title' => 'World',
                    'text' => '<p>This is the world information page. Edit this from your Pages!</p>',
                    'parsed_text' => '<p>This is the world information page. Edit this from your Pages!</p>',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]

            ]);
            $this->info("Added:   World Info Page");
        }
        else $this->line("Skipped: World Info Page");


    }
}
