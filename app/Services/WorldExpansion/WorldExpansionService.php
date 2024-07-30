<?php namespace App\Services\WorldExpansion;

use App\Services\Service;

use DB;
use Config;
use Settings;
use Auth;
use Notifications;

use App\Models\Item\Item;

use App\Models\WorldExpansion\Fauna;
use App\Models\WorldExpansion\FaunaCategory;
use App\Models\WorldExpansion\FaunaItem;
use App\Models\WorldExpansion\FaunaLocation;

use App\Models\WorldExpansion\Flora;
use App\Models\WorldExpansion\FloraCategory;
use App\Models\WorldExpansion\FloraItem;
use App\Models\WorldExpansion\FloraLocation;

use App\Models\WorldExpansion\LocationType;
use App\Models\WorldExpansion\Location;

use App\Models\WorldExpansion\WorldAttachment;

class WorldExpansionService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | WorldExpansion Service
    |--------------------------------------------------------------------------
    |
    | Handles the various functions used by multiple world expansion models.
    |
    */


    /**
     * Creates a new fauna category.
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Fauna\Category
     */
    public function updateAttachments($model, $data)
    {
        // Determine if there are attachments added.
        $attachments = [];
        if(isset($data['attachment_id'])) {
            foreach($data['attachment_id'] as $key => $attachment_id){
                if(!isset($data['attachment_type'][$key])) continue;
                switch ($data['attachment_type'][$key])
                {
                    case 'Item':        $attach = \App\Models\Item\Item::find((int)$attachment_id); break;
                    case 'News':        $attach = \App\Models\News::find((int)$attachment_id); break;
                    case 'Prompt':      $attach = \App\Models\Prompt\Prompt::find((int)$attachment_id); break;

                    case 'Figure':      $attach = \App\Models\WorldExpansion\Figure::find((int)$attachment_id); break;
                    case 'Fauna':       $attach = \App\Models\WorldExpansion\Fauna::find((int)$attachment_id); break;
                    case 'Flora':       $attach = \App\Models\WorldExpansion\Flora::find((int)$attachment_id); break;
                    case 'Faction':     $attach = \App\Models\WorldExpansion\Faction::find((int)$attachment_id); break;
                    case 'Concept':     $attach = \App\Models\WorldExpansion\Concept::find((int)$attachment_id); break;
                    case 'Event':       $attach = \App\Models\WorldExpansion\Event::find((int)$attachment_id); break;
                    case 'Location':    $attach = \App\Models\WorldExpansion\Location::find((int)$attachment_id); break;

                    default:            $attach = null;
                }
                if(!$attach) continue;  // Quietly ignore
                $attachments[] = [
                    'attachment'    => $attach,
                    'type'          => $data['attachment_type'][$key],
                    'data'          => isset($data['attachment_data'][$key]) ? $data['attachment_data'][$key] : null,
                ];
            }
        }

        // Remove all attachments from the model so they can be reattached with new data
        WorldAttachment::where('attacher_type',class_basename($model))->where('attacher_id',$model->id)->delete();

        // Attach any attachments to the model
        foreach($attachments as $attachment) {
            WorldAttachment::create([
                'attacher_id'       => $model->id,
                'attacher_type'     => class_basename($model),
                'attachment_id'     => $attachment['attachment']->id,
                'attachment_type'   => $attachment['type'],
                'data'              => $attachment['data'],
            ]);
        }
    }

}
