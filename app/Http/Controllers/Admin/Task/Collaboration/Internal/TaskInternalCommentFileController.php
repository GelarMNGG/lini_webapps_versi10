<?php

namespace App\Http\Controllers\Admin\Task\Collaboration\Internal;

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
        $this->middleware('auth:admin');
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
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

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
    public function destroy(Request $request, $id)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userLevel = Auth::user()->user_level;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $liniId = 1; //lini
        $taskId = $request->task_id;

        $firstCheck = DB::table('tasks_internal')->where('id',$taskId)->where('publisher_id',$userId)->first();
        $dataImage = DB::table('tasks_internal_comments_files')->select('image as image')->where('id',$id)->first();
        
        if ($userCompany == $liniId && isset($firstCheck)) {
            DB::table('tasks_internal_comments_files')->delete($id);
            
            //delete previous image
            $destinationPath = public_path().'/img/comment-file/task-internal/';
            $oldImage = $dataImage->image;
            
            if($oldImage !== 'default.png'){
                $image_path = $destinationPath.$oldImage;
                if(File::exists($image_path)) {
                    File::delete($image_path);
                }
            }
            
            return redirect()->back()->with('alert-success','Data berhasil dihapus.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Upload File pada Komentar Task Kolaborasi Internal Departemen.');
    }
}
