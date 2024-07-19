<?php namespace App\Services;

use DB;
use Carbon\Carbon;
use App\Services\Service;

use App\Models\Advent\AdventCalendar;
use App\Models\Advent\AdventParticipant;
use App\Models\User\User;
use App\Models\Item\Item;

class AdventManager extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Advent Calendar Manager
    |--------------------------------------------------------------------------
    |
    | Handles user claiming of advent calendar prizes.
    |
    */

    /**
     * Claims an advent calendar prize.
     *
     * @param  array                 $data
     * @param  \App\Models\User\User $user
     * @return bool|App\Models\Advent\AdventParticipant
     */
    public function claimPrize($advent, $user)
    {
        DB::beginTransaction();

        try {
            if(!$advent) throw new \Exception ("Invalid advent calendar.");
            if(!$advent->isActive) throw new \Exception ("This advent calendar isn\'t active.");
            if(!$advent->item($advent->day)) throw new \Exception('There is no prize for today.');
            if($advent->participants()->where('user_id', $user->id)->where('day', $advent->day)->exists()) throw new \Exception('You have already claimed today\'s prize.');

            // Log that the user claimed this day's prize
            $participant = AdventParticipant::create([
                'user_id' => $user->id,
                'advent_id' => $advent->id,
                'day' => $advent->day,
                'claimed_at' => Carbon::now()
            ]);

            // Give the user the item(s)
            if(!(new InventoryManager)->creditItem(null, $user, 'Advent Calendar Prize', [
                'data' => $participant->itemData,
                'notes' => 'Claimed ' . format_date($participant->claimed_at)
            ], $advent->item($advent->day), $advent->itemQuantity($advent->day))) throw new \Exception("Failed to claim item.");

            // Check for bonus prize/eligibility
            if($advent->day == $advent->days && isset($advent->data['bonus'])) {
                // Check if the user has a record for each day of the advent
                for($day = 1; $day <= $advent->days; $day++) {
                    if(!$advent->participants->where('user_id', $user->id)->where('day', $day)->first()) {
                        $allDays = false;
                        break;
                    }
                    if(!isset($allDays)) $allDays = true;
                }

                // If all days
                if(isset($allDays) && $allDays) if(!(new InventoryManager)->creditItem(null, $user, 'Advent Calendar Bonus Prize', [
                    'data' => $participant->itemData,
                    'notes' => 'Advent Calendar Bonus Prize'
                ], $advent->item('bonus'), $advent->itemQuantity('bonus'))) throw new \Exception("Failed to claim item.");
            }

            return $this->commitReturn($participant);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

}
