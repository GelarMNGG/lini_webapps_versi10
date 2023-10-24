<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class UserProjectReportCommentsController extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Report Comments.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Report Comments.');
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
        $pwoId = $request->pwo_id;

        $request->validate([
            'comment' => 'required|min:5',
        ]);

        //check priviledge & getting the data
        $priviledgeCheck = DB::table('project_work_order as pwoTableCheck')
        ->select([
            'pwoTableCheck.*',
            DB::raw('(SELECT status FROM projects WHERE projects.id = pwoTableCheck.project_id) as projectStatus')
        ])
        ->where('id',$pwoId)->first();

        //first check
        if (!isset($priviledgeCheck) || $priviledgeCheck->submittedCount < 1) {

            if (!isset($priviledgeCheck->projectStatus)) {
                $projectStatus = 1;
            }else{
                $projectStatus = $priviledgeCheck->projectStatus;
            }

            return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
        }

        //second check
        if ($userLevel == 4 || $userLevel == 3 && $userDepartment == 1) {
            //getting the data
            $data = $request->except(['_token','submit','comment_status']);

            $data['publisher_id'] = $userId;
            $data['publisher_type'] = $userType;
            $data['publisher_level'] = $userLevel;
            
            DB::table('projects_report_comments')->insert($data);

            return redirect()->back()->with('alert-success','Komentar berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Report Comments.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Report Comments.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Report Comments.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Report Comments.');
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
        $userType = Auth::user()->user_type;
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        $pwoId = DB::table('projects_report_comments')->where('id',$id)->first();

        if ($userLevel == 4 || $userLevel == 3 && $userDepartment == 1) {

            DB::table('projects_report_comments')->delete($id);

            return redirect()->back()->with('alert-success','Komentar berhasil dihapus.');
            #return redirect()->route('user-projects-report.show', $pwoId->pwo_id)->with('alert-success','Komentar berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Report Comments.');
    }
}
