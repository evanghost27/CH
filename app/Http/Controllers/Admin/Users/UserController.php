<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\Rank\Rank;
use DB;
use Auth;
use Config;
use Settings;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\User\User;
use App\Models\User\UserAlias;
use App\Models\User\UserUpdateLog;
use App\Models\WorldExpansion\Location;
use App\Models\WorldExpansion\Faction;

use App\Services\UserService;




class UserController extends Controller {
    /**
     * Show the user index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex(Request $request) {
        $query = User::join('ranks', 'users.rank_id', '=', 'ranks.id')->select('ranks.name AS rank_name', 'users.*');
        $sort = $request->only(['sort']);

        if ($request->get('name')) {
            $query->where(function ($query) use ($request) {
                $query->where('users.name', 'LIKE', '%'.$request->get('name').'%')->orWhere('users.alias', 'LIKE', '%'.$request->get('name').'%');
            });
        }
        if ($request->get('rank_id')) {
            $query->where('rank_id', $request->get('rank_id'));
        }

        switch ($sort['sort'] ?? null) {
            default:
                $query->orderBy('ranks.sort', 'DESC')->orderBy('name');
                break;
            case 'alpha':
                $query->orderBy('name');
                break;
            case 'alpha-reverse':
                $query->orderBy('name', 'DESC');
                break;
            case 'alias':
                $query->aliasSort();
                break;
            case 'alias-reverse':
                $query->aliasSort(true);
                break;
            case 'rank':
                $query->orderBy('ranks.sort', 'DESC')->orderBy('name');
                break;
            case 'newest':
                $query->orderBy('created_at', 'DESC');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'ASC');
                break;
        }

        return view('admin.users.index', [
            'users' => $query->paginate(30)->appends($request->query()),
            'ranks' => [0 => 'Any Rank'] + Rank::orderBy('ranks.sort', 'DESC')->pluck('name', 'id')->toArray(),
            'count' => $query->count(),
        ]);
    }

    /**
     * Show a user's admin page.
     *
     * @param mixed $name
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUser($name)
    {
        $interval = array(
            0 => 'whenever',
            1 => 'yearly',
            2 => 'quarterly',
            3 => 'monthly',
            4 => 'weekly',
            5 => 'daily'
        );

        $user = User::where('name', $name)->first();

        if (!$user) {
            abort(404);
        }

        return view('admin.users.user', [
            'user' => $user,
            'ranks' => Rank::orderBy('ranks.sort')->pluck('name', 'id')->toArray(),
            'locations' => Location::all()->where('is_user_home')->pluck('style','id')->toArray(),
            'factions' => Faction::all()->where('is_user_faction')->pluck('style','id')->toArray(),
            'user_enabled' => Settings::get('WE_user_locations'),
            'user_faction_enabled' => Settings::get('WE_user_factions'),
            'char_enabled' => Settings::get('WE_character_locations'),
            'char_faction_enabled' => Settings::get('WE_character_factions'),
            'location_interval' => $interval[Settings::get('WE_change_timelimit')]
        ]);
    }

    public function postUserBasicInfo(Request $request, $name) {
        $user = User::where('name', $name)->first();
        if (!$user) {
            flash('Invalid user.')->error();
        } elseif (!Auth::user()->canEditRank($user->rank)) {
            flash('You cannot edit the information of a user that has a higher rank than yourself.')->error();
        } else {
            if (!(new UserService)->logAdminAction(Auth::user(), 'Edited User', 'Edited '.$user->displayname)) {
                flash('Failed to log admin action.')->error();

                return redirect()->back();
            }

            $request->validate([
                'name' => 'required|between:3,25',
            ]);
            $data = $request->only(['name'] + (!$user->isAdmin ? [1 => 'rank_id'] : []));

            $logData = ['old_name' => $user->name] + $data;
            if ($user->update($data)) {
                UserUpdateLog::create(['staff_id' => Auth::user()->id, 'user_id' => $user->id, 'data' => json_encode($logData), 'type' => 'Name/Rank Change']);
                flash('Updated user\'s information successfully.')->success();
            } else {
                flash('Failed to update user\'s information.')->error();
            }
        }

        return redirect()->to($user->adminUrl);
    }

    public function postUserLocation(Request $request, $name)
    {
        $user = User::where('name', $name)->first();
        $service = new UserService;

        if(!$user) flash('Invalid user.')->error();
        else if (!Auth::user()->canEditRank($user->rank)) {
            flash('You cannot edit the information of a user that has a higher rank than yourself.')->error();
        }
        else if($service->updateLocation($request->input('location'), $user)) {
            flash('Location updated successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    public function postUserFaction(Request $request, $name)
    {
        $user = User::where('name', $name)->first();
        $service = new UserService;
        
        if(!$user) flash('Invalid user.')->error();
        else if (!Auth::user()->canEditRank($user->rank)) {
            flash('You cannot edit the information of a user that has a higher rank than yourself.')->error();
        }
        else if($service->updateFaction($request->input('faction'), $user)) {
            flash('Faction updated successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }


    public function postUserAlias(Request $request, $name, $id)
    {
        $user = User::where('name', $name)->first();
        $alias = UserAlias::find($id);

        $logData = ['old_alias' => $user ? $alias->alias : null, 'old_site' => $user ? $alias->site : null];
        $isPrimary = $alias->is_primary_alias;

        if (!$user) {
            flash('Invalid user.')->error();
        } elseif (!$alias) {
            flash('Invalid alias.')->error();
        } elseif (!Auth::user()->canEditRank($user->rank)) {
            flash('You cannot edit the information of a user that has a higher rank than yourself.')->error();
        } elseif ($alias && $alias->delete()) {
            // If this was the user's primary alias, check if they have any remaining valid primary aliases
            // or any remaining aliases at all, and update accordingly
            if ($isPrimary) {
                if (!$user->aliases->count()) {
                    $user->update(['has_alias' => 0]);
                } else {
                    // Hidden aliases are excluded as a courtesy measure (users may not want them forced visible for any number of reasons)
                    foreach ($user->aliases as $alias) {
                        if (config('lorekeeper.sites.'.$alias->site.'.auth') && config('lorekeeper.sites.'.$alias->site.'.primary_alias') && $alias->is_visible) {
                            $alias->update(['is_primary_alias' => 1]);
                            break;
                        }
                    }
                    // If the user does not have a remaining valid primary alias after this check, update accordingly
                    if (!$user->primaryAlias) {
                        $user->update(['has_alias' => 0]);
                    }
                }
            }
            if (!(new UserService)->logAdminAction(Auth::user(), 'Edited User', 'Cleared '.$user->displayname.'\' alias')) {
                flash('Failed to log admin action.')->error();

                return redirect()->back();
            }

            UserUpdateLog::create(['staff_id' => Auth::user()->id, 'user_id' => $user->id, 'data' => json_encode($logData), 'type' => 'Clear Alias']);
            flash('Cleared user\'s alias successfully.')->success();
        } else {
            flash('Failed to clear user\'s alias.')->error();
        }

        return redirect()->back();
    }

    public function postUserAccount(Request $request, $name) {
        $user = User::where('name', $name)->first();

        if (!$user) {
            flash('Invalid user.')->error();
        } elseif (!Auth::user()->canEditRank($user->rank)) {
            flash('You cannot edit the information of a user that has a higher rank than yourself.')->error();
        } elseif ($user->settings->update(['is_fto' => $request->get('is_fto') ?: 0])) {
            if (!(new UserService)->logAdminAction(Auth::user(), 'Edited User', 'Edited '.$user->displayname)) {
                flash('Failed to log admin action.')->error();

                return redirect()->back();
            }

            UserUpdateLog::create(['staff_id' => Auth::user()->id, 'user_id' => $user->id, 'data' => json_encode(['is_fto' => $request->get('is_fto') ? 'Yes' : 'No']), 'type' => 'FTO Status Change']);
            flash('Updated user\'s account information successfully.')->success();
        } else {
            flash('Failed to update user\'s account information.')->error();
        }

        return redirect()->back();
    }

    public function postUserBirthday(Request $request, $name) {
        $user = User::where('name', $name)->first();
        if (!$user) {
            flash('Invalid user.')->error();
        }

        $service = new UserService;
        // Make birthday into format we can store
        $date = $request->input('dob');

        $formatDate = Carbon::parse($date);
        $logData = ['old_date' => $user->birthday ? $user->birthday->isoFormat('DD-MM-YYYY') : Carbon::now()->isoFormat('DD-MM-YYYY')] + ['new_date' => $date];
        if (!(new UserService)->logAdminAction(Auth::user(), 'Edited User', 'Edited '.$user->displayname.' birthday')) {
            flash('Failed to log admin action.')->error();

            return redirect()->back();
        }

        if ($service->updateBirthday($formatDate, $user)) {
            UserUpdateLog::create(['staff_id' => Auth::user()->id, 'user_id' => $user->id, 'data' => json_encode($logData), 'type' => 'Birth Date Change']);
            flash('Birthday updated successfully!')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Show a user's account update log.
     *
     * @param mixed $name
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUserUpdates($name) {
        $user = User::where('name', $name)->first();

        if (!$user) {
            abort(404);
        }

        return view('admin.users.user_update_log', [
            'user' => $user,
            'logs' => UserUpdateLog::where('user_id', $user->id)->orderBy('id', 'DESC')->paginate(50),
        ]);
    }

    /**
     * Show a user's ban page.
     *
     * @param mixed $name
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getBan($name) {
        $user = User::where('name', $name)->first();

        if (!$user) {
            abort(404);
        }

        return view('admin.users.user_ban', [
            'user' => $user,
        ]);
    }

    /**
     * Show a user's ban confirmation page.
     *
     * @param mixed $name
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getBanConfirmation($name) {
        $user = User::where('name', $name)->first();

        if (!$user) {
            abort(404);
        }

        return view('admin.users._user_ban_confirmation', [
            'user' => $user,
        ]);
    }

    public function postBan(Request $request, UserService $service, $name) {
        $user = User::where('name', $name)->with('settings')->first();
        $wasBanned = $user->is_banned;
        if (!$user) {
            flash('Invalid user.')->error();
        } elseif (!Auth::user()->canEditRank($user->rank)) {
            flash('You cannot edit the information of a user that has a higher rank than yourself.')->error();
        } elseif ($service->ban(['ban_reason' => $request->get('ban_reason')], $user, Auth::user())) {
            flash($wasBanned ? 'User ban reason edited successfully.' : 'User banned successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Show a user's unban confirmation page.
     *
     * @param mixed $name
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUnbanConfirmation($name) {
        $user = User::where('name', $name)->with('settings')->first();

        if (!$user) {
            abort(404);
        }

        return view('admin.users._user_unban_confirmation', [
            'user' => $user,
        ]);
    }

    public function postUnban(Request $request, UserService $service, $name) {
        $user = User::where('name', $name)->first();

        if (!$user) {
            flash('Invalid user.')->error();
        } elseif (!Auth::user()->canEditRank($user->rank)) {
            flash('You cannot edit the information of a user that has a higher rank than yourself.')->error();
        } elseif ($service->unban($user, Auth::user())) {
            flash('User unbanned successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Show a user's deactivate page.
     *
     * @param mixed $name
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeactivate($name) {
        $user = User::where('name', $name)->first();

        if (!$user) {
            abort(404);
        }

        return view('admin.users.user_deactivate', [
            'user' => $user,
        ]);
    }

    /**
     * Show a user's deactivate confirmation page.
     *
     * @param mixed $name
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeactivateConfirmation($name) {
        $user = User::where('name', $name)->first();

        if (!$user) {
            abort(404);
        }

        return view('admin.users._user_deactivate_confirmation', [
            'user' => $user,
        ]);
    }

    public function postDeactivate(Request $request, UserService $service, $name) {
        $user = User::where('name', $name)->with('settings')->first();
        $wasDeactivated = $user->is_deactivated;
        if (!$user) {
            flash('Invalid user.')->error();
        } elseif (!Auth::user()->canEditRank($user->rank)) {
            flash('You cannot edit the information of a user that has a higher rank than yourself.')->error();
        } elseif ($service->deactivate(['deactivate_reason' => $request->get('deactivate_reason')], $user, Auth::user())) {
            flash($wasDeactivated ? 'User deactivation reason edited successfully.' : 'User deactivated successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Show a user's reactivate confirmation page.
     *
     * @param mixed $name
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getReactivateConfirmation($name) {
        $user = User::where('name', $name)->with('settings')->first();

        if (!$user) {
            abort(404);
        }

        return view('admin.users._user_reactivate_confirmation', [
            'user' => $user,
        ]);
    }

    public function postReactivate(Request $request, UserService $service, $name) {
        $user = User::where('name', $name)->first();

        if (!$user) {
            flash('Invalid user.')->error();
        } elseif (!Auth::user()->canEditRank($user->rank)) {
            flash('You cannot edit the information of a user that has a higher rank than yourself.')->error();
        } elseif ($service->reactivate($user, Auth::user())) {
            flash('User reactivated successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
}
