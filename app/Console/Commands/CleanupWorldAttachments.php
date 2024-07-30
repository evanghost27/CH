<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use App\Models\WorldExpansion\WorldAttachment;

class CleanupWorldAttachments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup-world-attachments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleans up old style world attachments and moves them into the dynamic system.';

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
     * @return int
     */
    public function handle()
    {
        $this->info("\n".'****************************************');
        $this->info('* TRANSFER WORLD EXPANSION ATTACHMENTS *');
        $this->info('****************************************'."\n");

        $this->info('This command will check for attachments/associations using the old Attachment system (FigureLocation, etc) and will move them to the WorldAttachment model.');
        if($this->confirm('Are you sure you want to run this? It will delete the tables and will move all entries to world_attachments.')) {
            // Migrate
            $this->line('Running migrations, just in case...');
            $this->call('migrate');
            $this->line("");

            $tables = [
                'concept_items',        'concept_locations',
                'event_factions',       'event_figures',        'event_locations',     'event_newses',    'event_prompts',
                'faction_figures',      'faction_locations',
                'fauna_items',          'fauna_locations',
                'flora_items',          'flora_locations',      'figure_items',
            ];


            foreach($tables as $table){
                if(Schema::hasTable($table)) {
                    $this->line("");
                    $this->line('Cleaning up table "'.$table.'".');
                    $this->processAttachment($table);
                } else {
                    $this->line('Table "'.$table.'" does not exist.');
                }
            }

            $this->line("");
            $this->info('Cleanup complete!');
            $this->line('Remember to check each page to ensure there are no remnants of things like "figure->items" that would cause issues.');
            $this->line("");

        }
        else $this->line('Aborting!');
    }

    public function processAttachment($table){
        $entries = DB::table($table)->get();
        $parts = explode("_",$table);
        switch($parts[0]){
            case "concept": $attacher_type = "Concept";     $attacher_id = "concept_id";    break;
            case "event":   $attacher_type = "Event";       $attacher_id = "event_id";      break;
            case "faction": $attacher_type = "Faction";     $attacher_id = "faction_id";    break;
            case "fauna":   $attacher_type = "Fauna";       $attacher_id = "fauna_id";      break;
            case "flora":   $attacher_type = "Flora";       $attacher_id = "flora_id";      break;
            case "figure":  $attacher_type = "Figure";      $attacher_id = "figure_id";     break;
            default:        $attacher_type = "fail";        $attacher_id = "fail";          break;
        }
        switch($parts[1]){
            case "items":       $attachment_type = "Item";        $attachment_id = "item_id";       break;
            case "locations":   $attachment_type = "Location";    $attachment_id = "location_id";   break;
            case "factions":    $attachment_type = "Faction";     $attachment_id = "faction_id";    break;
            case "figures":     $attachment_type = "Figure";      $attachment_id = "figure_id";     break;
            case "newses":      $attachment_type = "News";        $attachment_id = "news_id";       break;
            case "prompts":     $attachment_type = "Prompt";      $attachment_id = "prompt_id";     break;
            default:            $attachment_type = "fail";        $attachment_id = "fail";          break;
        }

        if($entries->count() > 0) {
            foreach($entries as $entry){
                WorldAttachment::create([
                    'attacher_id'       =>  $entry->$attacher_id,
                    'attacher_type'     => $attacher_type,
                    'attachment_id'     =>  $entry->$attachment_id,
                    'attachment_type'   => $attachment_type,
                ]);
            }
            $this->info('Created attachments between '.$entries->count().' '.$parts[0].' and '.$parts[1].'.');
        } else {
            $this->line('No attachments between '.$parts[0].' and '.$parts[1].'.');
        }
        Schema::dropIfExists($table);
        $this->line('Deleted table '.$table.'.');

    }
}
