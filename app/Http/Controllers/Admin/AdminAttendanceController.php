<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class AdminAttendanceController extends Controller
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
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        $now = Carbon::today();

        $data['attendances'] = DB::table('attendance')->where('user_id',$userId)->where('department_id', $userDepartment)->where('user_type',$userType)->orderBy('id','DESC')->get();
        
        ###clockinCount
        $data['clockinCount'] = DB::table('attendance')->where('user_id',$userId)->where('department_id', $userDepartment)->where('user_type',$userType)->whereDate('date',$now)->where('clockin','!=',null)->count();
        
        ###clockinAmendmentCount
        $data['clockinAmendmentCount'] = DB::table('attendance_amendment')->where('user_id',$userId)->where('department_id', $userDepartment)->where('user_type',$userType)->whereDate('date',$now)->where('clockin','!=',null)->count();

        ###clockoutCount
        $data['clockoutCount'] = DB::table('attendance')->where('user_id',$userId)->where('department_id', $userDepartment)->where('user_type',$userType)->whereDate('date',$now)->where('clockout','!=',null)->count();

        ###clockoutAmendmentCount
        $data['clockoutAmendmentCount'] = DB::table('attendance_amendment')->where('user_id',$userId)->where('department_id', $userDepartment)->where('user_type',$userType)->whereDate('date',$now)->where('clockout','!=',null)->count();

        return view('admin.attendance.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back();
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
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back();
    }
}
