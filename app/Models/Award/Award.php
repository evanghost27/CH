<?php

namespace App\Models\Award;

use Config;
use DB;
use App\Models\Model;
use App\Models\Award\AwardCategory;
use App\Models\Character\CharacterAward;
use App\Models\Shop\Shop;
use App\Models\Prompt\Prompt;
use App\Models\User\User;
use App\Models\User\UserAward;

class Award extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'award_category_id', 'name', 'has_image', 'description', 'parsed_description',
        'data', 'is_released', 'is_featured', 'is_user_owned', 'is_character_owned',
        'user_limit', 'character_limit', 'allow_transfer', 'extension', 'allow_reclaim'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'awards';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'credits' => 'array'
    ];

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'award_category_id' => 'nullable',
        'name' => 'required|unique:awards|between:3,100',
        'description' => 'nullable',
        'image' => 'mimes:png,jpeg,jpg,gif',
        'rarity' => 'nullable',
        'uses' => 'nullable|between:3,250',
        'release' => 'nullable|between:3,100'
    ];

    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'award_category_id' => 'nullable',
        'name' => 'required|between:3,100',
        'description' => 'nullable',
        'image' => 'mimes:png,jpeg,jpg,gif',
        'uses' => 'nullable|between:3,250',
        'release' => 'nullable|between:3,100'
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the category the award belongs to.
     */
    public function category()
    {
        return $this->belongsTo('App\Models\Award\AwardCategory', 'award_category_id');
    }

    /**
     * Gets the awards progressions.
     */
    public function progressions()
    {
        return $this->hasMany('App\Models\Award\AwardProgression', 'award_id');
    }

    /**
     * Gets the awards rewards.
     */
    public function rewards()
    {
        return $this->hasMany('App\Models\Award\AwardReward', 'award_id');
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to sort awards in alphabetical order.
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
     * Scope a query to sort awards in category order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortCategory($query)
    {
        if(AwardCategory::all()->count()) return $query->orderBy(AwardCategory::select('sort')->whereColumn('awards.award_category_id', 'award_categories.id'), 'DESC');
        return $query;
    }

    /**
     * Scope a query to sort awards by newest first.
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

    /**
     * Scope a query to show only released or "released" (at least one user-owned stack has ever existed) items.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReleased($query)
    {
        $users = UserAward::pluck('award_id')->toArray();
        $characters = CharacterAward::pluck('award_id')->toArray();
        $array = array_merge($users, $characters);
        return $query->whereIn('id', $array)->orWhere('is_released', 1);
    }


    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Displays the model's name, linked to its encyclopedia page.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->idUrl.'" class="display-award">'.$this->name.'</a>';
    }

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute()
    {
        return 'images/data/awards';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getImageFileNameAttribute()
    {
        return $this->id . '-image.' . $this->extension;
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
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if (!$this->has_image) return null;
        return asset($this->imageDirectory . '/' . $this->imageFileName);
    }

    /**
     * Gets the URL of the model's encyclopedia page.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('world/'.__('awards.awards').'?name='.$this->name);
    }

    /**
     * Gets the URL of the individual award's page, by ID.
     *
     * @return string
     */
    public function getIdUrlAttribute()
    {
        return url('world/'.__('awards.awards').'/'.$this->id);
    }

    /**
     * Gets the currency's asset type for asset management.
     *
     * @return string
     */
    public function getAssetTypeAttribute()
    {
        return 'awards';
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

    public function getCreditsAttribute(){
        return $this->data['credits'];
    }
    public function getPrettyCreditsAttribute(){

        $creds = [];
        $credits = [];

        foreach($this->credits as $credit){
            $text = isset($credit['name']) ? $credit['name'] :  (isset($credit['id']) ? User::find($credit['id'])->name : (isset($credit['url']) ? $credit['url'] : 'artist'));
            $link = isset($credit['url']) ? $credit['url'] :  (isset($credit['id']) ? User::find($credit['id'])->url : '#');
            $role = isset($credit['role']) ? '<small>('.$credit['role'].')</small>' : null;
            $credits[] = '<a href="'.$link.'" target="_blank">'.$text.'</a> '. $role;
        }
        return $credits;
    }

    /**
     * Get the rarity attribute.
     *
     * @return string
     */
    public function getRarityAttribute()
    {
        if (!$this->data) return null;
        return $this->data['rarity'];
    }

    /**
     * Get the source attribute.
     *
     * @return string
     */
    public function getSourceAttribute()
    {
        if (!$this->data) return null;
        return $this->data['release'];
    }

    /**
     * Get the prompts attribute as an associative array.
     *
     * @return array
     */
    public function getPromptsAttribute()
    {
        if (!$this->data) return null;
        $awardPrompts = $this->data['prompts'];
        return Prompt::whereIn('id', $awardPrompts)->get();
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/

    /**
     * Check if user can claim this award
     */
    public function canClaim($user)
    {
        if($user->awards()->where('award_id', $this->id)->count() && !$this->allow_reclaim) return false;
        return true;
    }

    /**
     * Gets how many progressions are completed by a user
     */
    public function progressionProgress($user = null)
    {
        if(!$user) return 0;
        $progressionSum = 0;
        foreach($this->progressions as $progression) {
            if($progression->isUnlocked($user)) $progressionSum += 1;
        }
        return $progressionSum;
    }
}
