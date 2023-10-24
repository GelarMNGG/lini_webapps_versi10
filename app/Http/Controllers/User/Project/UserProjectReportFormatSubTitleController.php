<?php

namespace App\Http\Controllers\User\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;

class UserProjectReportFormatSubTitleController extends Controller
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
        return redirect()->back()->with('alert-danger','Terjadi kesalahan jaringan, silahkan mencoba beberapa saat lagi.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Terjadi kesalahan jaringan, silahkan mencoba beberapa saat lagi.');
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

        $pm = 3; //pm
        $qcDocument = 4; //qc document
        $projectDepartment = 1; //project department

        $projectId = $request->project_id;
        $taskId = $request->task_id;
        $prtsId = $request->prts_id;

        //request validation
            $request->validate([
                'subtitle' => 'required|min:3'
            ]);

        //firstcheck
            if ($userDepartment != $projectDepartment) {
                return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman upload file template Project Report');
            }
        //check priviledge
            if ($userLevel == $qcDocument) {
                $privilegeCheck = DB::table('projects_task as pt')
                ->select([
                    'pt.*',
                    DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
                ])
                ->where('project_id',$projectId)->where('id',$taskId)->where('qcd_id',$userId)->where('deleted_at',null)->first();
            }else{
                $pmCheck = DB::table('projects')->where('id',$projectId)->where('pm_id',$userId)->where('deleted_at',null)->count();
                //privilege
                    if ($pmCheck > 0) {
                        $privilegeCheck = DB::table('projects_task as pt')
                        ->select([
                            'pt.*',
                            DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name'),
                        ])
                        ->where('project_id',$projectId)->where('id',$taskId)->where('deleted_at',null)->first();
                    }else{
                        $privilegeCheck = NULL;
                    }
            }

        if (isset($privilegeCheck)) {
            $data = $request->only(['subtitle','title_id']);
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');

            DB::table('project_report_all_format_subtitle')->insert($data);
            //redirect back
            return redirect()->back()->with('alert-success','Data berhasil diinput.');
        }

        return redirect()->back()->with('alert-danger','Terjadi kesalahan jaringan, silahkan mencoba beberapa saat lagi.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Terjadi kesalahan jaringan, silahkan mencoba beberapa saat lagi.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Terjadi kesalahan jaringan, silahkan mencoba beberapa saat lagi.');
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
        $userDepartment = Auth::user()->department_id; //project department

        $taskId = $request->task_id;
        $projectId = $request->project_id;
        $projectDepartment = 1;
        $qcDocument = 4;
        $subcatStatus = 1; //active subcategory
        $commentStatus = 0; //accessible for technician level user

        //request validatation
            $request->validate([
                'subtitle' => 'required|min:3'
            ]);
        //firstcheck
            if ($userDepartment != $projectDepartment) {
                return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Report');
            }
        //check priviledge
            if ($userLevel == $qcDocument) {
                $privilegeCheck = DB::table('projects_task as pt')
                ->select([
                    'pt.*',
                    DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
                ])
                ->where('project_id',$projectId)->where('id',$taskId)->where('qcd_id',$userId)->where('deleted_at',null)->first();
            }else{
                $pmCheck = DB::table('projects')->where('id',$projectId)->where('pm_id',$userId)->where('deleted_at',null)->count();
                //privilege
                    if ($pmCheck > 0) {
                        $privilegeCheck = DB::table('projects_task as pt')
                        ->select([
                            'pt.*',
                            DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name'),
                        ])
                        ->where('project_id',$projectId)->where('id',$taskId)->where('deleted_at',null)->first();
                    }else{
                        $privilegeCheck = NULL;
                    }
            }
        
        if (isset($privilegeCheck)) {
            $data = $request->only(['subtitle','sort_order']);
            $data['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

            DB::table('project_report_all_format_subtitle')->where('id',$id)->update($data);

            return redirect()->back()->with('alert-success','Data sub judul berhasil diubah.');
        }

        return redirect()->back()->with('alert-danger','Terjadi kesalahan jaringan, silahkan mencoba beberapa saat lagi.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id; //project department

        $taskId = $request->task_id;
        $projectId = $request->project_id;
        $projectDepartment = 1;
        $qcDocument = 4;
        $subcatStatus = 1; //active subcategory
        $commentStatus = 0; //accessible for technician level user

        //firstcheck
            if ($userDepartment != $projectDepartment) {
                return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Report');
            }
        //check priviledge
            if ($userLevel == $qcDocument) {
                $privilegeCheck = DB::table('projects_task as pt')
                ->select([
                    'pt.*',
                    DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
                ])
                ->where('project_id',$projectId)->where('id',$taskId)->where('qcd_id',$userId)->where('deleted_at',null)->first();
            }else{
                $pmCheck = DB::table('projects')->where('id',$projectId)->where('pm_id',$userId)->where('deleted_at',null)->count();
                //privilege
                    if ($pmCheck > 0) {
                        $privilegeCheck = DB::table('projects_task as pt')
                        ->select([
                            'pt.*',
                            DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name'),
                        ])
                        ->where('project_id',$projectId)->where('id',$taskId)->where('deleted_at',null)->first();
                    }else{
                        $privilegeCheck = NULL;
                    }
            }
        
        if (isset($privilegeCheck)) {
            //delete subtitle
                DB::table('project_report_all_format_subtitle')->delete($id);
            //delete report
                $dataReport = DB::table('project_report_all_format')->select('id')->where('subtitle_id',$id)->first();
                DB::table('project_report_all_format')->delete($dataReport->id);
            //redirect
            return redirect()->back()->with('alert-success','Data judul berhasil dihapus.');
        }

        return redirect()->back()->with('alert-danger','Terjadi kesalahan jaringan, silahkan mencoba beberapa saat lagi.');
    }
}
