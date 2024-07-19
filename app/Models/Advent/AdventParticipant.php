<?php

namespace App\Models\Advent;

use Config;
use DB;
use Carbon\Carbon;
use App\Models\Model;

class AdventParticipant extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'advent_id', 'user_id', 'day', 'claimed_at'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'advent_participants';

    /**
     * Dates on the model to convert to Carbon instances.
     *
     * @var array
     */
    public $dates = ['claimed_at'];

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the participating user.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User\User', 'user_id');
    }

    /**
     * Get the advent calendar being participated in.
     */
    public function advent()
    {
        return $this->belongsTo('App\Models\Advent\AdventCalendar', 'advent_id');
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Get the item data that will be added to the stack as a record of its source.
     *
     * @return string
     */
    public function getItemDataAttribute()
    {
        return 'Claimed from '.$this->advent->displayLink.' day '.$this->day.' by '.$this->user->displayName.'.';
    }

}
