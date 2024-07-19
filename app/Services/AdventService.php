<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;
use Carbon\Carbon;

use Illuminate\Support\Arr;
use App\Models\Advent\AdventCalendar;
use App\Models\Advent\AdventParticipant;
use App\Models\Item\Item;

class AdventService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Advent Calendar Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of advent calendars.
    |
    */

    /**
     * Creates a new advent calendar.
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Advent\AdventCalendar
     */
    public function createAdvent($data, $user)
    {
        DB::beginTransaction();

        try {
            if(!$data['start_at']) throw new \Exception ('A start time is required.');
            if(!$data['end_at']) throw new \Exception ('An end time is required.');

            $advent = AdventCalendar::create(Arr::only($data, ['name', 'display_name', 'summary', 'start_at', 'end_at']));

            return $this->commitReturn($advent);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a advent calendar.
     *
     * @param  \App\Models\Advent\AdventCalendar        $advent
     * @param  array                                    $data
     * @param  \App\Models\User\User                    $user
     * @return bool|\App\Models\Advent\AdventCalendar
     */
    public function updateAdvent($advent, $data, $user)
    {
        DB::beginTransaction();

        try {
            if(AdventCalendar::where('name', $data['name'])->where('id', '!=', $advent->id)->exists()) throw new \Exception("The name has already been taken.");
            if(!$data['start_at']) throw new \Exception ('A start time is required.');
            if(!$data['end_at']) throw new \Exception ('An end time is required.');

            // Check if there are participants that have claimed days greater than the new duration
            if($advent->participants()->count()) {
                $startAt = Carbon::create($data['start_at']); $endAt = Carbon::create($data['end_at']);
                if($advent->participants()->pluck('day')->max() > ($startAt->startOf('day')->diffInDays($endAt->endOf('day'))+1)) throw new \Exception('The new duration is shorter than the number of days the advent has already run for.');
            }

            // Process and encode prize information
            if(isset($data['item_ids'])) {
                // Check prizes for each day
                for($day = 1; $day <= $advent->days; $day++) {
                    if(isset($data['item_ids'][$day])) {
                        // Check that the item exists
                        $item = Item::find($data['item_ids'][$day]);
                        if(!$item) throw new \Exception('One or more of the items selected is invalid.');

                        $data['data'][$day] = [
                            'item' => $data['item_ids'][$day],
                            'quantity' => isset($data['quantities'][$day]) ? $data['quantities'][$day] : 1
                        ];
                    }
                }

                // Process bonus prize, if set
                if(isset($data['item_ids']['bonus'])) {
                    // Check that the item exists
                    $item = Item::find($data['item_ids']['bonus']);
                    if(!$item) throw new \Exception('One or more of the items selected is invalid.');

                    $data['data']['bonus'] = [
                        'item' => $data['item_ids']['bonus'],
                        'quantity' => isset($data['quantities']['bonus']) ? $data['quantities']['bonus'] : 1
                    ];
                }

                // Encode the prize data
                if (isset($data['data'])) $data['data'] = json_encode($data['data']);
				else $data['data'] = null;
            }

            $advent->update(Arr::only($data, ['name', 'display_name', 'summary', 'start_at', 'end_at', 'data']));

            return $this->commitReturn($advent);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Deletes an advent calendar.
     *
     * @param  \App\Models\Advent\AdventCalendar  $advent
     * @return bool
     */
    public function deleteAdvent($advent)
    {
        DB::beginTransaction();

        try {
            // Check first if the advent calendar has had participants
            if(AdventParticipant::where('advent_id', $advent->id)->exists()) throw new \Exception("A user has participated in this advent calendar, so deleting it would break the logs. While advent calendars remain visible after their end time, they cannot be interacted with.");

            $advent->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

}
