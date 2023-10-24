<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class UserProjectTaskController extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Project.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Project.');
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
        $userDepartment = Auth::user()->department_id;

        $projectId = $request->project_id;

        //first check
        if ($userLevel == 3 && $userDepartment == 1) {

            //validation
            $request->validate([
                'name' => 'required|unique:projects_task,name,'.$request->name,
                'number' => 'required|unique:projects_task,number,'.$request->number
            ]);

            $data = $request->except(['_token', 'submit']);
            $data['pm_id'] = $userId;
            $data['publisher_id'] = $userId;
            $data['publisher_type'] = $userType;
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');

            DB::table('projects_task')->insert($data);

            //insert log & send notifications
                $dataProjectTask = DB::table('projects_task')->orderBy('id','DESC')->first();
                $dataProject = DB::table('projects')->where('id',$projectId)->first();
                ###publisher data
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
                ###receiver data
                $logName = $request->name;
                $dataReceiverDepartment = DB::table('admins')->where('department_id',$userDepartment)->first();
                $dataNotif['receiver_id'] = $dataReceiverDepartment->id;
                $dataNotif['receiver_type'] = 'admin';
                $dataNotif['receiver_department'] = $userDepartment;
                $dataNotif['level'] = 1;

                ###logging
                $dataLog['project_id'] = $dataProject->id;
                $dataLog['name'] = "Pembuatan task <strong>".ucfirst($logName)."</strong>";
                $dataLog['publisher_id'] = $userId;
                $dataLog['publisher_type'] = $userType;
                DB::table('projects_log')->insert($dataLog);

                ###notif message
                $dataNotif['desc'] = "Membuat task <a href='".route('admin-projects-task.show',$dataProjectTask->id)."'><strong>".ucfirst($logName)."</strong></a> pada proyek <a href='".route('admin-projects.show',$dataProject->id)."'><strong>".ucfirst($dataProject->name)."</strong></a>";

                ###insert data to notifications table
                DB::table('notifications')->insert($dataNotif);
            //insert log & send notifications end

            return redirect()->route('user-projects.show', $projectId)->with('alert-success','Data berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Project.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Project.');
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
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;
        $pcLevel = 2;
        $qcdLevel = 4; //QC Document
        $qctLevel = 5; //QC Tools
        $qceLevel = 6; //QC Expenses

        //first check
        if($userLevel == 3 || $userLevel == 22 && $userDepartment == 1){

            $data['userDepartment'] = $userDepartment;
            $data['dataTaskStatus'] = DB::table('projects_task_status')->get();

            $data['taskData'] = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name'),
                DB::raw('(SELECT pm_id FROM projects WHERE projects.id = pt.project_id) as pm_id'),
            ])
            ->where('id',$id)->first();

            $pmId = $data['taskData']->pm_id;

            //data user
            $data['dataPM'] = DB::table('users')->where('id',$pmId)->first();
            $data['dataPCs'] = DB::table('users')->where('department_id', $userDepartment)->where('user_level',$pcLevel)->get();
            $data['dataQCDs'] = DB::table('users')->where('department_id', $userDepartment)->where('user_level',$qcdLevel)->get();
            $data['dataQCTs'] = DB::table('users')->where('department_id', $userDepartment)->where('user_level',$qctLevel)->get();
            $data['dataQCEs'] = DB::table('users')->where('department_id', $userDepartment)->where('user_level',$qceLevel)->get();

            $data['dataTechs'] = DB::table('techs')->get();

            //second check
            if ($userLevel != 22) {
                $secondCheck = DB::table('projects_task')->where('id',$id)->where('pm_id',$userId)->count();
                
                if ($secondCheck < 1) {
                    return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
                }

                return view('user.project.task.edit', $data);
            }

            return view('user.project.task.edit-co-admin', $data);

        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Project.');
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
        $userLevel = Auth::user()->user_level;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        $projectId = $request->project_id;
        $projectStatus = $request->status;
        $updateTaskStatus = $request->update_status_task;

        //userlevel 2 = pc
        //userlevel 22 = co admin

        //first check
        if ($userDepartment == 1) {
            //insert log & send notifications
                $dataProjectTask = DB::table('projects_task')->where('id',$id)->first();
                $dataProject = DB::table('projects')->where('id',$projectId)->first();
                ###publisher data
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

                ###PC amendment
                    $oldPCId = $dataProjectTask->pc_id;
                    $newPCId = $request->pc_id;

                    $teamTitle = "Project Coordinator";
                    if ($newPCId != 0 && $oldPCId != $newPCId) {
                        ###notification data for PM
                        $dataNotifPC['publisher_id'] = $userId;
                        $dataNotifPC['publisher_type'] = $userType;
                        $dataNotifPC['publisher_department'] = $userDepartment;

                        $dataNotifPC['receiver_id'] = $newPCId;
                        $dataNotifPC['receiver_type'] = $userType;
                        $dataNotifPC['receiver_department'] = $userDepartment;
                        $dataNotifPC['level'] = 1;

                        ###notif message
                        $dataNotifPC['desc'] = "Menunjuk Anda sebagai <strong>".ucwords($teamTitle)."</strong> pada <a href='".route('user-projects-task.show',$dataProjectTask->id)."'><strong>".ucfirst($dataProjectTask->name)."</strong></a>.";
                        ###insert data to notifications table for pm
                        DB::table('notifications')->insert($dataNotifPC);

                        ###logging
                        $dataTeam = DB::table('users')->where('id',$newPCId)->first();
                        $dataLogPC['project_id'] = $dataProject->id;
                        $dataLogPC['name'] = "Penunjukan <strong>".ucfirst($dataTeam->firstname)." ".ucfirst($dataTeam->lastname)."</strong> sebagai <strong>".ucwords($teamTitle)."</strong> yang baru.";
                        $dataLogPC['publisher_id'] = $userId;
                        $dataLogPC['publisher_type'] = $userType;
                        DB::table('projects_log')->insert($dataLogPC);
                        
                        ###admin notification
                            $dataReceiverDepartment = DB::table('admins')->where('department_id',$userDepartment)->first();
                            $dataNotif['receiver_id'] = $dataReceiverDepartment->id;
                            $dataNotif['receiver_type'] = 'admin';
                            $dataNotif['receiver_department'] = $userDepartment;
                            $dataNotif['level'] = 1;
        
                            ###notif message
                            $dataNotif['desc'] = "Menunjuk <strong>".ucfirst($dataTeam->firstname)." ".ucfirst($dataTeam->lastname)."</strong> sebagai <strong>".ucwords($teamTitle)."</strong> yang baru pada task <a href='".route('admin-projects-task.show',$dataProjectTask->id)."'><strong>".ucfirst($dataProjectTask->name)."</strong></a> proyek <a href='".route('admin-projects.show',$dataProject->id)."'><strong>".ucfirst($dataProject->name)."</strong></a>.";
        
                            ###insert data to notifications table
                            DB::table('notifications')->insert($dataNotif);
                        ###admin notification end
                    }
                ###PC data end
                ###QCD amendment
                    $oldQCDid = $dataProjectTask->qcd_id;
                    $newQCDid = $request->qcd_id;
                    $teamTitle = "QC document";
                    if ($newQCDid != 0 && $oldQCDid != $newQCDid) {
                        ###notification data for PM
                        $dataNotifQCD['publisher_id'] = $userId;
                        $dataNotifQCD['publisher_type'] = $userType;
                        $dataNotifQCD['publisher_department'] = $userDepartment;

                        $dataNotifQCD['receiver_id'] = $newQCDid;
                        $dataNotifQCD['receiver_type'] = $userType;
                        $dataNotifQCD['receiver_department'] = $userDepartment;
                        $dataNotifQCD['level'] = 1;

                        ###notif message
                        $dataNotifQCD['desc'] = "Menunjuk Anda sebagai <strong>".ucwords($teamTitle)."</strong> pada <a href='".route('user-projects-task.show',$dataProjectTask->id)."'><strong>".ucfirst($dataProjectTask->name)."</strong></a>.";
                        ###insert data to notifications table for pm
                        DB::table('notifications')->insert($dataNotifQCD);

                        ###logging
                        $dataTeam = DB::table('users')->where('id',$newQCDid)->first();
                        $dataLogQCD['project_id'] = $dataProject->id;
                        $dataLogQCD['name'] = "Penunjukan <strong>".ucfirst($dataTeam->firstname)." ".ucfirst($dataTeam->lastname)."</strong> sebagai <strong>".ucwords($teamTitle)."</strong> yang baru.";
                        $dataLogQCD['publisher_id'] = $userId;
                        $dataLogQCD['publisher_type'] = $userType;
                        DB::table('projects_log')->insert($dataLogQCD);
                        
                        ###admin notification
                            $dataReceiverDepartment = DB::table('admins')->where('department_id',$userDepartment)->first();
                            $dataNotif['receiver_id'] = $dataReceiverDepartment->id;
                            $dataNotif['receiver_type'] = 'admin';
                            $dataNotif['receiver_department'] = $userDepartment;
                            $dataNotif['level'] = 1;
        
                            ###notif message
                            $dataNotif['desc'] = "Menunjuk <strong>".ucfirst($dataTeam->firstname)." ".ucfirst($dataTeam->lastname)."</strong> sebagai <strong>".ucwords($teamTitle)."</strong> yang baru pada task <a href='".route('admin-projects-task.show',$dataProjectTask->id)."'><strong>".ucfirst($dataProjectTask->name)."</strong></a> proyek <a href='".route('admin-projects.show',$dataProject->id)."'><strong>".ucfirst($dataProject->name)."</strong></a>.";
        
                            ###insert data to notifications table
                            DB::table('notifications')->insert($dataNotif);
                        ###admin notification end
                    }
                ###QCD data end
                ###QCT amendment
                    $oldQCTid = $dataProjectTask->qct_id;
                    $newQCTid = $request->qct_id;
                    $teamTitle = "QC tools";
                    if ($newQCTid != 0 && $oldQCTid != $newQCTid) {
                        ###notification data for PM
                        $dataNotifQCT['publisher_id'] = $userId;
                        $dataNotifQCT['publisher_type'] = $userType;
                        $dataNotifQCT['publisher_department'] = $userDepartment;

                        $dataNotifQCT['receiver_id'] = $newQCTid;
                        $dataNotifQCT['receiver_type'] = $userType;
                        $dataNotifQCT['receiver_department'] = $userDepartment;
                        $dataNotifQCT['level'] = 1;

                        ###notif message
                        $dataNotifQCT['desc'] = "Menunjuk Anda sebagai <strong>".ucwords($teamTitle)."</strong> pada <a href='".route('user-projects-task.show',$dataProjectTask->id)."'><strong>".ucfirst($dataProjectTask->name)."</strong></a>.";
                        ###insert data to notifications table for pm
                        DB::table('notifications')->insert($dataNotifQCT);

                        ###logging
                        $dataTeam = DB::table('users')->where('id',$newQCTid)->first();
                        $dataLogQCT['project_id'] = $dataProject->id;
                        $dataLogQCT['name'] = "Penunjukan <strong>".ucfirst($dataTeam->firstname)." ".ucfirst($dataTeam->lastname)."</strong> sebagai <strong>".ucwords($teamTitle)."</strong> yang baru.";
                        $dataLogQCT['publisher_id'] = $userId;
                        $dataLogQCT['publisher_type'] = $userType;
                        DB::table('projects_log')->insert($dataLogQCT);

                        ###admin notification
                            $dataReceiverDepartment = DB::table('admins')->where('department_id',$userDepartment)->first();
                            $dataNotif['receiver_id'] = $dataReceiverDepartment->id;
                            $dataNotif['receiver_type'] = 'admin';
                            $dataNotif['receiver_department'] = $userDepartment;
                            $dataNotif['level'] = 1;
        
                            ###notif message
                            $dataNotif['desc'] = "Menunjuk <strong>".ucfirst($dataTeam->firstname)." ".ucfirst($dataTeam->lastname)."</strong> sebagai <strong>".ucwords($teamTitle)."</strong> yang baru pada task <a href='".route('admin-projects-task.show',$dataProjectTask->id)."'><strong>".ucfirst($dataProjectTask->name)."</strong></a> proyek <a href='".route('admin-projects.show',$dataProject->id)."'><strong>".ucfirst($dataProject->name)."</strong></a>.";
        
                            ###insert data to notifications table
                            DB::table('notifications')->insert($dataNotif);
                        ###admin notification end
                    }
                ###QCT data end
                ###QCE amendment
                    $oldQCEid = $dataProjectTask->qce_id;
                    $newQCEid = $request->qce_id;
                    $teamTitle = "QC expenses";
                    if ($newQCEid != 0 && $oldQCEid != $newQCEid) {
                        ###notification data for PM
                        $dataNotifQCE['publisher_id'] = $userId;
                        $dataNotifQCE['publisher_type'] = $userType;
                        $dataNotifQCE['publisher_department'] = $userDepartment;

                        $dataNotifQCE['receiver_id'] = $newQCEid;
                        $dataNotifQCE['receiver_type'] = $userType;
                        $dataNotifQCE['receiver_department'] = $userDepartment;
                        $dataNotifQCE['level'] = 1;

                        ###notif message
                        $dataNotifQCE['desc'] = "Menunjuk Anda sebagai <strong>".ucwords($teamTitle)."</strong> pada <a href='".route('user-projects-task.show',$dataProjectTask->id)."'><strong>".ucfirst($dataProjectTask->name)."</strong></a>.";
                        ###insert data to notifications table for pm
                        DB::table('notifications')->insert($dataNotifQCE);

                        ###logging
                        $dataTeam = DB::table('users')->where('id',$newQCEid)->first();
                        $dataLogQCE['project_id'] = $dataProject->id;
                        $dataLogQCE['name'] = "Penunjukan <strong>".ucfirst($dataTeam->firstname)." ".ucfirst($dataTeam->lastname)."</strong> sebagai <strong>".ucwords($teamTitle)."</strong> yang baru.";
                        $dataLogQCE['publisher_id'] = $userId;
                        $dataLogQCE['publisher_type'] = $userType;
                        DB::table('projects_log')->insert($dataLogQCE);

                        ###admin notification
                            $dataReceiverDepartment = DB::table('admins')->where('department_id',$userDepartment)->first();
                            $dataNotif['receiver_id'] = $dataReceiverDepartment->id;
                            $dataNotif['receiver_type'] = 'admin';
                            $dataNotif['receiver_department'] = $userDepartment;
                            $dataNotif['level'] = 1;
        
                            ###notif message
                            $dataNotif['desc'] = "Menunjuk <strong>".ucfirst($dataTeam->firstname)." ".ucfirst($dataTeam->lastname)."</strong> sebagai <strong>".ucwords($teamTitle)."</strong> yang baru pada task <a href='".route('admin-projects-task.show',$dataProjectTask->id)."'><strong>".ucfirst($dataProjectTask->name)."</strong></a> proyek <a href='".route('admin-projects.show',$dataProject->id)."'><strong>".ucfirst($dataProject->name)."</strong></a>.";
        
                            ###insert data to notifications table
                            DB::table('notifications')->insert($dataNotif);
                        ###admin notification end
                    }
                ###QCE data end
            //insert log & send notifications end

            //update task status
            if ($updateTaskStatus != null) {
                if ($userLevel == 2) {
                    $data['status'] = $request->status;
                    
                    DB::table('projects_task')->where('id', $id)->update($data);
                    return redirect()->back()->with('alert-success','Status task berhasil diubah.');
                }else{
                    $data['status'] = $updateTaskStatus;
                }
                
                DB::table('projects_task')->where('id', $id)->update($data);

                return redirect()->route('user-projects.show', $projectId)->with('alert-success','Data berhasil disimpan.');
            }

            if ($userLevel != 22) {

                //update task data
                $request->validate([
                    #'name' => 'required',
                    'number' => 'required',
                    'date_start' => 'nullable',
                    'date_end' => 'nullable|after:date_start',
                ]);
    
                $data = $request->except(['_token', 'submit', '_method']);
                $data['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
    
                DB::table('projects_task')->where('id', $id)->update($data);

                return redirect()->route('user-projects.show', $projectId)->with('alert-success','Data berhasil disimpan.');
            }

            //co admin
            $request->validate([
                'name' => 'required',
                'number' => 'required',
            ]);

            $data = $request->except(['_token', 'submit', '_method']);
            $data['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

            DB::table('projects_task')->where('id', $id)->update($data);

            return redirect()->route('user-projects.show', $projectId)->with('alert-success','Data berhasil disimpan.');

        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Project.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $userId = Auth::user()->id;
        $userLevel = Auth::user()->user_level;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        if ($userLevel == 22 && $userDepartment == 1) {
            //insert log & send notifications
                $dataProjectTask = DB::table('projects_task')->where('id',$id)->first();
                $dataProject = DB::table('projects')->where('id',$dataProjectTask->project_id)->first();
                ###publisher data
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
                ###receiver data
                $logName = $dataProjectTask->name;
                $dataReceiverDepartment = DB::table('admins')->where('department_id',$userDepartment)->first();
                $dataNotif['receiver_id'] = $dataReceiverDepartment->id;
                $dataNotif['receiver_type'] = 'admin';
                $dataNotif['receiver_department'] = $userDepartment;
                $dataNotif['level'] = 1;

                ###logging
                $dataLog['project_id'] = $dataProject->id;
                $dataLog['name'] = "Penghapusan task <strong>".ucfirst($logName)."</strong>";
                $dataLog['publisher_id'] = $userId;
                $dataLog['publisher_type'] = $userType;
                DB::table('projects_log')->insert($dataLog);

                ###notif message
                $dataNotif['desc'] = "Menghapus task <strong>".ucfirst($logName)."</strong></a> pada proyek <a href='".route('admin-projects.show',$dataProject->id)."'><strong>".ucfirst($dataProject->name)."</strong></a>";

                ###insert data to notifications table
                DB::table('notifications')->insert($dataNotif);
            //insert log & send notifications end

            //delete the data
            DB::table('projects_task')->delete($id);

            return redirect()->back()->with('alert-success','Task Project berhasil dihapus.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Project.');
    }
}
