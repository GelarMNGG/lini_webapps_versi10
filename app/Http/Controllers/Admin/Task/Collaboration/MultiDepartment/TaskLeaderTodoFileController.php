<?php

namespace App\Http\Controllers\Admin\Task\Collaboration\MultiDepartment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Auth;
use DB;

class TaskLeaderTodoFileController extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Upload File Todo Task Kolaborasi Multi Departemen.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Upload File Todo Task Kolaborasi Multi Departemen.');
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

        $taskId = $request->task_id;
        $todoId = $request->todo_id;

        $firstCheck = DB::table('tasks_leaders')->where('id',$taskId)->where('publisher_department',$userDepartment)->first();
        #####for image naming used
        $dataForImageName = $firstCheck->title;
        $dataForImageUploaderFirstname = Auth::user()->firstname;
        $dataForImageUploaderLastname = Auth::user()->lastname;

        if (isset($firstCheck)) {
            $request->validate([
                'image' => 'required|mimes:jpeg,jpg,png,pdf,xls,xlsx,zip,doc,docx|max:4096',
            ]);

            //file handler
            $fileName = null;
            $destinationPath = public_path().'/img/upload-doc/task-leaders/';
            
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

            DB::table('tasks_leaders_todos_files')->insert($data);

            //sent notifications
                $taskLeaderId = $taskId;
                $dataTaskLeaders = DB::table('tasks_leaders_todos')->where('id',$todoId)->first();
                $theId = $dataTaskLeaders->id;
                $theTaskId = $dataTaskLeaders->task_id;
                $theDeptId = $dataTaskLeaders->department_id;
                $theTitle = $dataTaskLeaders->name;

                $dataTasks = DB::table('tasks_leaders')->select('publisher_department','receiver_department')->where('id',$theTaskId)->first();
                $theDepartment = unserialize($dataTasks->receiver_department);
                $pubDepartment = $dataTasks->publisher_department;

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
                $collaboratorDatas = DB::table('admins')->select('id','user_type','department_id')->where('id','!=',$userId)->where('department_id','!=',NULL)->get();
                //admin
                foreach ($collaboratorDatas as $dataAlpha) {
                    if (in_array($dataAlpha->department_id,$theDepartment)) {
                        $dataNotif['receiver_id'] = $dataAlpha->id;
                        $dataNotif['receiver_type'] = $dataAlpha->user_type;
                        $dataNotif['receiver_department'] = $dataAlpha->department_id;
                        $dataNotif['level'] = 1;
    
                        ###notif message
                        //$dataNotif['desc'] = "<strong>".$publisherName."</strong> mengupload dokumen pendukung pada check list <a href='".route('task-leaders.show-todo',$theId)."'><strong>".ucfirst($theTitle)."</strong></a>.";
                        $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengupload dokumen pendukung pada check list <a href='".route('task-leaders-todo.show',$theId.'?tid='.$theTaskId.'&did='.$theDeptId)."'><strong>".ucfirst($theTitle)."</strong></a>.";

                        ###insert data to notifications table
                        $notifData = DB::table('notifications')->insert($dataNotif);
                    }
                }
                //staff / pic
                $staffDatas = DB::table('tasks_leaders_pic')->where('task_id',$theId)->get();
                foreach ($staffDatas as $dataBeta) {
                    if ($dataBeta->department_id != $userDepartment) {
                        if ($dataBeta->department_id == $theDepartment || $dataBeta->department_id == $pubDepartment) {
                            $dataNotif['receiver_id'] = $dataBeta->pic_id;
                            $dataNotif['receiver_type'] = 'user';
                            $dataNotif['receiver_department'] = $dataBeta->department_id;

                            $dataNotif['level'] = 1;
        
                            ###notif message
                            $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengupload dokumen pendukung pada check list <a href='".route('user-task-leaders-todo.show',$theId.'?tid='.$theTaskId.'&did='.$theDeptId)."'><strong>".ucfirst($theTitle)."</strong></a>.";
                            ###insert data to notifications table
                            $notifData = DB::table('notifications')->insert($dataNotif);
                        }
                    }
                }
            //sent notifications end

            return redirect()->back()->with('alert-success','File berhasil diupload.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Upload File Todo Task Kolaborasi Multi Departemen.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Upload File Todo Task Kolaborasi Multi Departemen.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Upload File Todo Task Kolaborasi Multi Departemen.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Upload File Todo Task Kolaborasi Multi Departemen.');
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

        $firstCheck = DB::table('tasks_leaders')->where('id',$taskId)->where('publisher_department',$userDepartment)->first();
        $dataImage = DB::table('tasks_leaders_todos_files')->select('image as image')->where('id',$id)->first();
        
        if (isset($firstCheck)) {
            DB::table('tasks_leaders_todos_files')->delete($id);
            
            //delete previous image
            $destinationPath = public_path().'/img/upload-doc/task-leaders/';
            $oldImage = $dataImage->image;
            
            if($oldImage !== 'default.png'){
                $image_path = $destinationPath.$oldImage;
                if(File::exists($image_path)) {
                    File::delete($image_path);
                }
            }
            
            return redirect()->back()->with('alert-success','Data berhasil dihapus.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Upload File Todo Task Kolaborasi Multi Departemen.');
    }
}
