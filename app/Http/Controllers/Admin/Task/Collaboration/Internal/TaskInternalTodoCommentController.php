<?php

namespace App\Http\Controllers\Admin\Task\Collaboration\Internal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class TaskInternalTodoCommentController extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Komentar To do Task Kolaborasi Internal Departemen.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Komentar To do Task Kolaborasi Internal Departemen.');
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
        $data = $request->except('_token','submit','title','level');
        $data['publisher_id'] = $userId;
        $data['publisher_type'] = $userType;
        $data['date'] = Carbon::now()->format('Y-m-d H:i:s');
        $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('tasks_internal_todos_comments')->insert($data);

        //sent notifications
            $dataTaskinternal = DB::table('tasks_internal_todos')->where('id',$todoId)->first();
            $theId = $dataTaskinternal->id;
            $theTaskId = $dataTaskinternal->task_id;
            $theDeptId = $dataTaskinternal->department_id;
            $theTitle = $dataTaskinternal->name;

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
            //staff / pic
            $receiverId = $dataTaskinternal->receiver_id;
            $dataNotif['receiver_id'] = $receiverId;
            $dataNotif['receiver_type'] = 'user';
            $dataNotif['receiver_department'] = $userDepartment;

            $dataNotif['level'] = 1;

            ###notif message
            $dataNotif['desc'] = "<strong>".$publisherName."</strong> memberikan komentar pada cheklist <a href='".route('user-task-internal-todo.show',$theId.'?tid='.$theTaskId.'&did='.$theDeptId)."'><strong>".ucfirst($theTitle)."</strong></a>.";
            ###insert data to notifications table
            $notifData = DB::table('notifications')->insert($dataNotif);
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Komentar To do Task Kolaborasi Internal Departemen.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Komentar To do Task Kolaborasi Internal Departemen.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Komentar To do Task Kolaborasi Internal Departemen.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Komentar To do Task Kolaborasi Internal Departemen.');
    }
}
