<?php

namespace App\Models\Advent;

use Config;
use DB;
use Carbon\Carbon;

use App\Models\Item\Item;

use App\Models\Model;

class AdventCalendar extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'display_name', 'summary', 'start_at', 'end_at', 'data'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'advent_calendars';

    /**
     * Dates on the model to convert to Carbon instances.
     *
     * @var array
     */
    public $dates = ['start_at', 'end_at'];

    /**
     * Validation rules for advent calendar creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required|unique:advent_calendars|between:3,50',
        'display_name' => 'required|between:3,40',
        'summary' => 'nullable'
    ];

    /**
     * Validation rules for advent calendar updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required|between:3,50',
        'display_name' => 'required|between:3,40',
        'summary' => 'nullable'
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the participation logs attached to this advent calendar.
     */
    public function participants()
    {
        return $this->hasMany('App\Models\Advent\AdventParticipant', 'advent_id')->orderBy('day');
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to only include active advents.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('start_at', '<=', Carbon::now())->where('end_at', '>=', Carbon::now());
    }

    /**
     * Scope a query to sort advent calendars in alphabetical order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  bool                                   $reverse
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortAlphabetical($query, $reverse = false)
    {
        return $query->orderBy('name', $reverse ? 'DESC' : 'ASC');
    }

    /**
     * Scope a query to sort advent calendars by newest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortNewest($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    /**
     * Scope a query to sort advent calendars oldest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOldest($query)
    {
        return $query->orderBy('id');
    }

    /**
     * Scope a query to sort advent calendars by start date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  bool                                   $reverse
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortStart($query, $reverse = false)
    {
        return $query->orderBy('start_at', $reverse ? 'DESC' : 'ASC');
    }

    /**
     * Scope a query to sort advent calendars by end date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  bool                                   $reverse
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortEnd($query, $reverse = false)
    {
        return $query->orderBy('end_at', $reverse ? 'DESC' : 'ASC');
    }

    /**
     * Scope a query to get participants of a particular advent calendar.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParticipants($query)
    {
        $query->select('advent_participants.*')->where('advent_id', $this->id);
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Displays the model's name.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return $this->attributes['display_name'];
    }

    /**
     * Displays the model's name, linked to its page.
     *
     * @return string
     */
    public function getDisplayLinkAttribute()
    {
        return '<a href="'.$this->url.'" class="display-prompt">'.$this->attributes['display_name'].'</a>';
    }

    /**
     * Gets the URL of the model's page.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('advent-calendars/'.$this->id);
    }

    /**
     * Check if the advent calendar is active or not.
     *
     * @return string
     */
    public function getIsActiveAttribute()
    {
        if($this->start_at->isPast() && $this->end_at->isFuture())
            return TRUE;
        else
            return FALSE;

    }

    /**
     * Get the data attribute as an associative array.
     *
     * @return array
     */
    public function getDataAttribute()
    {
        if (!$this->id) return null;
        return json_decode($this->attributes['data'], true);
    }

    /**
     * Get the number of days the advent calendar extends.
     *
     * @return int
     */
    public function getDaysAttribute()
    {
        return $this->start_at->startOf('day')->diffInDays($this->end_at->endOf('day'))+1;
    }

    /**
     * Get the current day of the advent calendar, if it is active.
     *
     * @return int
     */
    public function getDayAttribute()
    {
        if(!$this->isActive) return null;
        return $this->start_at->startOf('day')->diffInDays(Carbon::now()->endOf('day'))+1;
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/

    /**
     * Get the item for the current day.
     *
     * @param int              $day
     * @return App\Models\Item\Item
     */
    public function item($day)
    {
        if(!isset($this->data[$day]['item'])) return null;
        return Item::find($this->data[$day]['item']);
    }

    /**
     * Get the item quantity for the current day.
     *
     * @param int           $day
     * @return int
     */
    public function itemQuantity($day)
    {
        return (isset($this->data[$day]['quantity']) ? $this->data[$day]['quantity'] : 1);
    }

    /**
     * Displays the target item and its quantity.
     *
     * @param int           $day
     * @return string
     */
    public function displayItem($day)
    {
        $item = $this->item($day);
		if (!$item) return 'Deleted Asset';
        $image = ($item->imageUrl) ? '<img class="small-icon" src="'.$item->imageUrl.'"/>' : null;
        return $image.' '.$item->displayName.' ×'.$this->itemQuantity($day);
    }

    /**
     * Displays the target item and its quantity.
     *
     * @param int           $day
     * @return string
     */
    public function displayItemLong($day)
    {
        $item = $this->item($day);
		if (!$item) return 'Deleted Asset';
        $image = ($item->imageUrl) ? '<img style="max-height:150px;" src="'.$item->imageUrl.'" data-toggle="tooltip" title="'.$item->name.'"/>' : null;
        return $image.(isset($image) ? '<br/>' : '').' '.$item->displayName.' ×'.$this->itemQuantity($day);
    }

    /**
     * Displays the target item.
     *
     * @return string
     */
    public function displayItemShort($day)
    {
        $item = $this->item($day);
		if (!$item) return 'Deleted Asset';
        $image = ($item->imageUrl) ? '<img style="max-height:150px;" src="'.$item->imageUrl.'" data-toggle="tooltip" title="'.$item->name.'"/>' : null;
        if(isset($image)) return $image;
        else return $item->displayName;
    }

}
