<?php

namespace App\Http\Controllers\Tech;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class TechProcVideoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:tech');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Procurement Videos.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Procurement Videos.');
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

        $videoType = $request->video_type;

        $firstCheck = DB::table('proc_videos_by_tech')->where('video_type',$videoType)->where('tech_id',$userId)->count();

        if ($firstCheck < 1) {
            //grabbing data
            $dataInput = $request->except(['_token','submit']);
    
            //filtering the data
            $dataInput['tech_id'] = $userId;

            $dataInput['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
    
            DB::table('proc_videos_by_tech')->insert($dataInput);

            if ($videoType == 1) {
                return redirect()->route('tech-test-training.index')->with('alert-success','Silahkan mengikuti training yang tersedia.');
            }else{
                return redirect()->route('tech-test-training.index')->with('alert-success','Silahkan mengikuti tes paska. Semoga beruntung!');
            }

        }

        return redirect()->back()->with('alert-success','Silahkan melanjutkan ke proses selanjutnya.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Procurement Videos.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Procurement Videos.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Procurement Videos.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Procurement Videos.');
    }
}
