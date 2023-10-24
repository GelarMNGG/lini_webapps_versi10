<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Auth;
use DB;

class TaskUserCommentFileController extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Comments Upload File.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Comments Upload File.');
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

        $firstCheck = DB::table('tasks_comments')->where('tc_id',$commentId)->where('tc_publisher_id',$userId)->count();

        if ($firstCheck > 0) {
            $request->validate([
                'image' => 'required|mimes:jpeg,jpg,png,pdf|max:4096',
            ]);
    
            //file handler
            $fileName = null;
            $destinationPath = public_path().'/img/comment-file/task/';
            
            // Retrieving An Uploaded File
            $file = $request->file('image');
            if (!empty($file)) {
                $extension = $file->getClientOriginalExtension();
                $fileName = time().'_'.$file->getClientOriginalName();
    
                // Moving An Uploaded File
                $request->file('image')->move($destinationPath, $fileName);
            }
    
            //custom setting to support file upload
            //$data = $request->except('_token','submit','tc_task_id','tc_receiver_id','receiver_type','receiver_department','task_title','publisher_department');
            $data = $request->only('comment_id','image');
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
            
            if (!empty($fileName)) {
                $data['image'] = $fileName;
            }
    
            DB::table('tasks_comments_files')->insert($data);
    
            //sent notifications
                $taskId = $request->tc_task_id;
                $taskName = $request->task_title;
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
                if ($userId == $request->tc_receiver_id && $userType == $request->receiver_type && $userDepartment == $request->receiver_department) {
                    $receiverType = $request->publisher_type;
                    $dataNotif['receiver_id'] = $request->tc_publisher_id;
                    $dataNotif['receiver_type'] = $request->publisher_type;
                    $dataNotif['receiver_department'] = $request->publisher_department;
                }else{
                    $receiverType = $request->receiver_type;
                    $dataNotif['receiver_id'] = $request->tc_receiver_id;
                    $dataNotif['receiver_type'] = $request->receiver_type;
                    $dataNotif['receiver_department'] = $request->receiver_department;
                }
                $dataNotif['level'] = 1;
                ###notif message
                if ($receiverType == 'admin') {
                    $dataNotif['desc'] = "<strong>".$publisherName."</strong> upload file pada task <a href='".route('task.show',$taskId)."'><strong>".ucfirst($taskName)."</strong></a> untuk Anda.</strong>";
                }else{
                    $dataNotif['desc'] = "<strong>".$publisherName."</strong> upload file pada task <a href='".route('task-user.show',$taskId)."'><strong>".ucfirst($taskName)."</strong></a> untuk Anda.</strong>";
                }
    
                ###insert data to notifications table
                $notifData = DB::table('notifications')->insert($dataNotif);
            //sent notifications end
            
            return redirect()->back()->with('alert-success','File berhasil diupload.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Comments Upload File.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Comments Upload File.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Comments Upload File.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Comments Upload File.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $userId = Auth::user()->id;
        $userLevel = Auth::user()->user_level;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if ($userCompany == 1) {

            //check priviledge
            $dataCheck = DB::table('tasks_comments_files')->where('id',$id)->count();

            if ($dataCheck > 0) {
                //delete previous image
                $destinationPath = public_path().'/img/comment-file/task/';
                $dataImages = DB::table('tasks_comments_files')->select('image as image')->where('id', $id)->get();

                if (count($dataImages) > 0) {
                    foreach($dataImages as $dataImage){
                        $oldImage = $dataImage->image;
        
                        if($oldImage !== 'default.png'){
                            $image_path = $destinationPath.$oldImage;
                            if(File::exists($image_path)) {
                                File::delete($image_path);
                            }
                        }
                    }
                }

                //delete from database
                DB::table('tasks_comments_files')->delete($id);

                return redirect()->back()->with('alert-success','File berhasil dihapus.');
            }

            return redirect()->back()->with('alert-danger','File tidak ditemukan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Comments Upload File.');
    }
}
