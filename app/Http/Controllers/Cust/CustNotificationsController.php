<?php

namespace App\Http\Controllers\Cust;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Auth;
use DB;

class CustNotificationsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:cust');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notifStatus = 0;
        $customerId = Auth::user()->id;
        $userType = Auth::user()->user_type;

        $data['notifById'] = DB::table('notifications')
            ->where('receiver_id','=',$customerId)
            ->where('receiver_type','=',$userType)
            ->where('status','=',$notifStatus)
            ->orderBy('date','DESC')
            ->first();

        if ($data['notifById'] !== null) {
            $notifByIdData = $data['notifById']->id;
        }else{
            $notifByIdData = 0;
        }

        $data['notifAlls'] = DB::table('notifications')
            ->where('id','!=',$notifByIdData)
            ->where('receiver_id','=',$customerId)
            ->where('receiver_type','=',$userType)
            ->orderBy('status','ASC')
            ->orderBy('date','DESC')
            ->get();

        $data['notifDataCount'] = DB::table('notifications')
            ->where('receiver_id','=',$customerId)
            ->where('receiver_type','=',$userType)
            ->count();

        return view('cust.notification', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Notifications.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Notifications.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Notifications.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['status'] = 1;
        Notifikasi::where('id',$notifikasi)->update($data);
        
        $customerId = Auth::user()->id;
        $userType = Auth::user()->user_type;

        $data['notifById'] = DB::table('notifications')
            ->where('id','=',$notifikasi)
            ->first();

        $data['notifAlls'] = DB::table('notifications')
            ->where('id','!=',$notifikasi)
            ->where('receiver_id','=',$customerId)
            ->where('receiver_type','=',$userType)
            ->orderBy('status','ASC')
            ->orderBy('date','DESC')
            ->get();

        $data['notifDataCount'] = DB::table('notifications')
            ->where('receiver_id','=',$customerId)
            ->where('receiver_type','=',$userType)
            ->count();

        return view('cust.notification', $data);
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
        if (isset($request->status)) {
            $data['status'] = 1;
            Notifikasi::where('id',$notifikasi)->update($data);
            return redirect()->route('notifikasi-cust.index')->with('success','Status notifikasi berhasil diperbarui.');
        }
        return redirect()->route('notifikasi-cust.index')->with('success','Data notifikasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Notifications.');
    }
}
