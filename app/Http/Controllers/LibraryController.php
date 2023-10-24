<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class LibraryController extends Controller
{
    public function getCityList(Request $request)
    {
        $cities = DB::table('list_of_cities')
            ->where('code',$request->code)
            ->pluck('name','id');
        
        return response()->json($cities);
    }
}
