<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class TaskUserController extends Controller
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
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;
        $data['admins'] = DB::table('admins')->get();
        $data['users'] = DB::table('users')->get();

        //given tasks list
        $data['countData'] = DB::table('tasks')->where('task_receiver_id',$userId)->where('receiver_type',$userType)->where('receiver_department',$userDepartment)->count();
        $data['tasks'] = DB::table('tasks')
            ->select([
                'tasks.*',
                DB::raw('(SELECT tl_name FROM tasks_level WHERE tasks_level.tl_id = tasks.task_level) as task_level_title'),
                DB::raw('(SELECT ts_name FROM tasks_status WHERE tasks_status.ts_id = tasks.task_status) as pengiriman_status')
            ])
            ->where('task_receiver_id',$userId)
            ->where('receiver_type',$userType)
            ->where('receiver_department',$userDepartment)
            ->orderBy('task_status','ASC')
            ->orderBy('task_date','DESC')
            ->paginate(10);
        
        //tasks list
        $data['countDataTwo'] = DB::table('tasks')->where('task_publisher_id',$userId)->where('publisher_type',$userType)->where('publisher_department',$userDepartment)->count();
        $data['tasksTwo'] = DB::table('tasks')
            ->select([
                'tasks.*',
                DB::raw('(SELECT tl_name FROM tasks_level WHERE tasks_level.tl_id = tasks.task_level) as task_level_title'),
                DB::raw('(SELECT ts_name FROM tasks_status WHERE tasks_status.ts_id = tasks.task_status) as pengiriman_status')
            ])
            ->where('task_publisher_id',$userId)
            ->where('publisher_type',$userType)
            ->where('publisher_department',$userDepartment)
            ->orderBy('task_status','ASC')
            ->orderBy('task_date','DESC')
            ->paginate(10);

        return view('user.task.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //data
        $data['currentUserId'] = Auth::user()->id;
        $data['currentUserType'] = Auth::user()->user_type;
        $data['taskPriorities'] = DB::table('tasks_level')->get();
        $data['userTypes'] = DB::table('user_type')->get();

        return view('user.task.create', $data);
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

        $request->validate([
            'receiver_type' => 'required',
            'task_receiver_id' => 'required',
            'task_title' => 'required|min:10',
            'task_date' => 'required|after_or_equal:today',
            'task_due_date' => 'required|after_or_equal:task_date',
        ]);

        // date setting
        $request['task_date'] = Carbon::createFromFormat('Y-m-d', $request->task_date)->format('Y-m-d H:i:s');
        $request['task_due_date'] = Carbon::createFromFormat('Y-m-d', $request->task_due_date)->format('Y-m-d H:i:s');
        $request['task_publisher_id'] = $userId;
        $request['publisher_type'] = $userType;
        $request['publisher_department'] = $userDepartment;
        $request['receiver_department'] = $userDepartment;

        //insert to database
        DB::table('tasks')->insert($request->except('_token','submit'));

        //sent notifications
        $dataNotif['publisher_id'] = $request['task_publisher_id'];
        $dataNotif['publisher_type'] = $request['publisher_type'];
        $dataNotif['publisher_department'] = $userDepartment;
        $publisherName = Auth::user()->name;
        $publisherFirstname = Auth::user()->firstname;
        $publisherLastname = Auth::user()->lastname;
        if ($publisherFirstname !== null) {
            $publisherName = ucfirst($publisherFirstname).' '.ucfirst($publisherLastname);
        }
        ###receiver id & type
        $dataNotif['receiver_id'] = $request['task_receiver_id'];
        $dataNotif['receiver_type'] = $request['receiver_type'];
        $dataNotif['receiver_department'] = $userDepartment;
        $dataNotif['level'] = $request['task_level'];
        ###notif message
        $dataNotif['desc'] = "<strong>".$publisherName."</strong> membuat tugas <strong>".ucfirst($request['task_title'])."</strong> untuk Anda.</strong>";
        ###insert data to notifications table
        $notifData = DB::table('notifications')->insert($dataNotif);

        return redirect()->route('task-user.index')->with('alert-success','Penugasan berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        //key data
        $data['userId'] = $userId;
        $data['userType'] = $userType;
        $data['admins'] = DB::table('admins')->get();
        $data['users'] = DB::table('users')->get();

        $dataCheck = DB::table('tasks')
            ->select([
                'tasks.*',
                DB::raw('(SELECT tl_name FROM tasks_level WHERE tasks_level.tl_id = tasks.task_level) as task_level_title'),
                DB::raw('(SELECT ts_name FROM tasks_status WHERE tasks_status.ts_id = tasks.task_status) as task_status_name'),
                //category name
                DB::raw('(SELECT name FROM minutes_category WHERE minutes_category.id = tasks.category) as category_name')
            ])
            ->where('task_id', $id)
            ->where('task_receiver_id',$userId)
            ->where('receiver_type',$userType)
            ->where('receiver_department',$userDepartment)
            ->first();
        
        $dataCheckTwo = DB::table('tasks')
            ->select([
                'tasks.*',
                DB::raw('(SELECT tl_name FROM tasks_level WHERE tasks_level.tl_id = tasks.task_level) as task_level_title'),
                DB::raw('(SELECT ts_name FROM tasks_status WHERE tasks_status.ts_id = tasks.task_status) as task_status_name'),
                //category name
                DB::raw('(SELECT name FROM minutes_category WHERE minutes_category.id = tasks.category) as category_name')
            ])
            ->where('task_id', $id)
            ->where('task_publisher_id',$userId)
            ->where('publisher_type',$userType)
            ->where('publisher_department',$userDepartment)
            ->first();

        if (isset($dataCheck)) {
            $data['taskData'] = $dataCheck;
        }elseif(isset($dataCheckTwo)){
            $data['taskData'] = $dataCheckTwo;
        }else{
            return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
        }

        //other task
        $data['countData'] = DB::table('tasks')
            ->where('task_publisher_id',$userId)
            ->where('publisher_type',$userType)
            ->orWhere('task_receiver_id',$userId)
            ->where('receiver_type',$userType)
            ->count();

        ###get task by publisher
        $dataPublisher = DB::table('tasks')
            ->where('task_id','!=',$id)
            ->where([
                ['task_publisher_id',$userId],
                ['publisher_type',$userType]
            ])
            ->orderBy('task_id','DESC')
            ->limit(5)
            ->get();

        ###get task by receiver
        $dataReceiver = DB::table('tasks')
            ->where('task_id','!=',$id)
            ->where([
                ['task_receiver_id',$userId],
                ['receiver_type',$userType]
            ])
            ->orderBy('task_id','DESC')
            ->limit(5)
            ->get();

        ###emerging tasks
        $data['otherTasks'] = $dataPublisher->merge($dataReceiver);

        //other data
        $data['dataComments'] = DB::table('tasks_comments as tc')
            ->select([
                'tc.*',
                DB::raw('(SELECT COUNT(*) FROM tasks_comments_files WHERE tasks_comments_files.comment_id = tc.tc_id) countFiles')
            ])
            ->where('tc_task_id', $id)
            ->orderBy('tc_id','DESC')
            ->get();

        ###comment count
        $data['countComments'] = DB::table('tasks_comments')
            ->where('tc_task_id', $id)
            ->count();
        
        //task comment files
        $data['commentFiles'] = DB::table('tasks_comments_files')->get();
        
        return view('user.task.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        //key data
        $data['currentUserId'] = Auth::user()->id;
        $data['currentUserType'] = Auth::user()->user_type;
        $data['taskPriorities'] = DB::table('tasks_level')->get();
        $data['userTypes'] = DB::table('user_type')->get();

        ###task data
        $data['taskData'] = DB::table('tasks')
            ->select([
                'tasks.*',
                DB::raw('(SELECT tl_name FROM tasks_level WHERE tasks_level.tl_id = tasks.task_level) as task_level_title'),
                DB::raw('(SELECT ts_name FROM tasks_status WHERE tasks_status.ts_id = tasks.task_status) as pengiriman_status'),
            ])
            ->where('task_id', $id)
            ->where('task_publisher_id',$userId)
            ->where('publisher_department',$userDepartment)
            ->first();

        $taskData = $data['taskData'];
        
        if (!isset($taskData)) {
            return redirect()->back()->with('alert-danger','Maaf, halaman yang Anda tuju tidak tersedia.');
        }
        
        if ($taskData->receiver_type == 'admin') {
            $data['dataReceiver'] = DB::table('admins')->where('id',$taskData->task_receiver_id)->first();
        }else{
            $data['dataReceiver'] = DB::table('users')->where('id',$taskData->task_receiver_id)->first();
        }
        
        return view('user.task.edit', $data);
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

        if (isset($request->status)) {
            $dataReceiverCheck = DB::table('tasks')->where('task_id',$id)->where('task_receiver_id',$userId)->where('receiver_type',$userType)->where('receiver_department',$userDepartment)->first();

            $dataPublisherCheck = DB::table('tasks')->where('task_id',$id)->where('task_publisher_id',$userId)->where('publisher_type',$userType)->where('publisher_department',$userDepartment)->first();

            if (!isset($dataReceiverCheck) && !isset($dataPublisherCheck)) {
                return redirect()->back()->with('alert-danger','Maaf, Anda tidak diijinkan mengubah data.');
            }elseif(isset($dataReceiverCheck)){
                $dataCheck = $dataReceiverCheck;
                //update status
                $data['task_status'] = $request->status;
                DB::table('tasks')->where('task_id',$id)->update($data);

                //sent notifications
                    $statusData = DB::table('tasks_status')->where('ts_id', $request->status)->first();
                    $statusName = $statusData->ts_name;
                    $taskName = $dataCheck->task_title;
                    ###publisher id & type
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
                    $dataNotif['receiver_id'] = $dataCheck->task_publisher_id;
                    $dataNotif['receiver_type'] = $dataCheck->publisher_type;
                    $dataNotif['receiver_department'] = $userDepartment;
                    $dataNotif['level'] = $dataCheck->task_level;
                    ###notif message
                    $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengupdate status tugas <strong>".$taskName."</strong> menjadi <strong>".ucwords($statusName)."</strong>";
                    ###insert data to notifications table
                    $notifData = DB::table('notifications')->insert($dataNotif);
                //sent notifications end
            }else{
                $dataCheck = $dataPublisherCheck;
                //update status
                $data['task_status'] = $request->status;
                DB::table('tasks')->where('task_id',$id)->update($data);

                //sent notifications
                    $statusData = DB::table('tasks_status')->where('ts_id', $request->status)->first();
                    $statusName = $statusData->ts_name;
                    $taskName = $dataCheck->task_title;
                    ###publisher id & type
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
                    $dataNotif['receiver_id'] = $dataCheck->task_receiver_id;
                    $dataNotif['receiver_type'] = $dataCheck->receiver_type;
                    $dataNotif['receiver_department'] = $dataCheck->receiver_department;;
                    $dataNotif['level'] = $dataCheck->task_level;
                    ###notif message
                    $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengupdate status tugas <strong>".$taskName."</strong> menjadi <strong>".ucwords($statusName)."</strong>";
                    ###insert data to notifications table
                    $notifData = DB::table('notifications')->insert($dataNotif);
                //sent notifications end
            }
            return redirect()->route('task-user.index')->with('alert-success','Status tugas berhasil diperbarui.');
        }else{
            $dataCheck = DB::table('tasks')->where('task_id',$id)->where('task_publisher_id',$userId)->where('publisher_type',$userType)->where('receiver_department',$userDepartment)->first();

            if (!isset($dataCheck)) {
                return redirect()->back()->with('alert-danger','Maaf, Anda tidak diijinkan mengubah data.');
            }

            $request['task_date'] = Carbon::createFromFormat('Y-m-d', $request->task_date)->format('Y-m-d H:i:s');
            $request['task_due_date'] = Carbon::createFromFormat('Y-m-d', $request->task_due_date)->format('Y-m-d H:i:s');

            $data = $request->except(['_token','_method','submit']);
            DB::table('tasks')->where('task_id',$id)->update($data);

            return redirect()->route('task-user.index')->with('alert-success','Data tugas berhasil diperbarui.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task.');
    }
}
