<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class ProjectTaskController extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Task.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Task.');
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
        $userRole = Auth::user()->role;
        $userDepartment = Auth::user()->department_id;
        $projectId = $request->project_id;

        //first check
        if($userDepartment == 1 || $userRole == 1){

            //validation
            $request->validate([
                'name' => 'required|unique:projects_task,name,'.$request->name,
                'number' => 'required|unique:projects_task,number,'.$request->number
            ]);

            $data = $request->except(['_token', 'submit']);
            $data['publisher_id'] = $userId;
            $data['publisher_type'] = $userType;
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');

            DB::table('projects_task')->insert($data);

            return redirect()->route('admin-projects.show', $projectId)->with('alert-success','Data berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Task.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Task.');
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
        $userRole = Auth::user()->role;
        $userDepartment = Auth::user()->department_id;
        //first check
        if ($userDepartment == 1 || $userDepartment == 9 || $userRole == 1) {
            $data['userDepartment'] = $userDepartment;
            $data['taskData'] = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name'),
                DB::raw('(SELECT pm_id FROM projects WHERE projects.id = pt.project_id) as pm_id'),
            ])
            ->where('id',$id)->first();

            if (!isset($data['taskData'])) {
                return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
            }

            $projectId = $data['taskData']->project_id;
            $taskId = $data['taskData']->id;
            $pmId = $data['taskData']->pm_id;

            $data['dataPRCount'] = DB::table('project_purchase_requisition')->where('project_id',$projectId)->where('task_id',$taskId)->count();
            $data['dataPM'] = DB::table('users')->where('id',$pmId)->first();
            $data['dataTechs'] = DB::table('techs')->get();

            return view('admin.project.task.edit', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Task.');
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
        $userRole = Auth::user()->role;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;
        $projectId = $request->project_id;

        //first check
        if($userDepartment == 1 || $userRole == 1){
            //validation
            $request->validate([
                'name' => 'required|unique:projects_task,name,'.$id,
                'number' => 'required|unique:projects_task,number,'.$id
            ]);

            $data = $request->except(['_token', 'submit', '_method']);
            $data['publisher_id'] = $userId;
            $data['publisher_type'] = $userType;
            $data['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

            DB::table('projects_task')->where('id', $id)->update($data);

            return redirect()->route('admin-projects.show', $projectId)->with('alert-success','Data berhasil disimpan.');
        }elseif($userDepartment == 9){
            //update tech data
            $data['tech_id'] = $request->tech_id;

            DB::table('projects_task')->where('id', $id)->update($data);
            
            return redirect()->back()->with('alert-success','Data berhasil diubah.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Task.');
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
        $userDepartment = Auth::user()->department_id;
        $projectId = $request->project_id;
        
        //first check
        if ($userDepartment == 1) {
            $data['deleted_at'] = Carbon::now()->format('Y-m-d H:i:s');

            DB::table('projects_task')->where('id',$id)->update($data);

            return redirect()->route('admin-projects.show', $projectId)->with('alert-success','Data berhasil dihapus.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Task.');
    }
}
