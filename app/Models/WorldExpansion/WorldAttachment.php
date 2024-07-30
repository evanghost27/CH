<?php

namespace App\Models\WorldExpansion;

use Illuminate\Database\Eloquent\Model;

use App\Models\WorldExpansion\Figure;
use App\Models\WorldExpansion\Fauna;
use App\Models\WorldExpansion\Flora;
use App\Models\WorldExpansion\Faction;
use App\Models\WorldExpansion\Location;
use App\Models\WorldExpansion\Concept;
use App\Models\Item\Item;

class WorldAttachment extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'attacher_id', 'attacher_type', 'attachment_id', 'attachment_type', 'data'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'world_attachments';

    public $timestamps = false;

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the attachments.
     */
    public function attachment()
    {
        switch ($this->attachment_type)
        {
            case 'Figure':
                return $this->belongsTo('App\Models\WorldExpansion\Figure', 'attachment_id');
            case 'Fauna':
                return $this->belongsTo('App\Models\WorldExpansion\Fauna', 'attachment_id');
            case 'Flora':
                return $this->belongsTo('App\Models\WorldExpansion\Flora', 'attachment_id');
            case 'Faction':
                return $this->belongsTo('App\Models\WorldExpansion\Faction', 'attachment_id');
            case 'Concept':
                return $this->belongsTo('App\Models\WorldExpansion\Concept', 'attachment_id');
            case 'Location':
                return $this->belongsTo('App\Models\WorldExpansion\Location', 'attachment_id');
            case 'Event':
                return $this->belongsTo('App\Models\WorldExpansion\Event', 'attachment_id');
            case 'Item':
                return $this->belongsTo('App\Models\Item\Item', 'attachment_id');
            case 'Prompt':
                return $this->belongsTo('App\Models\Prompt\Prompt', 'attachment_id');
            case 'News':
                return $this->belongsTo('App\Models\News', 'attachment_id');
            case 'None':
                // Laravel requires a relationship instance to be returned (cannot return null), so returning one that doesn't exist here.
                return $this->belongsTo('App\Models\Loot\Loot', 'attachment_id', 'loot_table_id')->whereNull('loot_table_id');
        }
        return null;
    }
    /**
     * Get the attachers.
     */
    public function attacher()
    {
        switch ($this->attacher_type)
        {
            case 'Figure':
                return $this->belongsTo('App\Models\WorldExpansion\Figure', 'attacher_id');
            case 'Fauna':
                return $this->belongsTo('App\Models\WorldExpansion\Fauna', 'attacher_id');
            case 'Flora':
                return $this->belongsTo('App\Models\WorldExpansion\Flora', 'attacher_id');
            case 'Faction':
                return $this->belongsTo('App\Models\WorldExpansion\Faction', 'attacher_id');
            case 'Concept':
                return $this->belongsTo('App\Models\WorldExpansion\Concept', 'attacher_id');
            case 'Location':
                return $this->belongsTo('App\Models\WorldExpansion\Location', 'attacher_id');
            case 'Event':
                return $this->belongsTo('App\Models\WorldExpansion\Event', 'attacher_id');
            case 'Item':
                return $this->belongsTo('App\Models\Item\Item', 'attacher_id');
            case 'Prompt':
                return $this->belongsTo('App\Models\Prompt\Prompt', 'attacher_id');
            case 'News':
                return $this->belongsTo('App\Models\News', 'attacher_id');
            case 'None':
                // Laravel requires a relationship instance to be returned (cannot return null), so returning one that doesn't exist here.
                return $this->belongsTo('App\Models\Loot\Loot', 'attacher_id', 'loot_table_id')->whereNull('loot_table_id');
        }
        return null;
    }

    public function figures() {
        return $this->attachments()->where('attachment_type','Figure');
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/


}
