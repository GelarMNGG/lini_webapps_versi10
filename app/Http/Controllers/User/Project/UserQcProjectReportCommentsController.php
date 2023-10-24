<?php

namespace App\Http\Controllers\User\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;

class UserQcProjectReportCommentsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth' => 'verified']);
    }

    /**
     * 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect()->back()->with('alert-danger','Terjadi kesalahan jaringan, silahkan mencoba beberapa saat lagi.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Terjadi kesalahan input, silahkan mencoba beberapa saat lagi.');
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
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        //data validation
        $request->validate([
            'comment' => 'required|min:10|max:255'
        ]);

        $projectId = $request->project_id;
        $taskId = $request->task_id;
        $praId = $request->pra_id;

        //user type
        $projectDepartment = 1;
        $user = 'user';
        $qcDoc = 4;
        $pm = 3;

        //privilege check
        if ($userType == $user) {
            if ($userLevel == $qcDoc) {
                $privilegeCheck = DB::table('projects_task as pt')->where('project_id',$projectId)->where('id',$taskId)->where('qcd_id',$userId)->where('deleted_at',null)->count();
                //allowed id
                    $receiverIdData = DB::table('projects as pt')->select('pm_id')->where('id',$projectId)->where('deleted_at',null)->first();
                    $receiverId = $receiverIdData->pm_id;
            }elseif($userLevel == $pm){
                $privilegeCheck = DB::table('projects as pt')->where('id',$projectId)->where('pm_id',$userId)->where('deleted_at',null)->count();
                //allowed id
                    $receiverIdData = DB::table('projects_task as pt')->select('qcd_id')->where('project_id',$projectId)->where('id',$taskId)->where('deleted_at',null)->first();
                    $receiverId = $receiverIdData->qcd_id;
            }else{
                $privilegeCheck = 0;
            }
        }

        if ($privilegeCheck > 0) {
            //customize the data
                $data = $request->except('_token','submit','task_title');
                $data['publisher_id'] = $userId;
                $data['publisher_type'] = $userType;
                $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
            //insert to database
                DB::table('project_report_all_comments')->insert($data);
            //sent notifications
                $taskId = $request->task_id;
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
                ###user level receiver
                    if ($userId != $receiverId && $userType == $request->receiver_type && $userDepartment == $projectDepartment) {
                        $receiverType = 'user';
                        $dataNotif['receiver_id'] = $receiverId;
                        $dataNotif['receiver_type'] = $userType;
                        $dataNotif['receiver_department'] = $userDepartment;
                    }
                    $dataNotif['level'] = 1;
                    ###notif message
                        $dataNotif['desc'] = "<strong>".$publisherName."</strong> memberikan komentar pada laporan untuk task <a href='".route('user-projects-report-qc.show',$taskId.'?project_id='.$projectId)."'><strong>".ucfirst($taskName)."</strong></a> untuk Anda.</strong>";
                    ###insert data to notifications table
                        $notifData = DB::table('notifications')->insert($dataNotif);
                ###send notif to admin
                    $lini = 1;
                    $adminData = DB::table('admins')->select('id')->where('company_id',$lini)->where('department_id',$projectDepartment)->first();
                    $dataNotif['receiver_id'] = $adminData->id;
                    $dataNotif['receiver_type'] = 'admin';
                    $dataNotif['receiver_department'] = $projectDepartment;
                    $dataNotif['level'] = 1;
                    ###notif message
                        $dataNotif['desc'] = "<strong>".$publisherName."</strong> memberikan komentar pada laporan untuk task <a href='".route('admin-projects-report-qc.show',$taskId.'?project_id='.$projectId)."'><strong>".ucfirst($taskName)."</strong></a> untuk Anda.</strong>";
                    ###insert data to notifications table
                        $notifData = DB::table('notifications')->insert($dataNotif);
            //sent notifications end
            return redirect()->back()->with('alert-success','Komentar berhasil dikirimkan.');
        }

        return redirect()->back()->with('alert-danger','Terjadi kesalahan input, silahkan mencoba beberapa saat lagi.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Terjadi kesalahan input, silahkan mencoba beberapa saat lagi.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Terjadi kesalahan input, silahkan mencoba beberapa saat lagi.');
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
        return redirect()->back()->with('alert-danger','Terjadi kesalahan input, silahkan mencoba beberapa saat lagi.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Terjadi kesalahan input, silahkan mencoba beberapa saat lagi.');
    }
}
