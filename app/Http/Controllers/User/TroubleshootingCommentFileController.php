<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Auth;
use DB;

class TroubleshootingCommentFileController extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman upload file Troubleshooting Comments.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman upload file Troubleshooting Comments.');
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

        $commentId = $request->comment_id;

        $firstCheck = DB::table('troubleshooting_comments')->where('id',$commentId)->where('publisher_id',$userId)->first();

        if (isset($firstCheck)) {
            $request->validate([
                'image' => 'required|mimes:jpeg,jpg,png,pdf|max:4096',
            ]);
    
            //file handler
            $fileName = null;
            $destinationPath = public_path().'/img/comment-file/troubleshooting/';
            
            // Retrieving An Uploaded File
            $file = $request->file('image');
            if (!empty($file)) {
                $extension = $file->getClientOriginalExtension();
                $fileName = time().'_'.$file->getClientOriginalName();
    
                // Moving An Uploaded File
                $request->file('image')->move($destinationPath, $fileName);
            }
    
            //custom setting to support file upload
            $data = $request->only('comment_id','image');
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
            
            if (!empty($fileName)) {
                $data['image'] = $fileName;
            }
    
            DB::table('troubleshooting_comments_files')->insert($data);

            //sent notifications
                $troublesId = $firstCheck->troubles_id;
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
                $receiverId = $dataTroubles->publisher_id;
                $receiverType = $dataTroubles->publisher_type;

                if ($receiverId == $userId && $receiverType == $userType) {
                    $commentorDatas = DB::table('troubleshooting_comments')->where('troubles_id',$troublesId)->groupby('publisher_id')->get();
                    foreach ($commentorDatas as $dataAlpha) {
                        $receiverCommentType = $dataAlpha->publisher_type;
                        $dataNotif['level'] = 1;

                        $dataNotif['receiver_id'] = $dataAlpha->publisher_id;
                        $dataNotif['receiver_type'] = $dataAlpha->publisher_type;
                        //$dataNotif['receiver_department'] = $dataAlpha->department_id;

                        if ($receiverCommentType == 'admin') {
                            $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengupload file pada troubleshooting <a href='".route('admin.troubleshootingdetail',$theId)."'><strong>".ucfirst($theTitle)."</strong></a> untuk Anda.</strong>";
                        }elseif ($receiverType == 'user') {
                            $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengupload file pada troubleshooting <a href='".route('user.troubleshootingdetail',$theId)."'><strong>".ucfirst($theTitle)."</strong></a> untuk Anda.</strong>";
                        }else{
                            $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengupload file pada troubleshooting <a href='".route('tech.troubleshootingdetail',$theId)."'><strong>".ucfirst($theTitle)."</strong></a> untuk Anda.</strong>";
                        }
                        $notifData = DB::table('notifications')->insert($dataNotif);
                        ////need improvement
                        /*
                        if ($dataAlpha->publisher_id != $userId && $dataAlpha->publisher_type != $userType) {
                        }
                        */
                        ////need improvement end
                    }
                }else{
                    $dataNotif['receiver_id'] = $dataTroubles->publisher_id;
                    $dataNotif['receiver_type'] = $dataTroubles->publisher_type;
                    $dataNotif['receiver_department'] = $dataTroubles->department_id;
                    $dataNotif['level'] = 1;
                    ###notif message
                    if ($receiverType == 'admin') {
                        $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengupload file pada troubleshooting <a href='".route('admin.troubleshootingdetail',$theId)."'><strong>".ucfirst($theTitle)."</strong></a> untuk Anda.</strong>";
                    }elseif ($receiverType == 'user') {
                        $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengupload file pada troubleshooting <a href='".route('user.troubleshootingdetail',$theId)."'><strong>".ucfirst($theTitle)."</strong></a> untuk Anda.</strong>";
                    }else{
                        $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengupload file pada troubleshooting <a href='".route('tech.troubleshootingdetail',$theId)."'><strong>".ucfirst($theTitle)."</strong></a> untuk Anda.</strong>";
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

                    $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengupload file pada troubleshooting <a href='".route('user.troubleshootingdetail',$theId)."'><strong>".ucfirst($theTitle)."</strong></a>.</strong>";

                    $notifData = DB::table('notifications')->insert($dataNotif);
                }
                ###notif for technical support - 14 end
            //sent notifications end

            return redirect()->back()->with('alert-success','File berhasil diupload.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman upload file Troubleshooting Comments.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman upload file Troubleshooting Comments.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman upload file Troubleshooting Comments.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman upload file Troubleshooting Comments.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman upload file Troubleshooting Comments.');
    }
}
