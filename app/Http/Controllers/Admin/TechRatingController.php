<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class TechRatingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Rating Teknisi.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Rating Teknisi.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        $techId = $request->tech_id;
        $rating = $request->rating;

        $firstcheck = DB::table('proc_tech_rating')->where('tech_id',$techId)->where('giver_id',$userId)->where('giver_type',$userType)->where('giver_department',$userDepartment)->count();

        if ($userDepartment == 9 && $firstcheck < 1) {
            $data = $request->except(['_token','submit','rating']);

            if ($rating == 1) {
                $data['one'] = 1;
                $ratingTechId['one'] = 1;
                $ratingCode = 'one';
            }elseif($rating == 2){
                $data['two'] = 1;
                $ratingTechId['two'] = 1;
                $ratingCode = 'two';
            }elseif($rating == 3){
                $data['three'] = 1;
                $ratingTechId['three'] = 1;
                $ratingCode = 'three';
            }elseif($rating == 4){
                $data['four'] = 1;
                $ratingTechId['four'] = 1;
                $ratingCode = 'four';
            }elseif($rating == 5){
                $data['five'] = 1;
                $ratingTechId['five'] = 1;
                $ratingCode = 'five';
            }

            $data['giver_id'] = $userId;
            $data['giver_type'] = $userType;
            $data['giver_department'] = $userDepartment;
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');

            //add rating result
            $existingRating = DB::table('proc_tech_rating_result')->where('tech_id',$techId)->count();
            if ($existingRating > 0) {
                DB::table('proc_tech_rating_result')->where('tech_id',$techId)->increment($ratingCode,1);
            }else{
                $ratingTechId['tech_id'] = $techId;
                DB::table('proc_tech_rating_result')->insert($ratingTechId);
            }

            //update rating note
            DB::table('proc_tech_rating')->insert($data);

            return redirect()->back()->with('alert-success','Data berhasil diupdate.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Rating Teknisi.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Rating Teknisi.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Rating Teknisi.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Rating Teknisi.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Rating Teknisi.');
    }
}
