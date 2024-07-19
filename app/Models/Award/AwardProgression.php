<?php

namespace App\Models\Award;

use Config;
use DB;
use App\Models\Model;

use App\Models\Item\Item;
use App\Models\Currency\Currency;
use App\Models\Award\Award;

class AwardProgression extends Model
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
    protected $table = 'award_progressions';

    /**********************************************************************************************

        ATTRIBUTES

     **********************************************************************************************/

    /**
     * Serve progression type the same way as loot 
     * so as to keep the loot blades being re-used as simple as possible
     */
    public function getRewardableTypeAttribute() {
        return $this->type;
    }

    /**
     * Serve progression type id the same way as loot 
     * so as to keep the loot blades being re-used as simple as possible
     */
    public function getRewardableIdAttribute() {
        return $this->type_id;
    }

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

    /**********************************************************************************************

        OTHER FUNCTIONS 

    **********************************************************************************************/
    /**
     * Checks if the user has the progression
     */
    public function isUnlocked($user)
    {
        switch ($this->type)
        {
            case 'Item':
                // get all the user items with this id and sum the quantity
                return boolval($user->items()->where('item_id', $this->type_id)->sum('count') >= $this->quantity);
                break;
            case 'Currency':
                return \App\Models\User\UserCurrency::where('user_id', $user->id)->where('currency_id', $this->type_id)->sum('quantity') >= $this->quantity;
                break;
            case 'Award':
                return boolval($user->awards()->where('award_id', $this->type_id)->sum('count') >= $this->quantity);
                break;
        }
        return false;
    }

    /**
     * returns image based on whether or not user has this progression
     */
    public function unlocked($user = null, $isStaff = false)
    {
        if ($user) {
            if ($this->isUnlocked($user)) return 
            ($this->progression->imageUrl ?
                '<img src="' . $this->progression->imageUrl .'" class="img-fluid" data-toggle="tooltip" title="' . $this->progression->name . ' (Unlocked)">'
                :
                '<span class="text-success">' . $this->progression->displayName . ' (Unlocked) </span>'
            );
            else return 
            ($this->progression->imageUrl ?
                '<img src="' . $this->progression->imageUrl .'" class="img-fluid" style="filter: grayscale(50%);" data-toggle="tooltip" title="' .  $this->progression->name . ' (Not Unlocked)">'
                :
                '<span class="text-danger">' . $this->progression->displayName . ' (Not Unlocked) </span>'
            );   
        }

        if ($isStaff) return 
        ($this->progression->imageUrl ?
            '<img src="' . $this->progression->imageUrl .'" class="img-fluid" data-toggle="tooltip" title="' . $this->progression->name . ' (Unlocked)">'
            :
            '<span class="text-success">' . $this->progression->displayName . ' (Unlocked) </span>'
        );

        return 
        ($this->progression->imageUrl ?
            '<img src="' . $this->progression->imageUrl .'" class="img-fluid" style="filter: grayscale(50%);" data-toggle="tooltip" title="' .  $this->progression->name . ' (Not Unlocked)">'
            :
            '<span class="text-danger">' . $this->progression->displayName . ' (Not Unlocked) </span>'
        );  
    }
}
