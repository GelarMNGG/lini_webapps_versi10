<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class TaskUserCommentController extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Comments.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Comments.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //general data
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        //data validation
        $request->validate([
            'tc_comment' => 'required|min:10'
        ]);

        //customize the data
        $data = $request->except('_token','submit','receiver_department','task_title','publisher_department');
        $data['tc_publisher_id'] = $userId;
        $data['publisher_type'] = $userType;
        $data['tc_date'] = Carbon::now()->format('Y-m-d H:i:s');
        $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');

        if ($request->publisher_type == $userType) {
            $data['tc_receiver_id'] = $request->tc_receiver_id;
            $data['receiver_type'] = $request->receiver_type;
        }else{
            $data['tc_receiver_id'] = $request->tc_publisher_id;
            $data['receiver_type'] = $request->publisher_type;
        }

        DB::table('tasks_comments')->insert($data);

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
                $dataNotif['desc'] = "<strong>".$publisherName."</strong> memberikan komentar pada task <a href='".route('task.show',$taskId)."'><strong>".ucfirst($taskName)."</strong></a> untuk Anda.</strong>";
            }else{
                $dataNotif['desc'] = "<strong>".$publisherName."</strong> memberikan komentar pada task <a href='".route('task-user.show',$taskId)."'><strong>".ucfirst($taskName)."</strong></a> untuk Anda.</strong>";
            }
            ###insert data to notifications table
            $notifData = DB::table('notifications')->insert($dataNotif);
        //sent notifications end

        return redirect()->back()->with('alert-success','Komentar berhasil dikirimkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Comments.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Comments.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Comments.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Comments.');
    }
}
