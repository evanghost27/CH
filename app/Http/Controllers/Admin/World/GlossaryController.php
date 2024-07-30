<?php

namespace App\Http\Controllers\Admin\World;

use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\WorldExpansion\Glossary;
use App\Services\WorldExpansion\EventService;
use App\Services\WorldExpansion\GlossaryService;

class GlossaryController extends Controller
{
  /**
     * Shows the glossary index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getGlossaryIndex()
    {

        return view('admin.world_expansion.glossary', [
            'glossaries' => Glossary::sortAlphabetical()->get()
        ]);
    }

    /**
     * Shows the edit event category page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateEditTerm($id = null)
    {
        $term = Glossary::find($id) ?? new Glossary;

        return view('admin.world_expansion.create_edit_term', [
            'term' => $term
        ]);
    }

    /**
     * Creates or edits a glossary term.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\WorldExpansion\EventService  $service
     * @param  int|null                  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditTerm(Request $request, GlossaryService $service, $id = null)
    {
        $id ? $request->validate(Glossary::$updateRules) : $request->validate(Glossary::$createRules);

        $data = $request->only([
            'name', 'description', 'parsed_description', 'is_active', 'attachment_type', 'attachment_id'
        ]);
        if($id && $service->updateTerm(Glossary::find($id), $data, Auth::user())) {
            flash('Glossary Term updated successfully.')->success();
        }
        else if (!$id && $term = $service->createTerm($data, Auth::user())) {
            flash('Glossary Term created successfully.')->success();
            return redirect()->to('admin/world/glossary/edit/'.$term->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Gets the category deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteTerm($id)
    {
        $term = Glossary::find($id);
        return view('admin.world_expansion._delete_term', [
            'term' => $term,
        ]);
    }

    /**
     * Deletes a category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\WorldExpansion\EventService  $service
     * @param  int                       $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteTerm(Request $request, GlossaryService $service, $id)
    {
        if($id && $service->deleteTerm(Glossary::find($id))) {
            flash('Term deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/world/glossary');
    }

    /**
     * Toggles the glossary visible or nonvisible for users
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\WorldExpansion\EventService  $service
     * @param  int                       $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postToggleSetting(GlossaryService $service)
    {
        if($service->toggleSetting()) {
            flash('Glossary toggled successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

}
