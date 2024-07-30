<?php

namespace App\Models\WorldExpansion;

use DB;
use Auth;
use Config;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User\User;
use App\Models\WorldExpansion\EventCategory;
use App\Models\WorldExpansion\Location;
use App\Models\Item\Item;
use App\Models\Prompt\Prompt;
use App\Models\News;


class Event extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','description', 'summary', 'parsed_description', 'sort', 'image_extension', 'thumb_extension',
        'category_id', 'is_active', 'occur_start', 'occur_end'

    ];


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'events';

    protected $dates = ['occur_start', 'occur_end'];

    public $timestamps = true;

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required|unique:events|between:3,50',
        'description' => 'nullable',
        'summary' => 'nullable|max:300',
        'image' => 'mimes:png,gif,jpg,jpeg',
        'image_th' => 'mimes:png,gif,jpg,jpeg',
    ];

    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required|between:3,50',
        'description' => 'nullable',
        'summary' => 'nullable|max:300',
        'image' => 'mimes:png,gif,jpg,jpeg',
        'image_th' => 'mimes:png,gif,jpg,jpeg',
    ];


    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the location attached to this location.
     */
    public function category()
    {
        return $this->belongsTo('App\Models\WorldExpansion\EventCategory', 'category_id');
    }

    /**
     * Get the attacher attached to the model.
     */
    public function attachments()
    {
        return $this->hasMany('App\Models\WorldExpansion\WorldAttachment', 'attacher_id')->where('attacher_type',class_basename($this));
    }

    /**
     * Get the attacher attached to the model.
     */
    public function attachers()
    {
        return $this->hasMany('App\Models\WorldExpansion\WorldAttachment', 'attachment_id')->where('attachment_type',class_basename($this));
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to only include visible posts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query)
    {
        if(!Auth::check() || !(Auth::check() && Auth::user()->isStaff)) return $query->where('is_active', 1);
        else return $query;
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Displays the location's name, linked to its purchase page.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        if($this->is_active) {return '<a href="'.$this->url.'" class="display-location">'.$this->name.'</a>';}
        else {return '<s><a href="'.$this->url.'" class="display-location text-muted">'.$this->name.'</a></s>';}
    }

    /**
     * Displays the location's name, linked to its purchase page.
     *
     * @return string
     */
    public function getFullDisplayNameAttribute()
    {
        if($this->is_active) {return '<a href="'.$this->url.'" class="display-location">'.$this->style.'</a>';}
        else {return '<s><a href="'.$this->url.'" class="display-location text-muted">'.$this->style.'</a></s>';}
    }


    /**
     * Displays the location's name, linked to its purchase page.
     *
     * @return string
     */
    public function getFullDisplayNameUCAttribute()
    {
        if($this->is_active) {return '<a href="'.$this->url.'" class="display-location">'.ucfirst($this->style).'</a>';}
        else {return '<s><a href="'.$this->url.'" class="display-location text-muted">'.ucfirst($this->style).'</a></s>';}
    }

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute()
    {
        return 'images/data/event';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getImageFileNameAttribute()
    {
        return $this->id . '-image.' . $this->image_extension;
    }


    /**
     * Gets the file name of the model's thumbnail image.
     *
     * @return string
     */
    public function getThumbFileNameAttribute()
    {
        return $this->id . '-th.'. $this->thumb_extension;
    }

    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image_extension) return null;
        return asset($this->imageDirectory . '/' . $this->imageFileName);
    }

    /**
     * Gets the URL of the model's thumbnail image.
     *
     * @return string
     */
    public function getThumbUrlAttribute()
    {
        if (!$this->thumb_extension) return null;
        return asset($this->imageDirectory . '/' . $this->thumbFileName);
    }

    /**
     * Gets the URL of the model's encyclopedia page.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('world/events/'.$this->id);
    }



    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/



    /**
     * Scope a query to sort items in category order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortCategory($query)
    {
        $ids = EventCategory::orderBy('sort', 'DESC')->pluck('id')->toArray();
        return count($ids) ? $query->orderByRaw(DB::raw('FIELD(category_id, '.implode(',', $ids).')')) : $query;
    }
    /**
     * Scope a query to sort items in alphabetical order.
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
     * Scope a query to sort items by newest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortNewest($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    /**
     * Scope a query to sort features oldest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOldest($query)
    {
        return $query->orderBy('id');
    }

    public static function getEventsByCategory()
    {
        $sorted_event_categories = collect(EventCategory::all()->sortBy('name')->pluck('name')->toArray());
        $grouped = self::select('name', 'id', 'category_id')->with('category')->orderBy('name')->get()->keyBy('id')->groupBy('category.name', $preserveKeys = true)->toArray();
        if (isset($grouped[''])) {
            if (!$sorted_event_categories->contains('Miscellaneous')) {
                $sorted_event_categories->push('Miscellaneous');
            }
            $grouped['Miscellaneous'] = $grouped['Miscellaneous'] ?? [] + $grouped[''];
        }
        $sorted_event_categories = $sorted_event_categories->filter(function ($value, $key) use ($grouped) {
            return in_array($value, array_keys($grouped), true);
        });
        foreach ($grouped as $category => $events) {
            foreach ($events as $id => $event) {
                $grouped[$category][$id] = $event['name'];
            }
        }
        $events_by_category = $sorted_event_categories->map(function ($type) use ($grouped) {
            return $grouped;
        });
        unset($grouped['']);
        ksort($grouped);

        return $grouped;
    }



}
