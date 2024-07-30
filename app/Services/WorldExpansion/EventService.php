<?php namespace App\Services\WorldExpansion;

use App\Services\Service;

use DB;
use Config;
use Settings;
use Auth;
use Notifications;

use App\Models\News;
use App\Models\Item\Item;
use App\Models\Prompt\Prompt;

use App\Models\WorldExpansion\Event;
use App\Models\WorldExpansion\EventCategory;
use App\Models\WorldExpansion\EventFigure;
use App\Models\WorldExpansion\EventLocation;
use App\Models\WorldExpansion\EventFaction;
use App\Models\WorldExpansion\EventNews;
use App\Models\WorldExpansion\EventPrompt;

use App\Models\WorldExpansion\Figure;
use App\Models\WorldExpansion\FigureItem;
use App\Models\WorldExpansion\FigureCategory;

use App\Models\WorldExpansion\LocationType;
use App\Models\WorldExpansion\Location;
use App\Models\WorldExpansion\FactionType;
use App\Models\WorldExpansion\Faction;
use App\Services\WorldExpansion\WorldExpansionService;

class EventService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Event Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of natural things like figure and event.
    |
    */


    /**
     * Creates a new event category.
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Event\Category
     */
    public function createEventCategory($data, $user)
    {

        DB::beginTransaction();

        try {

            $data = $this->populateEventCategoryData($data);

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $image = $data['image'];
                unset($data['image']);
            }
            $image_th = null;
            if(isset($data['image_th']) && $data['image_th']) {
                $image_th = $data['image_th'];
                unset($data['image_th']);
            }

            $category = EventCategory::create($data);

            if ($image) {
                $category->image_extension = $image->getClientOriginalExtension();
                $category->update();
                $this->handleImage($image, $category->imagePath, $category->imageFileName, null);
            }
            if ($image_th) {
                $category->thumb_extension = $image_th->getClientOriginalExtension();
                $category->update();
                $this->handleImage($image_th, $category->imagePath, $category->thumbFileName, null);
            }

            return $this->commitReturn($category);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a category.
     *
     * @param  \App\Models\Category\Category  $category
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Category\Category
     */
    public function updateEventCategory($category, $data, $user)
    {
        DB::beginTransaction();

        try {
            // More specific validation
            if(EventCategory::where('name', $data['name'])->where('id', '!=', $category->id)->exists()) throw new \Exception("The name has already been taken.");

            $data = $this->populateEventCategoryData($data, $category);

            $image = null;
            if(isset($data['image']) && $data['image']) {
                if(isset($category->image_extension)) $old = $category->imageFileName;
                else $old = null;
                $image = $data['image'];
                unset($data['image']);
            }
            if ($image) {
                $category->image_extension = $image->getClientOriginalExtension();
                $category->update();
                $this->handleImage($image, $category->imagePath, $category->imageFileName, $old);
            }

            $image_th = null;
            if(isset($data['image_th']) && $data['image_th']) {
                if(isset($category->thumb_extension)) $old_th = $category->thumbFileName;
                else $old_th = null;
                $image_th = $data['image_th'];
                unset($data['image_th']);
            }

            if ($image_th) {
                $category->thumb_extension = $image_th->getClientOriginalExtension();
                $category->update();
                $this->handleImage($image_th, $category->imagePath, $category->thumbFileName, $old_th);
            }
            $category->update($data);

            return $this->commitReturn($category);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }



    /**
     * Deletes a category.
     *
     * @param  \App\Models\Category\Category  $category
     * @return bool
     */
    public function deleteEventCategory($category)
    {
        DB::beginTransaction();

        try {
            if(isset($category->image_extension)) $this->deleteImage($category->imagePath, $category->imageFileName);
            if(isset($category->thumb_extension)) $this->deleteImage($category->imagePath, $category->thumbFileName);

            if(count($category->events)){
                foreach($category->events as $event){
                    if(isset($event->image_extension)) $this->deleteImage($event->imagePath, $event->imageFileName);
                    if(isset($event->thumb_extension)) $this->deleteImage($event->imagePath, $event->thumbFileName);
                }
            }

            $category->delete();
            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Sorts category order.
     *
     * @param  array  $data
     * @return bool
     */
    public function sortEventCategory($data)
    {
        DB::beginTransaction();

        try {
            // explode the sort array and reverse it since the order is inverted
            $sort = array_reverse(explode(',', $data));

            foreach($sort as $key => $s) {
                EventCategory::where('id', $s)->update(['sort' => $key]);
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Processes user input for creating/updating a category.
     *
     * @param  array                  $data
     * @param  \App\Models\Category\Category  $category
     * @return array
     */
    private function populateEventCategoryData($data, $category = null)
    {
        if(isset($data['description']) && $data['description']) $data['parsed_description'] = parse($data['description']);
        if(isset($data['name']) && $data['name']) $data['name'] = parse($data['name']);

        if(isset($data['remove_image']))
        {
            if($category && isset($category->image_extension) && $data['remove_image'])
            {
                $data['image_extension'] = null;
                $this->deleteImage($category->imagePath, $category->imageFileName);
            }
            unset($data['remove_image']);
        }

        if(isset($data['remove_image_th']) && $data['remove_image_th'])
        {
            if($category && isset($category->thumb_extension) && $data['remove_image_th'])
            {
                $data['thumb_extension'] = null;
                $this->deleteImage($category->imagePath, $category->thumbFileName);
            }
            unset($data['remove_image_th']);
        }


        return $data;
    }



    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    |
    */



    /**
     * Creates a new event.
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Event\Category
     */
    public function createEvent($data, $user)
    {

        DB::beginTransaction();

        try {
            $data = $this->populateEventData($data);

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $image = $data['image'];
                unset($data['image']);
            }
            $image_th = null;
            if(isset($data['image_th']) && $data['image_th']) {
                $image_th = $data['image_th'];
                unset($data['image_th']);
            }

            $event = Event::create($data);

            if ($image) {
                $event->image_extension = $image->getClientOriginalExtension();
                $event->update();
                $this->handleImage($image, $event->imagePath, $event->imageFileName, null);
            }
            if ($image_th) {
                $event->thumb_extension = $image_th->getClientOriginalExtension();
                $event->update();
                $this->handleImage($image_th, $event->imagePath, $event->thumbFileName, null);
            }

            return $this->commitReturn($event);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a event.
     *
     * @param  \App\Models\WorldExpansion\Event  $event
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\WorldExpansion\Event
     */
    public function updateEvent($event, $data, $user)
    {

        DB::beginTransaction();

        try {
            // More specific validation
            if(Event::where('name', $data['name'])->where('id', '!=', $event->id)->exists()) throw new \Exception("The name has already been taken.");

            (new WorldExpansionService)->updateAttachments($event, $data);

            $data = $this->populateEventData($data, $event);

            // Image processing
            $image = null;
            if(isset($data['image']) && $data['image']) {
                if(isset($event->image_extension)) $old = $event->imageFileName;
                else $old = null;
                $image = $data['image'];
                unset($data['image']);
            }
            if ($image) {
                $event->image_extension = $image->getClientOriginalExtension();
                $event->update();
                $this->handleImage($image, $event->imagePath, $event->imageFileName, $old);
            }

            // Image Thumbnail Processing
            $image_th = null;
            if(isset($data['image_th']) && $data['image_th']) {
                if(isset($event->thumb_extension)) $old_th = $event->thumbFileName;
                else $old_th = null;
                $image_th = $data['image_th'];
                unset($data['image_th']);
            }

            if ($image_th) {
                $event->thumb_extension = $image_th->getClientOriginalExtension();
                $event->update();
                $this->handleImage($image_th, $event->imagePath, $event->thumbFileName, $old_th);
            }

            $event->update($data);

            return $this->commitReturn($event);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }


    /**
     * Deletes a event.
     *
     * @param  \App\Models\WorldExpansion\Event  $event
     * @return bool
     */
    public function deleteEvent($event)
    {
        DB::beginTransaction();

        try {
            if(isset($event->image_extension)) $this->deleteImage($event->imagePath, $event->imageFileName);
            if(isset($event->thumb_extension)) $this->deleteImage($event->imagePath, $event->thumbFileName);
            $event->delete();
            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Processes user input for creating/updating a event.
     *
     * @param  array                  $data
     * @param  \App\Models\WorldExpansion\Event  $event
     * @return array
     */
    private function populateEventData($data, $event = null)
    {

        $saveData['description'] = isset($data['description']) ? $data['description'] : null;
        if(isset($data['description']) && $data['description']) $saveData['parsed_description'] = parse($data['description']);
        $saveData['summary'] = isset($data['summary']) ? $data['summary'] : null;

        if(isset($data['name']) && $data['name']) $saveData['name'] = parse($data['name']);
        $saveData['is_active'] = isset($data['is_active']);
        $saveData['category_id'] = isset($data['category_id']) && $data['category_id'] ? $data['category_id'] : null;

        $saveData['image'] = isset($data['image']) ? $data['image'] : null;
        $saveData['image_th'] = isset($data['image_th']) ? $data['image_th'] : null;

        $saveData['occur_start'] = isset($data['occur_start']) ? $data['occur_start'] : null;
        $saveData['occur_end'] = isset($data['occur_end']) ? $data['occur_end'] : null;


        if(isset($data['remove_image']))
        {
            if($event && isset($event->image_extension) && $data['remove_image'])
            {
                $saveData['image_extension'] = null;
                $this->deleteImage($event->imagePath, $event->imageFileName);
            }
            unset($data['remove_image']);
        }

        if(isset($data['remove_image_th']) && $data['remove_image_th'])
        {
            if($event && isset($event->thumb_extension) && $data['remove_image_th'])
            {
                $saveData['thumb_extension'] = null;
                $this->deleteImage($event->imagePath, $event->thumbFileName);
            }
            unset($data['remove_image_th']);
        }

        return $saveData;
    }


    /**
     * Sorts category order.
     *
     * @param  array  $data
     * @return bool
     */
    public function sortEvent($data)
    {
        DB::beginTransaction();

        try {
            // explode the sort array and reverse it since the order is inverted
            $sort = array_reverse(explode(',', $data));

            foreach($sort as $key => $s) {
                Event::where('id', $s)->update(['sort' => $key]);
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }




    /**
     * Creates a new figure category.
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Figure\Category
     */
    public function createFigureCategory($data, $user)
    {

        DB::beginTransaction();

        try {

            $data = $this->populateFigureCategoryData($data);

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $image = $data['image'];
                unset($data['image']);
            }
            $image_th = null;
            if(isset($data['image_th']) && $data['image_th']) {
                $image_th = $data['image_th'];
                unset($data['image_th']);
            }

            $category = FigureCategory::create($data);

            if ($image) {
                $category->image_extension = $image->getClientOriginalExtension();
                $category->update();
                $this->handleImage($image, $category->imagePath, $category->imageFileName, null);
            }
            if ($image_th) {
                $category->thumb_extension = $image_th->getClientOriginalExtension();
                $category->update();
                $this->handleImage($image_th, $category->imagePath, $category->thumbFileName, null);
            }

            return $this->commitReturn($category);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a category.
     *
     * @param  \App\Models\Category\Category  $category
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Category\Category
     */
    public function updateFigureCategory($category, $data, $user)
    {
        DB::beginTransaction();

        try {
            // More specific validation
            if(FigureCategory::where('name', $data['name'])->where('id', '!=', $category->id)->exists()) throw new \Exception("The name has already been taken.");

            $data = $this->populateFigureCategoryData($data, $category);

            $image = null;
            if(isset($data['image']) && $data['image']) {
                if(isset($category->image_extension)) $old = $category->imageFileName;
                else $old = null;
                $image = $data['image'];
                unset($data['image']);
            }
            if ($image) {
                $category->image_extension = $image->getClientOriginalExtension();
                $category->update();
                $this->handleImage($image, $category->imagePath, $category->imageFileName, $old);
            }

            $image_th = null;
            if(isset($data['image_th']) && $data['image_th']) {
                if(isset($category->thumb_extension)) $old_th = $category->thumbFileName;
                else $old_th = null;
                $image_th = $data['image_th'];
                unset($data['image_th']);
            }

            if ($image_th) {
                $category->thumb_extension = $image_th->getClientOriginalExtension();
                $category->update();
                $this->handleImage($image_th, $category->imagePath, $category->thumbFileName, $old_th);
            }
            $category->update($data);

            return $this->commitReturn($category);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }



    /**
     * Deletes a category.
     *
     * @param  \App\Models\Category\Category  $category
     * @return bool
     */
    public function deleteFigureCategory($category)
    {
        DB::beginTransaction();

        try {
            if(isset($category->image_extension)) $this->deleteImage($category->imagePath, $category->imageFileName);
            if(isset($category->thumb_extension)) $this->deleteImage($category->imagePath, $category->thumbFileName);
            if(count($category->figures)){
                foreach($category->figures as $figure){
                    if(isset($figure->image_extension)) $this->deleteImage($figure->imagePath, $figure->imageFileName);
                    if(isset($figure->thumb_extension)) $this->deleteImage($figure->imagePath, $figure->thumbFileName);
                }
            }
            $category->delete();
            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Sorts category order.
     *
     * @param  array  $data
     * @return bool
     */
    public function sortFigureCategory($data)
    {
        DB::beginTransaction();

        try {
            // explode the sort array and reverse it since the order is inverted
            $sort = array_reverse(explode(',', $data));

            foreach($sort as $key => $s) {
                FigureCategory::where('id', $s)->update(['sort' => $key]);
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Processes user input for creating/updating a category.
     *
     * @param  array                  $data
     * @param  \App\Models\Category\Category  $category
     * @return array
     */
    private function populateFigureCategoryData($data, $category = null)
    {
        if(isset($data['description']) && $data['description']) $data['parsed_description'] = parse($data['description']);
        if(isset($data['name']) && $data['name']) $data['name'] = parse($data['name']);

        if(isset($data['remove_image']))
        {
            if($category && isset($category->image_extension) && $data['remove_image'])
            {
                $data['image_extension'] = null;
                $this->deleteImage($category->imagePath, $category->imageFileName);
            }
            unset($data['remove_image']);
        }

        if(isset($data['remove_image_th']) && $data['remove_image_th'])
        {
            if($category && isset($category->thumb_extension) && $data['remove_image_th'])
            {
                $data['thumb_extension'] = null;
                $this->deleteImage($category->imagePath, $category->thumbFileName);
            }
            unset($data['remove_image_th']);
        }


        return $data;
    }



    /*
    |--------------------------------------------------------------------------
    | Figures
    |--------------------------------------------------------------------------
    |
    */



    /**
     * Creates a new figure.
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Figure\Category
     */
    public function createFigure($data, $user)
    {

        DB::beginTransaction();

        try {

            $data = $this->populateFigureData($data);

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $image = $data['image'];
                unset($data['image']);
            }
            $image_th = null;
            if(isset($data['image_th']) && $data['image_th']) {
                $image_th = $data['image_th'];
                unset($data['image_th']);
            }

            $figure = Figure::create($data);

            if ($image) {
                $figure->image_extension = $image->getClientOriginalExtension();
                $figure->update();
                $this->handleImage($image, $figure->imagePath, $figure->imageFileName, null);
            }
            if ($image_th) {
                $figure->thumb_extension = $image_th->getClientOriginalExtension();
                $figure->update();
                $this->handleImage($image_th, $figure->imagePath, $figure->thumbFileName, null);
            }

            return $this->commitReturn($figure);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a figure.
     *
     * @param  \App\Models\WorldExpansion\Figure  $figure
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\WorldExpansion\Figure
     */
    public function updateFigure($figure, $data, $user)
    {

        DB::beginTransaction();

        try {
            // More specific validation
            if(Figure::where('name', $data['name'])->where('id', '!=', $figure->id)->exists()) throw new \Exception("The name has already been taken.");

            $figure->timestamps = false;

            // Determine if there are items added.
            if(isset($data['item_id'])) {
                $data['item_id'] = array_unique($data['item_id']);
                $items = Item::whereIn('id', $data['item_id'])->get();
                if(count($items) != count($data['item_id'])) throw new \Exception("One or more of the selected items does not exist.");
            }
            else $items = [];

            // Remove all items from the figure so they can be reattached with new data
            FigureItem::where('figure_id',$figure->id)->delete();

            // Attach any items to the figure
            foreach($items as $key=>$item) {
                FigureItem::create([
                    'item_id' => $item->id,
                    'figure_id' => $figure->id,
                ]);
            }

            $figure->timestamps = true;

            $data = $this->populateFigureData($data, $figure);

            $image = null;
            if(isset($data['image']) && $data['image']) {
                if(isset($figure->image_extension)) $old = $figure->imageFileName;
                else $old = null;
                $image = $data['image'];
                unset($data['image']);
            }
            if ($image) {
                $figure->image_extension = $image->getClientOriginalExtension();
                $figure->update();
                $this->handleImage($image, $figure->imagePath, $figure->imageFileName, $old);
            }

            $image_th = null;
            if(isset($data['image_th']) && $data['image_th']) {
                if(isset($figure->thumb_extension)) $old_th = $figure->thumbFileName;
                else $old_th = null;
                $image_th = $data['image_th'];
                unset($data['image_th']);
            }

            if ($image_th) {
                $figure->thumb_extension = $image_th->getClientOriginalExtension();
                $figure->update();
                $this->handleImage($image_th, $figure->imagePath, $figure->thumbFileName, $old_th);
            }

            $figure->update($data);

            return $this->commitReturn($figure);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }


    /**
     * Deletes a figure.
     *
     * @param  \App\Models\WorldExpansion\Figure  $figure
     * @return bool
     */
    public function deleteFigure($figure)
    {
        DB::beginTransaction();

        try {
            if(isset($figure->image_extension)) $this->deleteImage($figure->imagePath, $figure->imageFileName);
            if(isset($figure->thumb_extension)) $this->deleteImage($figure->imagePath, $figure->thumbFileName);
            $figure->delete();
            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Processes user input for creating/updating a figure.
     *
     * @param  array                  $data
     * @param  \App\Models\WorldExpansion\Figure  $figure
     * @return array
     */
    private function populateFigureData($data, $figure = null)
    {

        $saveData['description'] = isset($data['description']) ? $data['description'] : null;
        if(isset($data['description']) && $data['description']) $saveData['parsed_description'] = parse($data['description']);
        $saveData['summary'] = isset($data['summary']) ? $data['summary'] : null;

        if(isset($data['name']) && $data['name']) $saveData['name'] = parse($data['name']);
        $saveData['is_active'] = isset($data['is_active']);
        $saveData['category_id'] = isset($data['category_id']) && $data['category_id'] ? $data['category_id'] : null;

        $saveData['image'] = isset($data['image']) ? $data['image'] : null;
        $saveData['image_th'] = isset($data['image_th']) ? $data['image_th'] : null;

        if(isset($data['remove_image']))
        {
            if($figure && isset($figure->image_extension) && $data['remove_image'])
            {
                $saveData['image_extension'] = null;
                $this->deleteImage($figure->imagePath, $figure->imageFileName);
            }
            unset($data['remove_image']);
        }

        if(isset($data['remove_image_th']) && $data['remove_image_th'])
        {
            if($figure && isset($figure->thumb_extension) && $data['remove_image_th'])
            {
                $saveData['thumb_extension'] = null;
                $this->deleteImage($figure->imagePath, $figure->thumbFileName);
            }
            unset($data['remove_image_th']);
        }

        return $saveData;
    }


    /**
     * Sorts category order.
     *
     * @param  array  $data
     * @return bool
     */
    public function sortFigure($data)
    {
        DB::beginTransaction();

        try {
            // explode the sort array and reverse it since the order is inverted
            $sort = array_reverse(explode(',', $data));

            foreach($sort as $key => $s) {
                Figure::where('id', $s)->update(['sort' => $key]);
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

}
