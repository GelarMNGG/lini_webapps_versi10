<?php

namespace App\Http\Controllers\Tech;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;

class TechReportTextCommentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:tech');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Text Comments.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Text Comments.');
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

        $projectId = $request->project_id;
        $taskId = $request->task_id;
        $templateId = $request->template_id; //category
        $subcatId = $request->subcat_id;
        $projectDepartment = 1; //project department
        //check priviledge & getting the data
            $priviledgeCheck = DB::table('projects_task as pt')
            ->select([
                'pt.project_id',
                'pt.qcd_id',
                DB::raw('(SELECT pm_id FROM projects WHERE projects.id = pt.project_id) as pm_id'),
            ])
            ->where('project_id',$projectId)->where('tech_id',$userId)->first();

        //first check
        if (!isset($priviledgeCheck)) {
            return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
        }
        //secondcheck
            $dataCheck = DB::table('project_report_text')->where('project_id',$projectId)->where('task_id',$taskId)->where('template_id',$templateId)->where('subcat_id',$subcatId)->count();
        //second check
        if ($dataCheck > 0) {
            //setting up datas
                $data = $request->except(['_token','submit','project_name','task_title']);
                $data['publisher_id'] = $userId;
                $data['publisher_type'] = $userType;
            //insert to database
                DB::table('project_report_text_comments')->insert($data);
            //sent notifications
                $projectName = $request->project_name;
                $taskId = $request->task_id;
                $taskName = $request->task_title;
                $pmId = $priviledgeCheck->pm_id;
                $qcDocument = $priviledgeCheck->qcd_id;
                ###publisher id & type
                $dataNotif['publisher_id'] = $userId;
                $dataNotif['publisher_type'] = $userType;
                $dataNotif['publisher_department'] = $projectDepartment;
                $publisherName = Auth::user()->name;
                $publisherFirstname = Auth::user()->firstname;
                $publisherLastname = Auth::user()->lastname;
                if ($publisherFirstname !== null) {
                    $publisherName = ucfirst($publisherFirstname).' '.ucfirst($publisherLastname);
                }
                ###send notif to qcd and pm
                    $receiverType = 'user';
                    $dataNotif['receiver_type'] = $receiverType;
                    $dataNotif['receiver_department'] = $projectDepartment;
                    $dataNotif['level'] = 1;
                    ###notif message
                        $dataNotif['desc'] = "<strong>".$publisherName."</strong> memberikan komentar pada laporan untuk task <a href='".route('user-projects-report-qc.show',$taskId.'?project_id='.$projectId)."'><strong>".strtoupper($projectName)." - ".ucfirst($taskName)."</strong></a> untuk Anda.</strong>";
                    ###insert data to notifications table
                        if (isset($pmId)) {
                            $dataNotif['receiver_id'] = $pmId;
                            $notifData = DB::table('notifications')->insert($dataNotif);
                        }
                        if (isset($qcDocument)) {
                            $dataNotif['receiver_id'] = $qcDocument;
                            $notifData = DB::table('notifications')->insert($dataNotif);
                        }
                ###send notif to admin
                    $lini = 1;
                    $adminData = DB::table('admins')->select('id')->where('company_id',$lini)->where('department_id',$projectDepartment)->first();
                    $dataNotif['receiver_id'] = $adminData->id;
                    $dataNotif['receiver_type'] = 'admin';
                    $dataNotif['receiver_department'] = $projectDepartment;
                    $dataNotif['level'] = 1;
                    ###notif message
                        $dataNotif['desc'] = "<strong>".$publisherName."</strong> memberikan komentar pada laporan untuk task <a href='".route('admin-projects-report-qc.show',$taskId.'?project_id='.$projectId)."'><strong>".strtoupper($projectName)." - ".ucfirst($taskName)."</strong></a> untuk Anda.</strong>";
                    ###insert data to notifications table
                        $notifData = DB::table('notifications')->insert($dataNotif);
            //sent notifications end
            //redirect back
                return redirect()->back()->with('alert-success','Komentar berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Text Comments.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Text Comments.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Text Comments.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Text Comments.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Text Comments.');
    }
}
