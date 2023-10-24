<?php

namespace App\Http\Controllers\User\Task\Collaboration\Internal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Auth;
use DB;

class TaskInternalCommentFileController extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Upload File pada Komentar Task Kolaborasi Internal Departemen.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Upload File pada Komentar Task Kolaborasi Internal Departemen.');
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
        $userLevel = Auth::user()->user_level;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $coAdmin = 22; //coadmin
        
        $liniId = 1; //lini
        $commentId = $request->comment_id;
        $taskId = $request->task_id;

        $firstCheck = DB::table('tasks_internal')->where('id',$taskId)->first();
        $secondCheck = DB::table('tasks_internal_comments')->where('id',$commentId)->where('publisher_id',$userId)->first();
        #####for image naming used
        $dataForImageName = $firstCheck->title;
        $dataForImageUploaderFirstname = Auth::user()->firstname;
        $dataForImageUploaderLastname = Auth::user()->lastname;

        if ($userCompany == $liniId && isset($firstCheck) && isset($secondCheck)) {
            $request->validate([
                'image' => 'required|mimes:jpeg,jpg,png,pdf,xls,xlsx,zip,doc,docx|max:4096',
            ]);

            //file handler
            $fileName = null;
            $destinationPath = public_path().'/img/comment-file/task-internal/';
            
            // Retrieving An Uploaded File
            $file = $request->file('image');
            if (!empty($file)) {
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::slug($dataForImageName).'_COMMENT_'.ucfirst($dataForImageUploaderFirstname).'-'.ucfirst($dataForImageUploaderLastname).'_'.time().'_'.$file->getClientOriginalName();

                // Moving An Uploaded File
                $imageType = ['jpeg','jpg','png','JPEG','JPG','PNG'];
                $imageTypeCheck = in_array($extension,$imageType);

                if ($imageTypeCheck) {
                    $size = filesize($file)/1000;
                    if ($size > 4001) {
                        $x = 55;
                    }elseif ($size > 1000 && $size < 4000) {
                        $x = 75;
                    }else{
                        $x = 100;
                    }
                    Image::make($file)->save($destinationPath.$fileName,$x);
                }else{
                    $request->file('image')->move($destinationPath, $fileName);
                }
            }

            //custom setting to support file upload
            $data = $request->except(['_token','_method','submit']);
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
            
            if (!empty($fileName)) {
                $data['image'] = $fileName;
            }

            DB::table('tasks_internal_comments_files')->insert($data);

            //sent notifications
                $dataTaskInternal = DB::table('tasks_internal')->where('id',$taskId)->first();
                $theId = $dataTaskInternal->id;
                $theTitle = $dataTaskInternal->title;

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
                $dataAlpha = DB::table('admins')->where('department_id',$userDepartment)->where('active',1)->first();
                $dataNotif['receiver_id'] = $dataAlpha->id;
                $dataNotif['receiver_type'] = $dataAlpha->user_type;
                $dataNotif['receiver_department'] = $userDepartment;

                ###notif message
                $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengupload file pada komentar <a href='".route('task-internal.show',$theId)."'><strong>".ucfirst($theTitle)."</strong></a>.";
                
                ###insert data to notifications table
                $notifData = DB::table('notifications')->insert($dataNotif);
                
                //send notif to coadmin
                $coAdminRawDatas = DB::table('tasks_leaders')->select('coadmin_id')->where('id',$theId)->first();
                if (isset($coAdminRawDatas)) {
                    $coAdminArrays = $coAdminRawDatas->coadmin_id;
                    if ($userLevel != $coAdmin) {
                        $coadminDatas = DB::table('users')->where('id','LIKE','%'.$coAdminArrays.'%')->get();
                    }else{
                        $coadminDatas = DB::table('users')->where('id','LIKE','%'.$coAdminArrays.'%')->where('id','!=',$userId)->get();
                    }
                    foreach ($coadminDatas as $dataTeta) {
                        $dataNotif['receiver_id'] = $dataTeta->id;
                        $dataNotif['receiver_type'] = $dataTeta->user_type;
                        $dataNotif['receiver_department'] = $dataTeta->department_id;
    
                        $dataNotif['level'] = 1;
    
                        ###notif message
                        $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengupload file pada komentar <a href='".route('user-task-internal.show',$theId)."'><strong>".ucfirst($theTitle)."</strong></a>.";
                        ###insert data to notifications table
                        $notifData = DB::table('notifications')->insert($dataNotif);
                    }
                }
                //staff / pic
                $staffDatas = DB::table('tasks_internal_pic')->where('task_id',$theId)->get();
                foreach ($staffDatas as $dataBeta) {
                    $dataNotif['receiver_id'] = $dataBeta->pic_id;
                    $dataNotif['receiver_type'] = 'user';
                    $dataNotif['receiver_department'] = $userDepartment;

                    $dataNotif['level'] = 1;

                    ###notif message
                    $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengupload file pada komentar <a href='".route('user-task-internal.show',$theId)."'><strong>".ucfirst($theTitle)."</strong></a>.";
                    ###insert data to notifications table
                    $notifData = DB::table('notifications')->insert($dataNotif);
                }
            //sent notifications end

            return redirect()->back()->with('alert-success','File berhasil diupload.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Upload File pada Komentar Task Kolaborasi Internal Departemen.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Upload File pada Komentar Task Kolaborasi Internal Departemen.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Upload File pada Komentar Task Kolaborasi Internal Departemen.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Upload File pada Komentar Task Kolaborasi Internal Departemen.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Upload File pada Komentar Task Kolaborasi Internal Departemen.');
    }
}
