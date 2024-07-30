<?php

namespace App\Models\WorldExpansion;

use DB;
use Auth;
use Config;
use Settings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User\User;
use App\Models\Character\Character;
use App\Models\WorldExpansion\FactionType;

class Faction extends Model
{

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','description', 'summary', 'parsed_description', 'sort', 'image_extension', 'thumb_extension',
        'parent_id', 'type_id', 'is_active', 'display_style', 'is_character_faction', 'is_user_faction',

    ];


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'factions';

    public $timestamps = true;

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required|unique:factions|between:3,25',
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
     * Get the type attached to this faction.
     */
    public function type()
    {
        return $this->belongsTo('App\Models\WorldExpansion\FactionType', 'type_id');
    }

    /**
     * Get parents of this faction.
     */
    public function parent()
    {
        return $this->belongsTo('App\Models\WorldExpansion\Faction', 'parent_id')->visible();
    }

    /**
     * Get children of this faction.
     */
    public function children()
    {
        return $this->hasMany('App\Models\WorldExpansion\Faction', 'parent_id')->visible();
    }

    /**
     * Get the member figures associated with this faction.
     */
    public function members()
    {
        return $this->hasMany('App\Models\WorldExpansion\Figure', 'faction_id')->visible();
    }

    /**
     * Get the ranks associated with this faction.
     */
    public function ranks()
    {
        return $this->hasMany('App\Models\WorldExpansion\FactionRank', 'faction_id');
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

    /**
     * Scope a query to sort items in category order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortFactionType($query)
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

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Displays the faction's name, linked to its page.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        if($this->is_active) {return '<a href="'.$this->url.'" class="display-location">'.$this->name.'</a>';}
        else {return '<s><a href="'.$this->url.'" class="display-location text-muted">'.$this->name.'</a></s>';}
    }

    /**
     * Displays the faction's name, linked to its page.
     *
     * @return string
     */
    public function getFullDisplayNameAttribute()
    {
        if($this->is_active) {return '<a href="'.$this->url.'" class="display-location">'.$this->style.'</a>';}
        else {return '<s><a href="'.$this->url.'" class="display-location text-muted">'.$this->style.'</a></s>';}
    }


    /**
     * Displays the faction's name, linked to its page.
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
        return 'images/data/factions';
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
        return url('world/factions/'.$this->id);
    }

    /**
     * Gets the list of faction display styles.
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
            );
    }

    /**
     * Gets the display style of this particular faction.
     *
     * @return string
     */
    public function getStyleAttribute()
    {
        return $this->displayStyles[$this->display_style];
    }

    /**
     * Gets the list of member users and/or characters.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFactionMembersAttribute()
    {
        $figures = $this->members()->get();
        $users = Settings::get('WE_user_factions') > 0 && $this->is_user_faction ? User::visible()->where('faction_id', $this->id)->get() : null;
        $characters = Settings::get('WE_character_factions') > 0 && $this->is_character_faction ? Character::visible()->where('faction_id', $this->id)->get() : null;

        if($users && $characters) return $users->concat($characters);
        elseif($users || $characters) {
            if(!$users) return $characters;
            elseif(!$characters) return $users;
        }
        else return collect();
    }

    public static function getFactionsByType()
    {
        $sorted_faction_types = collect(FactionType::all()->sortBy('name')->pluck('name')->toArray());
        $grouped = self::select('name', 'id', 'type_id')->with('type')->orderBy('name')->get()->keyBy('id')->groupBy('type.name', $preserveKeys = true)->toArray();
        if (isset($grouped[''])) {
            if (!$sorted_faction_types->contains('Miscellaneous')) {
                $sorted_faction_types->push('Miscellaneous');
            }
            $grouped['Miscellaneous'] = $grouped['Miscellaneous'] ?? [] + $grouped[''];
        }
        $sorted_faction_types = $sorted_faction_types->filter(function ($value, $key) use ($grouped) {
            return in_array($value, array_keys($grouped), true);
        });
        foreach ($grouped as $type => $factions) {
            foreach ($factions as $id => $faction) {
                $grouped[$type][$id] = $faction['name'];
            }
        }
        $factions_by_type = $sorted_faction_types->map(function ($type) use ($grouped) {
            return $grouped;
        });
        unset($grouped['']);
        ksort($grouped);

        return $grouped;
    }

}
