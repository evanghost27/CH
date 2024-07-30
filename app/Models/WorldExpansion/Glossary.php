<?php

namespace App\Models\WorldExpansion;

use Auth;
use Illuminate\Database\Eloquent\Model;

class Glossary extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','description', 'parsed_description', 'is_active', 'link_id', 'link_type'

    ];


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'glossary';

    public $timestamps = false;

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required|unique:glossary|between:3,191',
        'description' => 'nullable',
    ];

    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required|between:3,191',
        'description' => 'nullable',
    ];

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
        $displayName = !$this->is_active ? '<s>' : '';
        $displayName .= $this->url ? '<a href="'.$this->url.'" class="glossary">'.$this->name.'</a>' : $this->name;
        $displayName .= !$this->is_active ? '</s>' : '';
        
        return $displayName;
    }

    /**
     * Gets the URL of the model's encyclopedia page.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return isset($this->link) ? $this->link->url : null;
    }

    /**
     * Gets the display style of this particular location.
     *
     * @return string
     */
    public function getLinkAttribute()
    {
        $link = 'App\Models\WorldExpansion\\'.$this->link_type;
        return isset($this->link_type) ? $link::find($this->link_id) : null;
    }


}
