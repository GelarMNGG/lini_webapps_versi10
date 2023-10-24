<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class UserProjectToolController extends Controller
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
    public function index(Request $request)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userLevel = Auth::user()->user_level;

        $projectId = $request->project_id;
        $taskId = $request->task_id;

        if ($userLevel != 5) {
            return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Tool Report.');
        }

        //check priviledge
        $dataCountCheck = DB::table('projects_task')->where('id',$taskId)->where('qct_id',$userId)->where('deleted_at',null)->first();

        $data['dataFinishedCount'] = DB::table('project_tools')->where('task_id',$taskId)->where('publisher_id',$dataCountCheck->tech_id)->where('status',3)->where('report_submitted',null)->where('deleted_at',null)->count();
        $data['dataReportCount'] = DB::table('project_tools_report')->where('task_id',$taskId)->where('publisher_id',$dataCountCheck->tech_id)->count();
        
        if (isset($dataCountCheck)) {
            //getting data
            $data['dataTools'] = DB::table('project_tools as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT name FROM project_tools_report_status WHERE project_tools_report_status.id = pt.status) as status_name')
            ])
            ->where('task_id',$taskId)
            ->where('publisher_id',$dataCountCheck->tech_id)->paginate(10);

            $data['projectTask'] = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
            ])
            ->where('id',$taskId)->where('qct_id',$userId)->where('deleted_at',null)->first();

            if (!isset($data['projectTask'])) {
                return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Tool Report.');
            }

            return view('user.project.tools.index', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Tool Report.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Tool Report.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Tool Report.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Tool Report.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Tool Report.');
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
        $userDepartment = Auth::user()->department_id;
        
        $userFirstname = Auth::user()->firstname;
        $userLastname = Auth::user()->lastname;

        $projectId = $request->project_id;
        $taskId = $request->task_id;

        if ($userLevel != 5) {
            return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Tool Report.');
        }

        //check priviledge
        $dataCountCheck = DB::table('projects_task')->where('id',$taskId)->where('qct_id',$userId)->where('deleted_at',null)->first();

        ### approve report
        if ($request->approve_report != null) {
            
            $data = $request->except(['_token','_method','submit','approve_report','project_id','task_id']);
            $data['approved_at'] = Carbon::now()->format('Y-m-d H:i:s');
            $data['approver_id'] = $userId;
            $data['approver_type'] = $userType;
            
            DB::table('project_tools_report')->where('id', $id)->update($data);
            
            ###create project log
                $dataLog['project_id'] = $projectId;
                $dataLog['task_id'] = $taskId;
                $dataLog['name'] = ucwords($userFirstname).' '.ucwords($userLastname).' menyetujui laporan pengembalian alat Anda.';
                $dataLog['publisher_id'] = $userId;
                $dataLog['publisher_type'] = $userId;

                DB::table('projects_log')->insert($dataLog);
            ###create project log

            ###send notifications
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
                $dataNotif['receiver_id'] = $dataCountCheck->tech_id;
                $dataNotif['receiver_type'] = 'tech';
                $dataNotif['receiver_department'] = $userDepartment;
                ###notif message
                $dataNotif['desc'] = "<strong>".$publisherName."</strong> menyetujui laporan pengembalian alat Anda.</strong>";
                ###insert data to notifications table
                $notifData = DB::table('notifications')->insert($dataNotif);
            ###send notifications end

            return redirect()->back()->with('alert-success','Laporan pengembalian alat telah berhasil Anda setujui.');
        }
        ### approve report
        
        if (isset($dataCountCheck)) {

            $data = $request->except(['_token','_method','submit','tool_name']);
            $data['request_approved'] = Carbon::now()->format('Y-m-d H:i:s');
            $data['request_approver_id'] = $userId;
            $data['request_approver_type'] = $userType;

            DB::table('project_tools')->where('id', $id)->update($data);

            ###create project log
                $dataLog['project_id'] = $projectId;
                $dataLog['task_id'] = $taskId;
                $dataLog['name'] = ucwords($userFirstname).' '.ucwords($userLastname).' menyetujui permohonan peminjaman alat'.ucwords($request->tool_name).'.';
                $dataLog['publisher_id'] = $userId;
                $dataLog['publisher_type'] = $userId;

                DB::table('projects_log')->insert($dataLog);
            ###create project log

            ###send notifications
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
                $dataNotif['receiver_id'] = $dataCountCheck->tech_id;
                $dataNotif['receiver_type'] = 'tech';
                $dataNotif['receiver_department'] = $userDepartment;
                ###notif message
                $dataNotif['desc'] = "<strong>".$publisherName."</strong> menyetujui permohonan peminjaman alat <strong> ".ucwords($request->tool_name)."</strong> Anda.</strong>";
                ###insert data to notifications table
                $notifData = DB::table('notifications')->insert($dataNotif);
            ###send notifications end

            return redirect()->back()->with('alert-success','Data berhasil diubah');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Tool Report.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Tool Report.');
    }

    //customize
    public function report(Request $request)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;

        $projectId = $request->project_id;
        $taskId = $request->task_id;

        $approverDepartment = 4; //general affair
        $toolStatus = 3;

        //firstcheck
        $firstCheck = DB::table('projects_task')->where('id',$taskId)->where('project_id',$projectId)->where('qct_id',$userId)->first();

        if (!isset($firstCheck)) {
            return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tools.');
        }

        //check priviledge
        $dataCountCheck = DB::table('project_tools')->where('status',$toolStatus)->where('publisher_id',$firstCheck->tech_id)->count();
        
        if ($dataCountCheck > 0) {
            //getting the data
            $data['projectTask'] = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
            ])
            ->where('id',$taskId)->where('tech_id',$firstCheck->tech_id)->where('deleted_at',null)->first();
            
            $data['dataReportTools'] = DB::table('project_tools')
            ->where('project_id',$projectId)
            ->where('task_id',$taskId)
            ->where('status',$toolStatus)->where('publisher_id',$firstCheck->tech_id)->get();

            $data['userProfile'] = DB::table('techs')->where('id',$firstCheck->tech_id)->first();
            $data['approverProfile'] = DB::table('admins')->where('department_id',$approverDepartment)->first();
            $data['dataReportCount'] = DB::table('project_tools_report')->where('project_id',$projectId)->where('task_id',$taskId)->where('publisher_id',$firstCheck->tech_id)->first();

            return view('user.project.tools.report', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Tools.');
    }
}
