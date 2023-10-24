<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use App\User;
use Auth;
use DB;

class UserNotificationsController extends Controller
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
        $notifStatus = 0;
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        $data['notifById'] = DB::table('notifications as notif')
            ->select([
                'notif.*',
                DB::raw('(SELECT tl_name FROM tasks_level WHERE tasks_level.tl_id = notif.level) as level_name'),
                DB::raw('(SELECT image FROM users WHERE users.id = notif.publisher_id AND notif.publisher_type = \'user\' AND users.image IS NOT NULL) as publisher_user_image'),
                DB::raw('(SELECT image FROM admins WHERE admins.id = notif.publisher_id AND notif.publisher_type = \'admin\' AND admins.image IS NOT NULL) as publisher_admin_image')
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
                DB::raw('(SELECT tl_name FROM tasks_level WHERE tasks_level.tl_id = notif.level) as level_name'),
                DB::raw('(SELECT image FROM users WHERE users.id = notif.publisher_id AND notif.publisher_type = \'user\' AND users.image IS NOT NULL) as publisher_user_image'),
                DB::raw('(SELECT image FROM admins WHERE admins.id = notif.publisher_id AND notif.publisher_type = \'admin\' AND admins.image IS NOT NULL) as publisher_admin_image')
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

        return view('user.notification', $data);
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
     * @param  \App\Models\Notifikasi  $notifikasi
     * @return \Illuminate\Http\Response
     */
    public function show(Notifikasi $notifikasi)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Notifikasi.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Notifikasi  $notifikasi
     * @return \Illuminate\Http\Response
     */
    public function edit($notifikasi)
    {
        $data['status'] = 1;
        Notifikasi::where('id',$notifikasi)->update($data);
        
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;

        $data['notifById'] = DB::table('notifications as notif')
            ->select([
                'notif.*',
                DB::raw('(SELECT tl_name FROM tasks_level WHERE tasks_level.tl_id = notif.level) as level_name')
            ])
            ->where('id','=',$notifikasi)
            ->first();

        $data['notifAlls'] = DB::table('notifications as notif')
            ->select([
                'notif.*',
                DB::raw('(SELECT tl_name FROM tasks_level WHERE tasks_level.tl_id = notif.level) as level_name')
            ])
            ->where('id','!=',$notifikasi)
            ->where('receiver_id','=',$userId)
            ->where('receiver_type','=',$userType)
            ->orderBy('status','ASC')
            ->orderBy('date','DESC')
            ->get();

        $data['notifDataCount'] = DB::table('notifications')
            ->where('receiver_id','=',$userId)
            ->where('receiver_type','=',$userType)
            ->count();

        return view('user.notification', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Notifikasi  $notifikasi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $notifikasi)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        if (isset($request->all)) {
            $data['status'] = 1;
            //Notifikasi::where('receiver_id',$userId)->where('receiver_department',$userDepartment)->where('receiver_type',$userType)->update($data);
            Notifikasi::where('receiver_id',$userId)->where('receiver_type',$userType)->update($data);
            return redirect()->route('notifikasi-user.index')->with('alert-success','Status notifikasi berhasil diperbarui.');
        }

        if (isset($request->status)) {

            $data['status'] = 1;
            Notifikasi::where('id',$notifikasi)->update($data);

            return redirect()->route('notifikasi-user.index')->with('alert-success','Status notifikasi berhasil diperbarui.');
        }
        return redirect()->route('notifikasi-user.index')->with('success','Data notifikasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Notifikasi  $notifikasi
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notifikasi $notifikasi)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Notifikasi.');
    }
}
