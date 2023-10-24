<?php

namespace App\Http\Controllers\User\Task\Collaboration\Internal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Auth;
use DB;

class TaskInternalTodoController extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Todo Task Kolaborasi Internal Departemen.');
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
        $userLevel = Auth::user()->user_level;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $liniId = 1; //lini
        $coAdmin = 22; //coadmin

        $taskId = $request->tid;
        $picId = $request->pid;

        $firstCheck = DB::table('tasks_internal')->where('id',$taskId)->where('publisher_id',$userId)->where('publisher_type',$userType)->first();
        $secondCheck = DB::table('tasks_internal_pic')->where('id',$picId)->first();

        if (!isset($firstCheck) || !isset($secondCheck)) {
            return redirect()->back()->with('alert-warning','Halaman yang akan Anda tuju tidak tersedia.');
        }

        if ($userCompany == $liniId && $userLevel == $coAdmin && isset($firstCheck)) {
            $receiverId = $secondCheck->pic_id;
            $receiverType = $secondCheck->pic_type;
            if ($receiverType == 'admin') {
                $data['todoData'] = DB::table('admins')->where('id',$receiverId)->first();
            }else{
                $data['todoData'] = DB::table('users')->where('id',$receiverId)->first();
            }
            $data['taskData'] = $firstCheck;
            $data['pid'] = $picId;

            return view('user.task.collaboration.task-internal.todo.create',$data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Todo Task Kolaborasi Internal Departemen.');
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

        $liniId = 1; //lini
        $coAdmin = 22; //coadmin
        $taskId = $request->task_id;
        $picId = $request->pic_id;
        
        $firstCheck = DB::table('tasks_internal')->where('id',$taskId)->where('publisher_id',$userId)->where('publisher_type',$userType)->first();
        $secondCheck = DB::table('tasks_internal_pic')->select('pic_id')->where('id',$picId)->first();

        if (!isset($firstCheck) || !isset($secondCheck)) {
            return redirect()->back()->with('alert-warning','Halaman yang akan Anda tuju tidak tersedia.');
        }

        if ($userCompany == $liniId && $userLevel == $coAdmin && isset($firstCheck)) {
            $request->validate([
                'name' => 'required',
            ]);
    
            // date setting
            $data = $request->except('_token','submit','pic_id');
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
            $data['department_id'] = $userDepartment;
            $data['requester_id'] = $userId;
            $data['requester_type'] = $userType;
            $data['receiver_id'] = $secondCheck->pic_id;
            $data['receiver_type'] = 'user';

            //insert to database
            DB::table('tasks_internal_todos')->insert($data);
    
            //send notifications
                $theDepartment = $userDepartment;
                $taskinternal = DB::table('tasks_internal')->select('id','title')->where('id',$taskId)->first();
                $theId = $taskinternal->id;
                $theTitle = $taskinternal->title;
    
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
                //staff / pic
                $receiverId = $secondCheck->pic_id;
                $dataNotif['receiver_id'] = $receiverId;
                $dataNotif['receiver_type'] = 'user';
                $dataNotif['receiver_department'] = $userDepartment;
                $dataNotif['level'] = 1;

                ###notif message
                $dataNotif['desc'] = "<strong>".$publisherName."</strong> menambahkan checklist dalam proyek <a href='".route('user-task-internal.show',$theId)."'><strong>".ucfirst($theTitle)."</strong></a> untuk departemen Anda.";
                ###insert data to notifications table
                $notifData = DB::table('notifications')->insert($dataNotif);
            //send notifications end
    
            return redirect()->route('user-task-internal.show',$taskId)->with('alert-success','PIC collaborative internal berhasil didaftarkan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Todo Task Kolaborasi Internal Departemen.');
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
        $theId = $id; //todo_id

        $firstCheck = DB::table('tasks_internal')->where('id',$taskId)->first();
        $secondCheck = DB::table('tasks_internal_todos')->where('id',$theId)->first();

        $departmentId = $firstCheck->department_id;
        
        if (!isset($firstCheck) || !isset($secondCheck)) {
            return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
        }

        if ($userDepartment == $departmentId || $userId == $firstCheck->publisher_id || $userDepartment == 11) {
            $receiverType = $secondCheck->receiver_type;
            if ($receiverType == 'admin') {
                $data['todoData'] = DB::table('tasks_internal_todos as tit')
                ->select([
                    'tit.*',
                    DB::raw('(SELECT firstname FROM admins WHERE admins.id = tit.receiver_id) as pic_firstname'),
                    DB::raw('(SELECT lastname FROM admins WHERE admins.id = tit.receiver_id) as pic_lastname')
                ])
                ->where('id',$theId)->first();
            }else{
                $data['todoData'] = DB::table('tasks_internal_todos as tit')
                ->select([
                    'tit.*',
                    DB::raw('(SELECT firstname FROM users WHERE users.id = tit.receiver_id) as pic_firstname'),
                    DB::raw('(SELECT lastname FROM users WHERE users.id = tit.receiver_id) as pic_lastname')
                ])
                ->where('id',$theId)->first();
            }

            $data['departmentData'] = DB::table('department')->where('id',$departmentId)->first();
            $data['internalTodoFiles'] = DB::table('tasks_internal_todos_files')->where('todo_id',$theId)->where('todo_id', $theId)->get();
            $data['admins'] = DB::table('admins')->get();

            $data['taskData'] = $firstCheck;
            $data['coAdminDatas'] = unserialize($firstCheck->coadmin_id);
            $data['currentTodoData'] = $secondCheck;

            //other data
            $data['dataComments'] = DB::table('tasks_internal_todos_comments as tc')
            ->select([
                'tc.*',
                DB::raw('(SELECT COUNT(comment_id) FROM tasks_internal_comments_files WHERE tasks_internal_comments_files.comment_id = tc.id) countFiles')
            ])
            ->where('task_id', $taskId)
            ->where('todo_id', $theId)
            ->orderBy('id','DESC')
            ->get();

            ###comment count
            $data['countComments'] = DB::table('tasks_internal_todos_comments')
                ->where('task_id', $taskId)
                ->where('todo_id', $theId)
                ->count();

            //supporting datas
            $data['users'] = DB::table('users')->get();

            return view('user.task.collaboration.task-internal.todo.show',$data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Todo Task Kolaborasi Internal Departemen.');
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

        $liniId = 1; //lini
        $coAdmin = 22; //coadmin

        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userLevel = Auth::user()->user_level;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $firstCheck = DB::table('tasks_internal')->where('id',$taskId)->where('publisher_id',$userId)->where('publisher_type',$userType)->first();
        $secondCheck = DB::table('tasks_internal_todos')->where('id',$theId)->where('requester_id',$userId)->first();
        
        if (!isset($firstCheck) || !isset($secondCheck)) {
            return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
        }

        if ($userCompany == $liniId && $userLevel == $coAdmin) {
            $receiverType = $secondCheck->receiver_type;
            if ($receiverType == 'admin') {
                $data['todoData'] = DB::table('tasks_internal_todos as tit')
                ->select([
                    'tit.*',
                    DB::raw('(SELECT firstname FROM admins WHERE admins.id = tit.receiver_id) as pic_firstname'),
                    DB::raw('(SELECT lastname FROM admins WHERE admins.id = tit.receiver_id) as pic_lastname')
                ])
                ->where('id',$theId)->first();
            }else{
                $data['todoData'] = DB::table('tasks_internal_todos as tit')
                ->select([
                    'tit.*',
                    DB::raw('(SELECT firstname FROM users WHERE users.id = tit.receiver_id) as pic_firstname'),
                    DB::raw('(SELECT lastname FROM users WHERE users.id = tit.receiver_id) as pic_lastname')
                ])
                ->where('id',$theId)->first();
            }

            $data['taskData'] = $firstCheck;
            $data['currentTodoData'] = $secondCheck;

            return view('user.task.collaboration.task-internal.todo.edit',$data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Todo Task Kolaborasi Internal Departemen.');
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
        $userLevel = Auth::user()->user_level;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $liniId = 1; //lini
        $coAdmin = 22; //coadmin
        $todoId = $id;
        $taskId = $request->task_id;

        $firstCheck = DB::table('tasks_internal')->where('id',$taskId)->where('publisher_id',$userId)->where('publisher_type',$userType)->first();
        $secondCheck = DB::table('tasks_internal_todos')->where('id',$todoId)->where('requester_id',$userId)->first();

        if (!isset($firstCheck) || !isset($secondCheck)) {
            return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
        }

        if ($userCompany == $liniId && $userLevel == $coAdmin) {
            $request->validate([
                'name' => 'required',
            ]);
    
            // date setting
            $data = $request->except('_token','submit','_method');
            $data['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

            //insert to database
            DB::table('tasks_internal_todos')->where('id',$id)->update($data);
    
            //send notifications
                $theDepartment = $userDepartment;
                $taskinternal = DB::table('tasks_internal')->select('id','title')->where('id',$taskId)->first();
                $theId = $taskinternal->id;
                $theTitle = $taskinternal->title;
    
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
                //staff / pic
                $picId = $secondCheck->receiver_id;
                $dataNotif['receiver_id'] = $picId;
                $dataNotif['receiver_type'] = 'user';
                $dataNotif['receiver_department'] = $userDepartment;

                $dataNotif['level'] = 1;

                ###notif message
                $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengubah checklist dalam proyek <a href='".route('user-task-internal.show',$theId)."'><strong>".ucfirst($theTitle)."</strong></a> untuk departemen Anda.";
                ###insert data to notifications table
                $notifData = DB::table('notifications')->insert($dataNotif);
            //send notifications end
    
            return redirect()->route('user-task-internal.show',$taskId)->with('alert-success','Check list collaborative internal berhasil diubah.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Todo Task Kolaborasi Internal Departemen.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Todo Task Kolaborasi Internal Departemen.');
    }
}
