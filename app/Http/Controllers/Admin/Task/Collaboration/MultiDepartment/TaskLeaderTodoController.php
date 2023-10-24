<?php

namespace App\Http\Controllers\Admin\Task\Collaboration\MultiDepartment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Auth;
use DB;

class TaskLeaderTodoController extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Todo Task Kolaborasi Multi Departemen.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        $taskId = $request->tid;
        $departmentId = $request->did;

        $firstCheck = DB::table('tasks_leaders')->where('id',$taskId)->first();
        if (isset($firstCheck->receiver_department)) {
            $receiverDepartments = unserialize($firstCheck->receiver_department);
            $secondCheck = in_array($departmentId,$receiverDepartments);
        }else{
            $secondCheck = false;
        }

        if (!isset($firstCheck) || $secondCheck == false) {
            return redirect()->back()->with('alert-warning','Halaman yang akan Anda tuju tidak tersedia.');
        }

        if (isset($firstCheck)) {
            $data['departmentData'] = DB::table('department')->where('id',$departmentId)->first();
            $data['taskData'] = $firstCheck;

            return view('admin.task.collaboration.task-leader.todo.create',$data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Todo Task Kolaborasi Multi Departemen.');
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
        $departmentId = $request->department_id;

        $firstCheck = DB::table('tasks_leaders')->where('id',$taskId)->first();

        if ($userType == 'admin' && isset($firstCheck)) {
            $request->validate([
                'name' => 'required',
            ]);
    
            // date setting
            $data = $request->except('_token','submit');
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
            $data['requester_id'] = $userId;
            $data['requester_type'] = $userType;
            $data['requester_department'] = $userDepartment;

            //insert to database
            DB::table('tasks_leaders_todos')->insert($data);
    
            //send notifications
                $theDepartment = $departmentId;
                $taskLeader = DB::table('tasks_leaders')->select('id','title','publisher_department')->where('id',$taskId)->first();
                $theId = $taskLeader->id;
                $theTitle = $taskLeader->title;
                $pubDepartment = $taskLeader->publisher_department;
    
                $dataNotif['publisher_id'] = $userId;
                $dataNotif['publisher_type'] = $userType;
                $dataNotif['publisher_department'] = $userDepartment;
                $publisherName = Auth::user()->name;
                $publisherFirstname = Auth::user()->firstname;
                $publisherLastname = Auth::user()->lastname;
                if ($publisherFirstname !== null) {
                    $publisherName = ucfirst($publisherFirstname).' '.ucfirst($publisherLastname);
                }else{
                    $publisherName = ucfirst($publisherName);
                }
                ###receiver id & type
                //Admin
                $collaboratorDatas = DB::table('admins')->get();
                foreach ($collaboratorDatas as $dataAlpha) {
                    if ($dataAlpha->department_id != $userDepartment) {
                        if ($dataAlpha->department_id == $theDepartment || $dataAlpha->department_id == $pubDepartment) {
                            $dataNotif['receiver_id'] = $dataAlpha->id;
                            $dataNotif['receiver_type'] = $dataAlpha->user_type;
                            $dataNotif['receiver_department'] = $dataAlpha->department_id;

                            $dataNotif['level'] = 1;
        
                            ###notif message
                            $dataNotif['desc'] = "<strong>".$publisherName."</strong> menambahkan checklist dalam proyek <a href='".route('task-leaders.show',$theId)."'><strong>".ucfirst($theTitle)."</strong></a> untuk departemen Anda.";
                            ###insert data to notifications table
                            $notifData = DB::table('notifications')->insert($dataNotif);
                        }
                    }
                }
                //staff / pic
                $staffDatas = DB::table('tasks_leaders_pic')->where('task_id',$taskId)->get();
                foreach ($staffDatas as $dataBeta) {
                    if ($dataBeta->department_id != $userDepartment) {
                        if ($dataBeta->department_id == $theDepartment || $dataBeta->department_id == $pubDepartment) {
                            $dataNotif['receiver_id'] = $dataBeta->pic_id;
                            $dataNotif['receiver_type'] = 'user';
                            $dataNotif['receiver_department'] = $dataBeta->department_id;

                            $dataNotif['level'] = 1;
        
                            ###notif message
                            $dataNotif['desc'] = "<strong>".$publisherName."</strong> menambahkan checklist dalam proyek <a href='".route('user-task-leaders.show',$theId)."'><strong>".ucfirst($theTitle)."</strong></a> untuk departemen Anda.";
                            ###insert data to notifications table
                            $notifData = DB::table('notifications')->insert($dataNotif);
                        }
                    }
                }

            //send notifications end
    
            return redirect()->route('task-leaders.show',$taskId)->with('alert-success','PIC collaborative leader berhasil didaftarkan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Todo Task Kolaborasi Multi Departemen.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        $taskId = $request->tid;
        $departmentId = $request->did;
        $theId = $id; //todo_id

        $firstCheck = DB::table('tasks_leaders')->where('id',$taskId)->first();
        $secondCheck = DB::table('tasks_leaders_todos')->where('id',$theId)->first();
        
        if (!isset($firstCheck) || !isset($secondCheck)) {
            return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
        }

        $departmentId = $secondCheck->department_id;

        if ($userDepartment == $departmentId || $userId == $firstCheck->publisher_id || $userDepartment == 11) {
            $data['todoData'] = DB::table('tasks_leaders_todos')->where('id',$theId)->first();
            $data['departmentData'] = DB::table('department')->where('id',$departmentId)->first();
            $data['leadersTodoFiles'] = DB::table('tasks_leaders_todos_files')->where('todo_id',$theId)->where('todo_id', $theId)->get();
            $data['admins'] = DB::table('admins')->get();

            $data['taskData'] = $firstCheck;
            $data['currentTodoData'] = $secondCheck;

            //other data
            $data['dataComments'] = DB::table('tasks_leaders_todos_comments as tc')
            ->select([
                'tc.*',
                DB::raw('(SELECT COUNT(comment_id) FROM tasks_leaders_comments_files WHERE tasks_leaders_comments_files.comment_id = tc.id) countFiles')
            ])
            ->where('task_id', $taskId)
            ->where('todo_id', $theId)
            ->orderBy('id','DESC')
            ->get();

            ###comment count
            $data['countComments'] = DB::table('tasks_leaders_todos_comments')
                ->where('task_id', $taskId)
                ->where('todo_id', $theId)
                ->count();

            //supporting datas
            $data['users'] = DB::table('users')->get();

            return view('admin.task.collaboration.task-leader.todo.show',$data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Todo Task Kolaborasi Multi Departemen.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $theId = explode("&", $id, 2)[0];
        $taskId = explode("&", $id, 2)[1];

        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        $firstCheck = DB::table('tasks_leaders')->where('id',$taskId)->first();
        $secondCheck = DB::table('tasks_leaders_todos')->where('id',$theId)->first();

        
        if (!isset($firstCheck) || !isset($secondCheck)) {
            return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
        }

        $departmentId = $secondCheck->department_id;

        if ($userDepartment == $departmentId || $userId == $firstCheck->publisher_id) {
            $data['todoData'] = DB::table('tasks_leaders_todos')->where('id',$theId)->first();
            $data['departmentData'] = DB::table('department')->where('id',$departmentId)->first();

            $data['taskData'] = $firstCheck;
            $data['currentTodoData'] = $secondCheck;

            return view('admin.task.collaboration.task-leader.todo.edit',$data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Todo Task Kolaborasi Multi Departemen.');
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
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        $todoId = $id;
        $taskId = $request->task_id;
        $departmentId = $request->department_id;

        $firstCheck = DB::table('tasks_leaders')->where('id',$taskId)->first();
        $secondCheck = DB::table('tasks_leaders_todos')->where('task_id',$taskId)->where('department_id',$departmentId)->where('id',$todoId)->first();

        if (!isset($firstCheck) || !isset($secondCheck)) {
            return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
        }

        if ($userDepartment == $departmentId || $userId == $firstCheck->publisher_id) {
            $request->validate([
                'name' => 'required',
            ]);
    
            // date setting
            $data = $request->except('_token','submit','_method');
            $data['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
            //$data['requester_id'] = $userId;
            //$data['requester_type'] = $userType;
            //$data['requester_department'] = $userDepartment;

            //insert to database
            DB::table('tasks_leaders_todos')->where('id',$id)->update($data);
    
            //send notifications
                $theDepartment = $departmentId;
                $taskLeader = DB::table('tasks_leaders')->select('id','title','publisher_department')->where('id',$taskId)->first();
                $theId = $taskLeader->id;
                $theTitle = $taskLeader->title;
                $pubDepartment = $taskLeader->publisher_department;
    
                $dataNotif['publisher_id'] = $userId;
                $dataNotif['publisher_type'] = $userType;
                $dataNotif['publisher_department'] = $userDepartment;
                $publisherName = Auth::user()->name;
                $publisherFirstname = Auth::user()->firstname;
                $publisherLastname = Auth::user()->lastname;
                if ($publisherFirstname !== null) {
                    $publisherName = ucfirst($publisherFirstname).' '.ucfirst($publisherLastname);
                }else{
                    $publisherName = ucfirst($publisherName);
                }
                ###receiver id & type
                //admin
                $collaboratorDatas = DB::table('admins')->get();
                foreach ($collaboratorDatas as $dataAlpha) {
                    if ($dataAlpha->department_id != $userDepartment) {
                        if ($dataAlpha->department_id == $theDepartment || $dataAlpha->department_id == $pubDepartment) {
                            $dataNotif['receiver_id'] = $dataAlpha->id;
                            $dataNotif['receiver_type'] = $dataAlpha->user_type;
                            $dataNotif['receiver_department'] = $dataAlpha->department_id;

                            $dataNotif['level'] = 1;
        
                            ###notif message
                            $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengubah check list dalam proyek <a href='".route('task-leaders.show',$theId)."'><strong>".ucfirst($theTitle)."</strong></a> untuk departemen Anda.";
                            ###insert data to notifications table
                            $notifData = DB::table('notifications')->insert($dataNotif);
                        }
                    }
                }
                //staff / pic
                $staffDatas = DB::table('tasks_leaders_pic')->where('task_id',$taskId)->get();
                foreach ($staffDatas as $dataBeta) {
                    if ($dataBeta->department_id != $userDepartment) {
                        if ($dataBeta->department_id == $theDepartment || $dataBeta->department_id == $pubDepartment) {
                            $dataNotif['receiver_id'] = $dataBeta->pic_id;
                            $dataNotif['receiver_type'] = 'user';
                            $dataNotif['receiver_department'] = $dataBeta->department_id;

                            $dataNotif['level'] = 1;
        
                            ###notif message
                            $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengubah checklist dalam proyek <a href='".route('user-task-leaders.show',$theId)."'><strong>".ucfirst($theTitle)."</strong></a> untuk departemen Anda.";
                            ###insert data to notifications table
                            $notifData = DB::table('notifications')->insert($dataNotif);
                        }
                    }
                }
            //send notifications end
    
            return redirect()->route('task-leaders.show',$taskId)->with('alert-success','Check list collaborative leader berhasil diubah.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Todo Task Kolaborasi Multi Departemen.');
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
        $userDepartment = Auth::user()->department_id;

        $taskId = $request->task_id;

        $firstCheck = DB::table('tasks_leaders')->where('id',$taskId)->first();
        $secondCheck = DB::table('tasks_leaders_todos')->where('id',$id)->first();
        $thirdCheck = DB::table('tasks_leaders_todos_files')->where('task_id',$taskId)->where('todo_id',$id)->get();
        
        if (isset($firstCheck) && $firstCheck->publisher_id == $userId && $firstCheck->publisher_department == $userDepartment || $userDepartment == $secondCheck->department_id) {
            DB::table('tasks_leaders_todos')->delete($id);

            //delete previous image
            if (isset($thirdCheck)) {
                $destinationPath = public_path().'/img/upload-doc/task-leaders/';
                foreach ($thirdCheck as $dataImage) {
                    $oldImage = $dataImage->image;
        
                    if($oldImage !== 'default.png'){
                        $image_path = $destinationPath.$oldImage;
                        if(File::exists($image_path)) {
                            File::delete($image_path);
                        }
                    }
                    DB::table('tasks_leaders_todos_files')->delete($dataImage->id);
                }
            }

            return redirect()->back()->with('alert-success','Data berhasil dihapus.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Todo Task Kolaborasi Multi Departemen.');
    }
}
