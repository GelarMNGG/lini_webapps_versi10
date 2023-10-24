<?php

namespace App\Http\Controllers\Tech;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class TechNotificationsController extends Controller
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
        $notifStatus = 0;
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = 1;

        $data['notifById'] = DB::table('notifications as notif')
            ->select([
                'notif.*',
                DB::raw('(SELECT tl_name FROM tasks_level WHERE tasks_level.tl_id = notif.level) as level_name')
            ])
            ->where('receiver_id',$userId)
            ->where('receiver_type',$userType)
            //->where('receiver_department',$userDepartment)
            ->where('status',$notifStatus)
            ->orderBy('date','DESC')
            ->first();

        if ($data['notifById'] !== null) {
            $notifByIdData = $data['notifById']->id;
        }else{
            $notifByIdData = 0;
        }

        $data['notifAlls'] = DB::table('notifications as notif')
            ->select([
                'notif.*',
                DB::raw('(SELECT tl_name FROM tasks_level WHERE tasks_level.tl_id = notif.level) as level_name')
            ])
            ->where('id','!=',$notifByIdData)
            ->where('receiver_id',$userId)
            ->where('receiver_type',$userType)
            //->where('receiver_department',$userDepartment)
            ->orderBy('status','ASC')
            ->orderBy('date','DESC')
            ->get();

        $data['notifDataCount'] = DB::table('notifications')
            ->where('receiver_id','=',$userId)
            ->where('receiver_type','=',$userType)
            //->where('receiver_department',$userDepartment)
            ->count();

        return view('tech.notification', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Notifikasi.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Notifikasi.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Notifikasi.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Notifikasi.');
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
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        if (isset($request->all)) {
            $data['status'] = 1;
            //Notifikasi::where('receiver_id',$userId)->where('receiver_department',$userDepartment)->where('receiver_type',$userType)->update($data);
            Notifikasi::where('receiver_id',$userId)->where('receiver_type',$userType)->update($data);
            return redirect()->route('notifikasi-tech.index')->with('alert-success','Status notifikasi berhasil diperbarui.');
        }

        if (isset($request->status)) {
            $data['status'] = 1;

            DB::table('notifications')->where('id',$id)->update($data);

            return redirect()->route('notifikasi-tech.index')->with('alert-success','Status notifikasi berhasil diperbarui.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Notifikasi.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Notifikasi.');
    }
}
