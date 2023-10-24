<?php

namespace App\Http\Controllers\Cust;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class CustProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:cust');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;

        $userDepartment = 1; //project department

        $firstCheck = DB::table('projects')->where('id',$id)->where('customer_id',$userId)->count();

        if ($firstCheck > 0 && $userType == 'cust') {

            //getting all data by user
            $data['project'] = DB::table('projects as proj')
            ->select([
                'proj.*',
                #DB::raw('(SELECT COUNT(*) WHERE proj.status = 1) as newCount'),
                #DB::raw('(SELECT COUNT(*) WHERE proj.status = 2) as onprogressCount'),
                #DB::raw('(SELECT COUNT(*) WHERE proj.status = 3) as reportingCount'),
                #DB::raw('(SELECT COUNT(*) WHERE proj.status = 4) as finishedCount'),
                //taskcount
                DB::raw('(SELECT COUNT(*) FROM projects_task as pta WHERE pta.project_id = proj.id AND pta.project_id IS NOT NULL) as taskCount'),
                DB::raw('(SELECT COUNT(pta.status) FROM projects_task as pta WHERE pta.project_id = proj.id AND pta.status = 0 AND pta.status IS NOT NULL) as taskStatus0'),
                DB::raw('(SELECT COUNT(pta.status) FROM projects_task as pta WHERE pta.project_id = proj.id AND pta.status = 1 AND pta.status IS NOT NULL) as taskStatus1'),
                DB::raw('(SELECT COUNT(pta.status) FROM projects_task as pta WHERE pta.project_id = proj.id AND pta.status = 2 AND pta.status IS NOT NULL) as taskStatus2'),
                DB::raw('(SELECT COUNT(pta.status) FROM projects_task as pta WHERE pta.project_id = proj.id AND pta.status = 3 AND pta.status IS NOT NULL) as taskStatus3'),
                DB::raw('(SELECT COUNT(pta.status) FROM projects_task as pta WHERE pta.project_id = proj.id AND pta.status = 4 AND pta.status IS NOT NULL) as taskStatus4'),
            ])
            ->where('id',$id)
            ->where('customer_id',$userId)
            ->first();

            //task data
            $data['projectTaskDatas'] = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                //imagereport count
                DB::raw('(SELECT COUNT(pri.shared) FROM project_report_images as pri WHERE pri.project_id = pt.project_id AND pri.task_id = pt.id AND pri.shared = 1 AND pri.shared IS NOT NULL) as reportImageCount'),
            ])
            ->where('project_id',$id)->where('deleted_at',null)->get();

            $data['dataProjectStatus'] = DB::table('projects_status')->get();
            $data['dataTaskStatus'] = DB::table('projects_task_status')->get();

            $data['dataUsers'] = DB::table('users')->get();
            $data['dataTechs'] = DB::table('techs')->get();

            $data['userDepartment'] = $userDepartment;

            return view('cust.project.show', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project.');
    }

    //customized link
    public function dashboard()
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;

        //getting all data by user
        $data['projects'] = DB::table('projects as proj')
        ->select([
            'proj.*',
            #DB::raw('(SELECT COUNT(*) WHERE proj.status = 1 AND proj.status IS NOT NULL) as newCount'),
            #DB::raw('(SELECT COUNT(*) WHERE proj.status = 2 AND proj.status IS NOT NULL) as onprogressCount'),
            #DB::raw('(SELECT COUNT(*) WHERE proj.status = 3 AND proj.status IS NOT NULL) as reportingCount'),
            #DB::raw('(SELECT COUNT(*) WHERE proj.status = 4 AND proj.status IS NOT NULL) as finishedCount'),
            //taskCount
            DB::raw('(SELECT COUNT(*) FROM projects_task as pta WHERE pta.project_id = proj.id AND pta.project_id IS NOT NULL) as taskCount'),
        ])
        ->where('customer_id',$userId)
        ->paginate(20);

        //count the data by status
        $data['newCount'] = DB::table('projects')->where('customer_id',$userId)->where('status',1)->count();
        $data['onprogressCount'] = DB::table('projects')->where('customer_id',$userId)->where('status',2)->count();
        $data['reportingCount'] = DB::table('projects')->where('customer_id',$userId)->where('status',3)->count();
        $data['finishedCount'] = DB::table('projects')->where('customer_id',$userId)->where('status',4)->count();
        
        if (isset($data['projects']) && $userType == 'cust') {
            return view('cust.project.dashboard', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project.');
    }
}
