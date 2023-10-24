<?php

namespace App\Http\Controllers\User\Task\Collaboration\MultiDepartment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Auth;
use DB;

class TaskLeaderCommentFileController extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Leaders Comments Upload File.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Leaders Comments Upload File.');
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
        $taskId = $request->task_id;

        $firstCheck = DB::table('tasks_leaders')->where('id',$taskId)->first();
        $secondCheck = DB::table('tasks_leaders_comments')->where('id',$commentId)->where('publisher_id',$userId)->first();
        #####for image naming used
        $dataForImageName = $firstCheck->title;
        $dataForImageUploaderFirstname = Auth::user()->firstname;
        $dataForImageUploaderLastname = Auth::user()->lastname;

        if (isset($firstCheck) && isset($secondCheck)) {
            $request->validate([
                'image' => 'required|mimes:jpeg,jpg,png,pdf,xls,xlsx,zip,doc,docx|max:4096',
            ]);

            //file handler
            $fileName = null;
            $destinationPath = public_path().'/img/comment-file/task-leaders/';
            
            // Retrieving An Uploaded File
            $file = $request->file('image');
            if (!empty($file)) {
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::slug($dataForImageName).'_COMMENT_'.ucfirst($dataForImageUploaderFirstname).'-'.ucfirst($dataForImageUploaderLastname).'_'.time().'_'.$file->getClientOriginalName();

                // Moving An Uploaded File
                $request->file('image')->move($destinationPath, $fileName);
            }

            //custom setting to support file upload
            $data = $request->except(['_token','_method','submit']);
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
            
            if (!empty($fileName)) {
                $data['image'] = $fileName;
            }

            DB::table('tasks_leaders_comments_files')->insert($data);

            //sent notifications
                $taskLeaderId = $taskId;
                $dataTaskLeaders = DB::table('tasks_leaders')->where('id',$taskLeaderId)->first();
                $theId = $dataTaskLeaders->id;
                $theTitle = $dataTaskLeaders->title;
                $theDepartment = unserialize($dataTaskLeaders->receiver_department);
                $pubDepartment = $dataTaskLeaders->publisher_department;

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
                $receiverId = $dataTaskLeaders->publisher_id;
                $receiverType = $dataTaskLeaders->publisher_type;

                //admin
                $collaboratorDatas = DB::table('admins')->select('id','user_type','department_id')->where('id','!=',$userId)->where('department_id','!=',NULL)->get();
                foreach ($collaboratorDatas as $dataAlpha) {
                    if (in_array($dataAlpha->department_id,$theDepartment)) {
                        $dataNotif['receiver_id'] = $dataAlpha->id;
                        $dataNotif['receiver_type'] = $dataAlpha->user_type;
                        $dataNotif['receiver_department'] = $dataAlpha->department_id;
                        $dataNotif['level'] = 1;
    
                        ###notif message
                        $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengupload file pada komentar proyek <a href='".route('task-leaders.show',$theId)."'><strong>".ucfirst($theTitle)."</strong></a>.</strong>";
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
                            $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengupload file pada komentar <a href='".route('user-task-leaders.show',$theId)."'><strong>".ucfirst($theTitle)."</strong></a>.";
                            ###insert data to notifications table
                            $notifData = DB::table('notifications')->insert($dataNotif);
                        }
                    }
                }
            //sent notifications end

            return redirect()->back()->with('alert-success','File berhasil diupload.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Leaders Comments Upload File.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Leaders Comments Upload File.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Leaders Comments Upload File.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Leaders Comments Upload File.');
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
        $coAdmin = 22; //co admin level

        if ($userLevel != $coAdmin) {
            return redirect()->back()->with('alert-danger','Gagal menghapus file.');
        }

        $firstCheck = DB::table('tasks_leaders')->where('id',$taskId)->where('coadmin_id','LIKE','%'.$userId.'%')->first();
        $publisherCheck = DB::table('tasks_leaders')->where('id',$taskId)->where('publisher_id',$userId)->where('publisher_type',$userType)->first();
        $dataImage = DB::table('tasks_leaders_comments_files')->select('image as image')->where('id',$id)->first();
        
        if (isset($firstCheck) || isset($publisherCheck)) {
            DB::table('tasks_leaders_comments_files')->delete($id);
            
            //delete previous image
            $destinationPath = public_path().'/img/comment-file/task-leaders/';
            $oldImage = $dataImage->image;
            
            if($oldImage !== 'default.png'){
                $image_path = $destinationPath.$oldImage;
                if(File::exists($image_path)) {
                    File::delete($image_path);
                }
            }
            
            return redirect()->back()->with('alert-success','Data berhasil dihapus.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Leaders Comments Upload File.');
    }
}
