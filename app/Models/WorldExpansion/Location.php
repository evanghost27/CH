<?php

namespace App\Models\WorldExpansion;

use DB;
use Auth;
use Config;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User\User;
use App\Models\WorldExpansion\LocationType;

class Location extends Model
{

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','description', 'summary', 'parsed_description', 'sort', 'image_extension', 'thumb_extension',
        'parent_id', 'type_id', 'is_active', 'display_style', 'is_character_home', 'is_user_home',

    ];


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'locations';

    public $timestamps = true;

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required|unique:locations|between:3,25',
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
        'name' => 'required|between:3,25',
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
    public function type()
    {
        return $this->belongsTo('App\Models\WorldExpansion\LocationType', 'type_id');
    }

    /**
     * Get the location attached to this location.
     */
    public function parent()
    {
        return $this->belongsTo('App\Models\WorldExpansion\Location', 'parent_id')->visible();
    }

    /**
     * Get the location attached to this location.
     */
    public function children()
    {
        return $this->hasMany('App\Models\WorldExpansion\Location', 'parent_id')->visible();
    }

    /**
     * Get the location attached to this location.
     */
    public function gallerysubmissions()
    {
        return $this->hasMany('App\Models\Gallery\GallerySubmission', 'location_id')->visible();
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
        return 'images/data/locations';
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
        return url('world/locations/'.$this->id);
    }

    /**
     * Gets the list of location display styles.
     *
     * @return string
     */
    public function getDisplayStylesAttribute()
    {
        return
            array(
                0 => $this->name,
                1 => 'the '.$this->type->name.' of '.$this->name,
                2 => $this->type->name.' of '.$this->name,
                3 => $this->name.' '.$this->type->name,
                4 => $this->type->name.' '.$this->name,
            );
    }

    /**
     * Gets the display style of this particular location.
     *
     * @return string
     */
    public function getStyleAttribute()
    {
        return $this->displayStyles[$this->display_style];
    }

    /**
     * Gets the display style of this particular location.
     *
     * @return string
     */
    public function getStyleParentAttribute()
    {
        if($this->parent_id) return $this->style . ' (' . $this->parent->style . ')';
        else return $this->style;
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
    public function scopeSortLocationType($query)
    {
        $ids = LocationType::orderBy('sort', 'DESC')->pluck('id')->toArray();
        return count($ids) ? $query->orderByRaw(DB::raw('FIELD(type_id, '.implode(',', $ids).')')) : $query;
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


    public static function getLocationsByType()
    {
        $sorted_location_types = collect(LocationType::all()->sortBy('name')->pluck('name')->toArray());
        $grouped = self::select('name', 'id', 'type_id')->with('type')->orderBy('name')->get()->keyBy('id')->groupBy('type.name', $preserveKeys = true)->toArray();
        if (isset($grouped[''])) {
            if (!$sorted_location_types->contains('Miscellaneous')) {
                $sorted_location_types->push('Miscellaneous');
            }
            $grouped['Miscellaneous'] = $grouped['Miscellaneous'] ?? [] + $grouped[''];
        }
        $sorted_location_types = $sorted_location_types->filter(function ($value, $key) use ($grouped) {
            return in_array($value, array_keys($grouped), true);
        });
        foreach ($grouped as $type => $locations) {
            foreach ($locations as $id => $location) {
                $grouped[$type][$id] = $location['name'];
            }
        }
        $locations_by_type = $sorted_location_types->map(function ($type) use ($grouped) {
            return $grouped;
        });
        unset($grouped['']);
        ksort($grouped);

        return $grouped;
    }


}
