<?php

namespace App\Http\Controllers\Tech;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class TechProjectCashAdvanceController extends Controller
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
    public function index(Request $request)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = 1; //project department
        $projectId = $request->project_id;
        $taskId = $request->task_id;
        $cashAdvanceStatus = 3; //3 approved ca
        $expenseStatus = 4; //2 approved expense

        //first check
        if ($taskId == null) {
            return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Cash Advance.');
        }

        //second check
        $checkPriviledge = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('tech_id',$userId)->where('deleted_at',null)->count();

        if ($checkPriviledge > 0) {
            //getting project data
            $data['projectTask'] = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
            ])
            ->where('id',$taskId)->where('tech_id',$userId)->first();

            //data cash advance
            $data['dataCashAdvance'] = DB::table('project_cash_advance as pca1')
            ->select([
                'pca1.*',
                DB::raw('(SELECT name FROM project_cash_advance_status WHERE project_cash_advance_status.id = pca1.status) as status_name'),
            ])
            ->where('task_id',$taskId)->where('publisher_id',$userId)->paginate(10);

            //cash advance sum
            $data['dataCashAdvanceCount'] = DB::table('project_cash_advance as pca')
            ->select([
                'pca.*',
                DB::raw('SUM(pca.amount) as total_dana'),
                DB::raw('(SELECT SUM(amount) FROM project_expenses WHERE project_expenses.status = '.$expenseStatus.' && project_expenses.task_id = pca.task_id) as total_pengeluaran'),
            ])
            ->where('status',$cashAdvanceStatus)->where('task_id',$taskId)->where('publisher_id',$userId)->first();

            $data['dataCAStatus'] = DB::table('project_cash_advance_status')->get();

            return view('tech.cash-advance.index',$data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Cash Advance.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = 1; //project department
        $projectId = $request->project_id;
        $taskId = $request->task_id;

        //first check
        if ($taskId == null) {
            return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Cash Advance.');
        }

        //check priviledge
        $checkPriviledge = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('tech_id',$userId)->where('deleted_at',null)->count();

        if ($checkPriviledge > 0) {

            //getting data
            $data['projectTask'] = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
            ])
            ->where('id',$taskId)->where('tech_id',$userId)->first();

            return view('tech.cash-advance.create',$data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Cash Advance.');
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
        $userDepartment = 1; //project department
        $projectId = $request->project_id;
        $taskId = $request->task_id;

        $request->validate([
            'name' => 'required',
            'amount' => 'required',
        ]);

        //check priviledge
        $checkPriviledge = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('tech_id',$userId)->where('deleted_at',null)->count();

        if ($checkPriviledge > 0) {
            //getting data
            $data = $request->except(['_token','submit']);
            $data['publisher_id'] = $userId;
            $data['publisher_type'] = $userType;
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');

            //insert data
            DB::table('project_cash_advance')->insert($data);

            return redirect()->route('project-ca-tech.index',$data)->with('alert-success','Data berhasil disimpan');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Cash Advance.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Cash Advance.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = 1; //project department

        //check priviledge
        $checkPriviledge = DB::table('project_cash_advance')->where('id',$id)->where('publisher_id',$userId)->count();

        if ($checkPriviledge > 0) {

            //getting data
            $data['dataCashAdvance'] = DB::table('project_cash_advance as pca')
            ->select([
                'pca.*',
                DB::raw('(SELECT id FROM projects WHERE projects.id = pca.project_id) as project_id'),
                DB::raw('(SELECT name FROM projects WHERE projects.id = pca.project_id) as project_name'),
                DB::raw('(SELECT number FROM projects_task WHERE projects_task.id = pca.task_id) as number'),
                DB::raw('(SELECT name FROM project_cash_advance_status WHERE project_cash_advance_status.id = pca.status) as status_name'),
            ])
            ->where('id',$id)->where('publisher_id',$userId)->first();

            $projectId = $data['dataCashAdvance']->project_id;
            $taskId = $data['dataCashAdvance']->task_id;

            //task data
            $data['projectTask'] = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
            ])
            ->where('id',$taskId)->where('tech_id',$userId)->first();

            return view('tech.cash-advance.edit',$data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Cash Advance.');
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
        $userDepartment = 1; //project department
        $projectId = $request->project_id;
        $taskId = $request->task_id;

        //check priviledge
        $checkPriviledge = DB::table('project_cash_advance')->where('id',$id)->where('publisher_id',$userId)->count();

        if ($checkPriviledge > 0) {
            //update status
            if (isset($request->status)) {
                //getting data
                $data = $request->except(['_token','_method','submit']);
                $data['status'] = $request->status;
                $data['reject_status'] = 0;
                $data['rejected_at'] = null;
                $data['submitted_at'] = Carbon::now()->format('Y-m-d H:i:s');

                DB::table('project_cash_advance')->where('id',$id)->update($data);

                return redirect()->route('project-ca-tech.index','project_id='.$projectId.'&task_id='.$taskId)->with('alert-success', 'Data berhasil disimpan');
            }

            //validate
            $request->validate([
                'name' => 'required',
                'amount' => 'required',
            ]);

            //getting data
            $data = $request->except(['_token','_method','submit']);
            $data['publisher_id'] = $userId;
            $data['publisher_type'] = $userType;
            $data['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

            //insert data
            DB::table('project_cash_advance')->where('id',$id)->update($data);

            return redirect()->route('project-ca-tech.index','project_id='.$projectId.'&task_id='.$taskId)->with('alert-success','Data berhasil disimpan');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Cash Advance.');
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
        $userDepartment = 1; //project department
        $projectId = $request->project_id;
        $taskId = $request->task_id;

        //first check
        if ($taskId == null) {
            return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Cash Advance.');
        }

        //check priviledge
        $checkPriviledge = DB::table('project_cash_advance')->where('id',$id)->where('publisher_id',$userId)->count();

        if ($checkPriviledge > 0) {

            //delete from database
            DB::table('project_cash_advance')->delete($id);

            return redirect()->route('project-ca-tech.index','project_id='.$projectId.'&task_id='.$taskId)->with('alert-success','Data berhasil dihapus');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Cash Advance.');
    }
}
