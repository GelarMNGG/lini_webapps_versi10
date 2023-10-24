<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class ClockinController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth' => 'verified']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect()->back();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.attendance.clockin');
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

        $request->validate([
            'clockin_image' => 'required',
        ]);

        //file handler
        $fileName = null;
        $destinationPath = public_path().'/img/attendance/';
        
        // Retrieving An Uploaded File
        $file = $request->file('clockin_image');
        if (!empty($file)) {
            $extension = $file->getClientOriginalExtension();
            $fileName = time().'_'.$file->getClientOriginalName();
    
            // Moving An Uploaded File
            $request->file('clockin_image')->move($destinationPath, $fileName);
        }

        //custom setting to support file upload
        $data = $request->except(['_token','submit']);
        $data['user_id'] = $userId;
        $data['user_type'] = $userType;
        $data['department_id'] = $userDepartment;

        if (!empty($fileName)) {
            $data['clockin_image'] = $fileName;
        }

        DB::table('attendance')->insert($data);

        return redirect()->route('attendance.index')->with('alert-success','Clock in berhasil.');
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
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        $data['clockinData'] = DB::table('attendance')->where('id',$id)->first();

        return view('user.attendance.clockin-edit', $data);
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
        $request->validate([
            'clockin' => 'required',
            'note' => 'required|min:10',
        ]);

        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        //data supervisor
        $dataSupervisor = DB::table('admins')->where('department_id',$userDepartment)->first();

        //data customization
        $data = $request->except(['_token','_method','submit']);
        $data['clockin'] = date('H:i:s', strtotime($request->clockin));

        $data['user_id'] = $userId;
        $data['user_type'] = $userType;
        $data['department_id'] = $userDepartment;
        $data['supervisor_id'] = $dataSupervisor->id;

        //insert data into attendance_amandment table
        DB::table('attendance_amendment')->insert($data);

        //send notification to her supervisor
        $dataNotif['receiver_id'] = $dataSupervisor->id;
        $dataNotif['receiver_type'] = $dataSupervisor->user_type;
        $dataNotif['publisher_id'] = $userId;
        $dataNotif['publisher_type'] = $userType;
        $dataNotif['desc'] = ucfirst(Auth::user()->firstname).' '.ucfirst(Auth::user()->lastname).' mengajukan pengubahan jam clockin hari ini.';

        DB::table('notifications')->insert($dataNotif);
        
        return redirect()->route('attendance.index')->with('alert-success','Permintaan perubahan jam clock in Anda sudah dikirimkan ke supervisor Anda.');
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
