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

class TaskInternalTodoFileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Upload File Todo Task Kolaborasi Internal Departemen.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Upload File Todo Task Kolaborasi Internal Departemen.');
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
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        $coAdmin = 22; //coadmin

        $liniId = 1; //lini
        $taskId = $request->task_id;
        $todoId = $request->todo_id;

        $firstCheck = DB::table('tasks_internal')->where('id',$taskId)->first();
        $secondCheck = DB::table('tasks_internal_todos')->where('id',$todoId)->whereOr('requester_id',$userId)->whereOr('receiver_id',$userId)->first();
        
        if (!isset($firstCheck) || !isset($secondCheck)) {
            return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengupload file pada todo ini.');
        }
        #####for image naming used

        $dataForImageName = $firstCheck->title;
        $dataForImageUploaderFirstname = Auth::user()->firstname;
        $dataForImageUploaderLastname = Auth::user()->lastname;

        if ($userCompany == $liniId && isset($firstCheck)) {
            $request->validate([
                'image' => 'required|mimes:jpeg,jpg,png,pdf,xls,xlsx,zip,doc,docx|max:4096',
            ]);

            //file handler
            $fileName = null;
            $destinationPath = public_path().'/img/upload-doc/task-internal/';
            
            // Retrieving An Uploaded File
            $file = $request->file('image');
            if (!empty($file)) {
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::slug($dataForImageName).'_TODO_'.ucfirst($dataForImageUploaderFirstname).'-'.ucfirst($dataForImageUploaderLastname).'_'.time().'_'.$file->getClientOriginalName();

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

            DB::table('tasks_internal_todos_files')->insert($data);

            //sent notifications
                $taskLeaderId = $taskId;
                $dataTaskinternal = DB::table('tasks_internal_todos')->where('id',$todoId)->first();
                $theId = $dataTaskinternal->id;
                $theTaskId = $dataTaskinternal->task_id;
                $theDeptId = $dataTaskinternal->department_id;
                $theTitle = $dataTaskinternal->name;

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
                $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengupload dokumen pendukung pada check list <a href='".route('task-internal-todo.show',$theId.'?tid='.$theTaskId.'&did='.$theDeptId)."'><strong>".ucfirst($theTitle)."</strong></a>.";
                ###insert data to notifications table
                $notifData = DB::table('notifications')->insert($dataNotif);

                //coadmin
                $coAdminRawDatas = DB::table('tasks_internal')->select('coadmin_id')->where('id',$theId)->first();
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
                        $dataNotif['receiver_department'] = $userDepartment;
        
                        $dataNotif['level'] = 1;
        
                        ###notif message
                        $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengupload dokumen pendukung pada check list <a href='".route('user-task-internal-todo.show',$theId.'?tid='.$theTaskId.'&did='.$theDeptId)."'><strong>".ucfirst($theTitle)."</strong></a>.";
                        ###insert data to notifications table
                        $notifData = DB::table('notifications')->insert($dataNotif);
                    }
                }
            //sent notifications end

            return redirect()->back()->with('alert-success','File berhasil diupload.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Upload File Todo Task Kolaborasi Internal Departemen.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Upload File Todo Task Kolaborasi Internal Departemen.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Upload File Todo Task Kolaborasi Internal Departemen.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Upload File Todo Task Kolaborasi Internal Departemen.');
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
        $userDepartment = Auth::user()->department_id;

        $taskId = $request->task_id;
        $coAdmin = 22; //coadmin

        $firstCheck = DB::table('tasks_internal')->where('id',$taskId)->where('publisher_id',$userId)->where('publisher_type',$userType)->first();
        $dataImage = DB::table('tasks_internal_todos_files')->select('image as image')->where('id',$id)->first();

        if (!isset($firstCheck) || !isset($dataImage) || $userLevel != $coAdmin) {
            return redirect()->back()->with('alert-danger','Anda tidak diijinkan menghapus file pada task ini.');
        }
        
        if (isset($firstCheck)) {
            DB::table('tasks_internal_todos_files')->delete($id);
            
            //delete previous image
            $destinationPath = public_path().'/img/upload-doc/task-internal/';
            $oldImage = $dataImage->image;
            
            if($oldImage !== 'default.png'){
                $image_path = $destinationPath.$oldImage;
                if(File::exists($image_path)) {
                    File::delete($image_path);
                }
            }
            
            return redirect()->back()->with('alert-success','Data berhasil dihapus.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Upload File Todo Task Kolaborasi Internal Departemen.');
    }
}
