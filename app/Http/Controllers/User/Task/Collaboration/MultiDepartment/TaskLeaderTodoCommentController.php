<?php

namespace App\Http\Controllers\User\Task\Collaboration\MultiDepartment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class TaskLeaderTodoCommentController extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Leader Todo Comments.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Leader Todo Comments.');
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

        //data validation
        $request->validate([
            'comment' => 'required|min:10'
        ]);

        //customize the data
        $data = $request->except('_token','submit','title','publisher_department','level');
        $data['publisher_id'] = $userId;
        $data['publisher_type'] = $userType;
        $data['date'] = Carbon::now()->format('Y-m-d H:i:s');
        $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('tasks_leaders_todos_comments')->insert($data);

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

            $dataNotif['level'] = $request->level;
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
                    $dataNotif['desc'] = "<strong>".$publisherName."</strong> memberikan komentar pada cheklist <a href='".route('task-leaders-todo.show',$theId.'?tid='.$theTaskId.'&did='.$theDeptId)."'><strong>".ucfirst($theTitle)."</strong></a>.";

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
                        $dataNotif['desc'] = "<strong>".$publisherName."</strong> memberikan komentar pada cheklist <a href='".route('user-task-leaders-todo.show',$theId.'?tid='.$theTaskId.'&did='.$theDeptId)."'><strong>".ucfirst($theTitle)."</strong></a>.";
                        ###insert data to notifications table
                        $notifData = DB::table('notifications')->insert($dataNotif);
                    }
                }
            }
        //sent notifications end

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Leader Todo Comments.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Leader Todo Comments.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Leader Todo Comments.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Leader Todo Comments.');
    }
}
