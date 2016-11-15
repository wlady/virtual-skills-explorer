<?php
/**
 * Created by PhpStorm.
 * User: wlady2001
 * Date: 04.11.16
 * Time: 17:43
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function filter(Request $request)
    {
        return response()
            ->json($this->person->getFiltered($request))
            ->header('Content-Type', 'text/json');
    }
}
