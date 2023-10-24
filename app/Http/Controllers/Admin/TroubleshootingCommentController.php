<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class TroubleshootingCommentController extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Troubleshooting Comments.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Troubleshooting Comments.');
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

        $troublesId = $request->troubles_id;

        //data validation
        $request->validate([
            'comment' => 'required|min:10'
        ]);

        //customize the data
        $data = $request->except('_token','submit','receiver_department');
        $data['publisher_id'] = $userId;
        $data['publisher_type'] = $userType;
        $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('troubleshooting_comments')->insert($data);
        #$id = DB::table('troubleshooting_comments')->insertGetId($data);

        //sent notifications
            $dataTroubles = DB::table('troubleshooting')->where('id',$troublesId)->first();
            $theId = $dataTroubles->id;
            $theTitle = $dataTroubles->title;

            ###publisher id & type
            $dataNotif['publisher_id'] = $userId;
            $dataNotif['publisher_type'] = $userType;
            $dataNotif['publisher_department'] = $userDepartment;
            $publisherName = Auth::user()->name;
            $publisherFirstname = Auth::user()->firstname;
            $publisherLastname = Auth::user()->lastname;
            if ($publisherFirstname !== null) {
                $publisherName = ucfirst($publisherFirstname).' '.ucfirst($publisherLastname);
            }
            ###receiver id & type
            $receiverId = $request->receiver_id;
            $receiverType = $request->receiver_type;

            if ($receiverId == $userId && $receiverType == $userType) {
                $commentorDatas = DB::table('troubleshooting_comments')->where('troubles_id',$troublesId)->get();
                foreach ($commentorDatas as $dataAlpha) {
                    if ($dataAlpha->publisher_id != $userId && $dataAlpha->publisher_type != $userType) {
                        $dataNotif['receiver_id'] = $dataAlpha->publisher_id;
                            $dataNotif['receiver_type'] = $dataAlpha->publisher_type;
                            //$dataNotif['receiver_department'] = $dataAlpha->department_id;
                            $dataNotif['level'] = 1;
                            $receiverType = $dataAlpha->publisher_type;
        
                        if ($receiverType == 'admin') {
                            $dataNotif['desc'] = "<strong>".$publisherName."</strong> memberikan komentar pada troubleshooting <a href='".route('admin.troubleshootingdetail',$theId)."'><strong>".ucfirst($theTitle)."</strong></a> untuk Anda.</strong>";
                        }elseif($receiverType == 'user'){
                            $dataNotif['desc'] = "<strong>".$publisherName."</strong> memberikan komentar pada troubleshooting <a href='".route('user.troubleshootingdetail',$theId)."'><strong>".ucfirst($theTitle)."</strong></a> untuk Anda.</strong>";
                        }else{
                            $dataNotif['desc'] = "<strong>".$publisherName."</strong> memberikan komentar pada troubleshooting <a href='".route('tech.troubleshootingdetail',$theId)."'><strong>".ucfirst($theTitle)."</strong></a> untuk Anda.</strong>";
                        }
        
                        $notifData = DB::table('notifications')->insert($dataNotif);
                    }
                }
            }else{
                $dataNotif['receiver_id'] = $request->receiver_id;
                $dataNotif['receiver_type'] = $request->receiver_type;
                $dataNotif['receiver_department'] = $request->receiver_department;
                $dataNotif['level'] = 1;
                ###notif message
                if ($receiverType == 'admin') {
                    $dataNotif['desc'] = "<strong>".$publisherName."</strong> memberikan komentar pada troubleshooting <a href='".route('admin.troubleshootingdetail',$theId)."'><strong>".ucfirst($theTitle)."</strong></a> untuk Anda.</strong>";
                }elseif($receiverType == 'user'){
                    $dataNotif['desc'] = "<strong>".$publisherName."</strong> memberikan komentar pada troubleshooting <a href='".route('user.troubleshootingdetail',$theId)."'><strong>".ucfirst($theTitle)."</strong></a> untuk Anda.</strong>";
                }else{
                    $dataNotif['desc'] = "<strong>".$publisherName."</strong> memberikan komentar pada troubleshooting <a href='".route('tech.troubleshootingdetail',$theId)."'><strong>".ucfirst($theTitle)."</strong></a> untuk Anda.</strong>";
                }
                ###insert data to notifications table
                $notifData = DB::table('notifications')->insert($dataNotif);
            }

            ###notif for technical support - 14
            $techSupportLevel = 14;
            $techSupportDatas = DB::table('users')->where('user_level',$techSupportLevel)->get();
            foreach ($techSupportDatas as $dataTs) {
                $dataNotif['receiver_id'] = $dataTs->id;
                $dataNotif['receiver_type'] = $dataTs->user_type;
                $dataNotif['receiver_department'] = $dataTs->department_id;
                $dataNotif['level'] = 1;

                $dataNotif['desc'] = "<strong>".$publisherName."</strong> memberikan komentar pada troubleshooting <a href='".route('user.troubleshootingdetail',$theId)."'><strong>".ucfirst($theTitle)."</strong></a>.</strong>";

                $notifData = DB::table('notifications')->insert($dataNotif);
            }
            ###notif for technical support - 14 end
        //sent notifications end

        return redirect()->back()->with('alert-success','Komentar berhasil dikirimkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Troubleshooting Comments.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Troubleshooting Comments.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Troubleshooting Comments.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Troubleshooting Comments.');
    }
}
