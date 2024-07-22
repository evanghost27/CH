<?php

namespace App\Console\Commands;

use App\Facades\Settings;
use App\Models\Item\Item;
use App\Models\User\User;
use App\Services\InventoryManager;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Notifications;

class DistributeBirthdayReward extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'distribute-birthday-rewards';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Distribute birthday rewards.';

    /**
     * Create a new command instance.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->info("\n".'*******************************');
        $this->info('* BIRTHDAY REWARDS RUNNING *');
        $this->info('*******************************');

        // Get users with a birthday for the given month
        // it grants on the first of the month every month, it prevents players from having their exact day given out if they don't want to
        // may add a setting later where players can opt out of rewards if they don't want even the month to be public
        $birthdayUsers = User::whereMonth('birthday', Carbon::now()->month)->get();

        // this is what will be granted to EVERY user, there is no tweaking it, so if you want to put some randomness in here, just make the selected ID a box with a loot table
        $item = Item::where('id', Settings::get('birthday_item'))->first();

        // For each user
        foreach ($birthdayUsers as $user) {
            try {
                // Exclude users whose birthdays are fully hidden
                if ($user->settings->birthday_setting > 0) {
                    // this is what the "log type" will be in the logs
                    // it's usually something like "staff grant", "shop purchase"
                    // you can change this if you want
                    $logType = 'Birthday Reward';
                    // this is what appears after the log type, it will also show up as the source if it's an item, so you can take it out if you really want, just set it to null, don't outright remove it or it will break
                    // usually it is "recieved item from X", "purchased from X by for (X currency)"
                    // you can change this as well, we're setting it to a birthday message as default because it's cute
                    $data = 'Happy Birthday, '.$user->displayName.'!';

                    if (!(new InventoryManager)->creditItem(null, $user, $logType, [
                        'data'  => $data,
                        'notes' => null,
                    ], $item, 1)) {
                        $this->error('Failed to distribute birthday reward.');
                    }

                    //notify the user of the gift
                    Notifications::create('BIRTHDAY_REWARDED', $user, [
                        'user_name' => $user->name,
                    ]);
                }
            } catch (\Exception $e) {
                $this->error('error:'.$e->getMessage());
            }
        }
        $this->info('Rewards have been distributed');
    }
}
