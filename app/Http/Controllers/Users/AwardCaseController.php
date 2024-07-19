<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;

use DB;
use Auth;
use App\Models\User\User;
use App\Models\Character\Character;
use App\Models\User\UserAward;
use App\Models\Award\Award;
use App\Models\Award\AwardCategory;
use App\Models\Award\AwardLog;
use App\Models\Character\CharacterAward;
use App\Services\AwardCaseManager;

use App\Http\Controllers\Controller;

class AwardCaseController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | AwardCase Controller
    |--------------------------------------------------------------------------
    |
    | Handles awardcase management for the user.
    |
    */

    /**
     * Shows the user's awardcase page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        $categories = AwardCategory::orderBy('sort', 'DESC')->get();
        $awards = count($categories) ?
            Auth::user()->awards()
                ->where('count', '>', 0)
                ->orderByRaw('FIELD(award_category_id,'.implode(',', $categories->pluck('id')->toArray()).')')
                ->orderBy('name')
                ->orderBy('updated_at')
                ->get()
                ->groupBy(['award_category_id', 'id']) :
            Auth::user()->awards()
                ->where('count', '>', 0)
                ->orderBy('name')
                ->orderBy('updated_at')
                ->get()
                ->groupBy(['award_category_id', 'id']);
        return view('home.awardcase', [
            'categories' => $categories->keyBy('id'),
            'awards' => $awards,
            'userOptions' => User::visible()->where('id', '!=', Auth::user()->id)->orderBy('name')->pluck('name', 'id')->toArray(),
            'user' => Auth::user()
        ]);
    }

    /**
     * Shows the awardcase stack modal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int                       $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getStack(Request $request, $id)
    {
        $first_instance = UserAward::withTrashed()->where('id', $id)->first();
        $readOnly = $request->get('read_only') ? : ((Auth::check() && $first_instance && ($first_instance->user_id == Auth::user()->id || Auth::user()->hasPower('edit_inventories'))) ? 0 : 1);
        $stack = UserAward::where([['user_id', $first_instance->user_id], ['award_id', $first_instance->award_id], ['count', '>', 0]])->get();
        $award = Award::where('id', $first_instance->award_id)->first();

        return view('home._awardcase_stack', [
            'stack' => $stack,
            'award' => $award,
            'user' => Auth::user(),
            'userOptions' => ['' => 'Select User'] + User::visible()->where('id', '!=', $first_instance ? $first_instance->user_id : 0)->orderBy('name')->get()->pluck('verified_name', 'id')->toArray(),
            'characterOptions' => ['' => 'Select Character'] + Character::visible()->myo(0)->where('user_id', optional(Auth::user())->id)->orderBy('sort','DESC')->get()->pluck('fullName','id')->toArray(),
            'readOnly' => $readOnly
        ]);
    }

    /**
     * Shows the awardcase stack modal, for characters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int                       $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterStack(Request $request, $id)
    {
        $first_instance = CharacterAward::withTrashed()->where('id', $id)->first();
        $stack = CharacterAward::where([['character_id', $first_instance->character_id], ['award_id', $first_instance->award_id], ['count', '>', 0]])->get();
        $award = Award::where('id', $first_instance->award_id)->first();

        $character = $first_instance->character;
        isset($stack->first()->character->user_id) ?
        $ownerId = $stack->first()->character->user_id : null;

        $hasPower = Auth::user()->hasPower('edit_inventories');
        $readOnly = $request->get('read_only') ? : ((Auth::check() && $first_instance && (isset($ownerId) == TRUE || $hasPower == TRUE)) ? 0 : 1);

        return view('character._award_stack', [
            'stack' => $stack,
            'award' => $award,
            'user' => Auth::user(),
            'has_power' => $hasPower,
            'readOnly' => $readOnly,
            'character' => $character,
            'owner_id' => isset($ownerId) ? $ownerId : null,
        ]);
    }

    /**
     * Edits the awardcase of involved users.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\AwardCaseManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEdit(Request $request, AwardCaseManager $service)
    {
        if(!$request->ids) { flash('No '.__('awards.awards').' selected.')->error(); }
        if(!$request->quantities) { flash('Quantities not set.')->error(); }

        if($request->ids && $request->quantities) {
            switch($request->action) {
                default:
                    flash('Invalid action selected.')->error();
                    break;
                case 'transfer':
                    return $this->postTransfer($request, $service);
                    break;
                case 'characterTransfer':
                    return $this->postTransferToCharacter($request, $service);
                    break;
                case 'delete':
                    return $this->postDelete($request, $service);
                    break;
            }
        }
        return redirect()->back();
    }

    /**
     * Transfers awardcase awards to another user.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\AwardCaseManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    private function postTransfer(Request $request, AwardCaseManager $service)
    {
        if($service->transferStack(Auth::user(), User::visible()->where('id', $request->get('user_id'))->first(), UserAward::find($request->get('ids')), $request->get('quantities'))) {
            flash(ucfirst(__('awards.award')).' transferred successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Transfers inventory items to another user.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\InventoryManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    private function postTransferToCharacter(Request $request, AwardCaseManager $service)
    {
        if($service->transferCharacterStack(Auth::user(), Character::visible()->where('id', $request->get('character_id'))->first(), UserAward::find($request->get('ids')), $request->get('quantities'))) {
            flash(ucfirst(__('awards.award')).' transferred successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Deletes an awardcase stack.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\AwardCaseManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    private function postDelete(Request $request, AwardCaseManager $service)
    {
        if($service->deleteStack(Auth::user(), UserAward::find($request->get('ids')), $request->get('quantities'))) {
            flash(ucfirst(__('awards.award')).' deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Shows the awardcase selection widget.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSelector($id)
    {
        return view('widgets._awardcase_select', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Acts on an award based on the award's tag.
     *
     * @param  \Illuminate\Http\Request       $request
     * @return \Illuminate\Http\RedirectResponse
     */
    private function postAct(Request $request)
    {
        $stacks = UserAward::with('award')->find($request->get('ids'));
        $tag = $request->get('tag');
        $service = $stacks->first()->award->hasTag($tag) ? $stacks->first()->award->tag($tag)->service : null;
        if($service && $service->act($stacks, Auth::user(), $request->all())) {
            flash(ucfirst(__('awards.award')).' used successfully.')->success();
        }
        else if(!$stacks->first()->award->hasTag($tag)) flash('Invalid action selected.')->error();
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /*****************************************************************************
     *
     * PROGRESSION STUFF
     *
     *****************************************************************************/

     /**
      * Claims award if progression is met.
      */
    public function postClaimAward(AwardCaseManager $service, $id)
    {
        $award = Award::find($id);
        if($service->claimAward($award, Auth::user())) {
            flash(ucfirst(__('awards.award')).' claimed successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
}
