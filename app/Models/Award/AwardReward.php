<?php

namespace App\Models\Award;

use Config;
use DB;
use App\Models\Model;

use App\Models\Item\Item;
use App\Models\Currency\Currency;
use App\Models\Award\Award;

class AwardReward extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'award_id', 'type', 'type_id', 'quantity'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'award_rewards';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the award the progression belongs to.
     */
    public function award()
    {
        return $this->belongsTo('App\Models\Award\Award', 'award_id');
    }

    /**
     * get the type of award progression.
     */
    public function progression()
    {
        switch ($this->type)
        {
            case 'Item':
                return $this->belongsTo('App\Models\Item\Item', 'type_id');
                break;
            case 'Currency':
                return $this->belongsTo('App\Models\Currency\Currency', 'type_id');
                break;
            case 'Award':
                return $this->belongsTo('App\Models\Award\Award', 'type_id');
                break;
        }
        return null;
    }
}
