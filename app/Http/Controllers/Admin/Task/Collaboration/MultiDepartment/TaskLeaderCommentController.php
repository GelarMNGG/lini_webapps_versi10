<?php

namespace App\Http\Controllers\Admin\Task\Collaboration\MultiDepartment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class TaskLeaderCommentController extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Komentar Task Kolaborasi Multi Departemen.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Komentar Task Kolaborasi Multi Departemen.');
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
            'comment' => 'required|min:10'
        ]);

        //customize the data
        $data = $request->except('_token','submit','receiver_department','title','publisher_department','level');
        $data['publisher_id'] = $userId;
        $data['publisher_type'] = $userType;
        $data['receiver_type'] = $userType;
        $data['date'] = Carbon::now()->format('Y-m-d H:i:s');
        $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('tasks_leaders_comments')->insert($data);

        //sent notifications
            $theId = $request->task_id;
            $theDepartment = $request->department_id;
            $pubDepartment = $request->publisher_department;
            $theName = $request->title;
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
            $receiverDepartments = unserialize($request->receiver_department);
            $collaboratorDatas = DB::table('admins')->get();
            foreach ($collaboratorDatas as $dataAlpha) {
                if (in_array($dataAlpha->department_id,$receiverDepartments)) {
                    $dataNotif['receiver_id'] = $dataAlpha->id;
                    $dataNotif['receiver_type'] = $dataAlpha->user_type;
                    $dataNotif['receiver_department'] = $dataAlpha->department_id;

                    ###notif message
                    $dataNotif['desc'] = "<strong>".$publisherName."</strong> memberikan komentar pada task <a href='".route('task-leaders.show',$theId)."'><strong>".ucfirst($theName)."</strong></a>.";
                    
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
                        $dataNotif['desc'] = "<strong>".$publisherName."</strong> memberikan komentar pada task <a href='".route('user-task-leaders.show',$theId)."'><strong>".ucfirst($theName)."</strong></a>.";
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Komentar Task Kolaborasi Multi Departemen.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Komentar Task Kolaborasi Multi Departemen.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Komentar Task Kolaborasi Multi Departemen.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Komentar Task Kolaborasi Multi Departemen.');
    }
}
