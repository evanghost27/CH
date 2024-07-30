<?php

namespace App\Http\Controllers\WorldExpansion;

use Settings;
use App\Http\Controllers\Controller;
use App\Models\SitePage;
use App\Models\WorldExpansion\Glossary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorldExpansionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | World Expansion Controller
    |--------------------------------------------------------------------------
    |
    | Displays information about the world, as entered in the admin panel.
    | Pages displayed by this controller form the site's encyclopedia.
    |
    */

    /**
     * Shows the index page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        $world = SitePage::where('key','world')->first();
        if(!$world) abort(404);

        return view('worldexpansion.world', [
            'world' => $world
        ]);
    }
    
    /**
     * Shows the glossary page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getGlossary(Request $request)
    {
        $showGlossary = Settings::get('WE_glossary');
        if(!$showGlossary) abort(404);
        
        $query = Glossary::query();
        $name = $request->get('name');
        $sort = $request->get('sort');
        if($name) $query->where('name', 'LIKE', '%'.$name.'%');
        
        if(isset($sort))
        {
            switch($sort) {
                case 'alpha':
                    $query->sortAlphabetical();
                    break;
                case 'alpha-reverse':
                    $query->sortAlphabetical(true);
                    break;
            }
        }
        else $query->sortAlphabetical();
        if(!Auth::check() || !(Auth::check() && Auth::user()->isStaff)) $query->visible();
        
        return view('worldexpansion.glossary', [
            'terms' => $query->paginate(30)->appends($request->query()),
        ]);
    }
}
