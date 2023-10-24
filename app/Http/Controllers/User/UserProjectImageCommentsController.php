<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class UserProjectImageCommentsController extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Images Comments.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Images Comments.');
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
        $taskId = $request->task_id;

        $request->validate([
            'comment' => 'required|min:5',
        ]);

        //check privilege & getting the data
        if ($userLevel == 4 && $userDepartment == 1) {
            $privilegeCheck = DB::table('projects_task as taskTableCheck')
            ->select([
                'taskTableCheck.*',
                DB::raw('(SELECT COUNT(project_report_images.submitted_at) FROM project_report_images WHERE project_report_images.task_id = taskTableCheck.id AND project_report_images.submitted_at IS NOT NULL) as submittedCount'),
            ])
            ->where('project_id',$projectId)
            ->where('id',$taskId)
            ->where('qcd_id',$userId)
            ->where('deleted_at',null)
            ->first();
        }
        if($userLevel == 3 && $userDepartment == 1){
            $privilegeCheck = DB::table('projects_task as taskTableCheck')
            ->select([
                'taskTableCheck.*',
                DB::raw('(SELECT COUNT(project_report_images.submitted_at) FROM project_report_images WHERE project_report_images.task_id = taskTableCheck.id AND project_report_images.submitted_at IS NOT NULL) as submittedCount'),
            ])
            ->where('project_id',$projectId)
            ->where('id',$taskId)
            ->where('pm_id',$userId)
            ->where('deleted_at',null)
            ->first();
        }

        //first check
        if (!isset($privilegeCheck)) {
            return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
        }

        //second check
        if ($userLevel == 4 || $userLevel == 3 && $userDepartment == 1) {
            //getting the data
            $data = $request->except(['_token','submit','comment_status']);

            $data['publisher_id'] = $userId;
            $data['publisher_type'] = $userType;
            $data['publisher_level'] = $userLevel;
            
            if ($request->comment_status) {
                $data['status'] = $request->comment_status;
                DB::table('project_report_images_comments')->insert($data);

                return redirect()->back()->with('alert-success','Komentar berhasil disimpan.');
            }
            
            DB::table('project_report_images_comments')->insert($data);

            return redirect()->back()->with('alert-success','Komentar berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Images Comments.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Images Comments.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Images Comments.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Images Comments.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Images Comments.');
    }
}
