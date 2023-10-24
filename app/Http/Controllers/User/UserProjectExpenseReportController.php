<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class UserProjectExpenseReportController extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Expenses Report.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Expenses Report.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Expenses Report.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Expenses Report.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Expenses Report.');
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
        $userDepartment = Auth::user()->department_id;

        $projectId = $request->project_id;
        $taskId = $request->task_id;

        $expenseStatus = 4; //approved by admin
        $expenseSubmittedStatus = 2; //submitted

        ### dataCountCheck ###
            if ($userLevel == 3) {
                $dataCountCheck = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('pm_id',$userId)->where('deleted_at',null)->count();
            }else{
                $dataCountCheck = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('qce_id',$userId)->where('deleted_at',null)->count();
            }

            if ($dataCountCheck < 1) {
                return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
            }
        ### dataCountCheck end ###

        ### 6 QC expenses
        if ($userLevel == 6 || $userLevel == 3 && $userDepartment == 1) {

            $data = $request->except(['_token','_method','submit']);
            
            if ($userLevel == 3) {
                if ($request->status == 1) {
                    $data['status'] = $request->status;
                    $data['submitted_at'] = null;
                    $data['canceled_at'] = null;
                    $data['approved_at'] = null;
                    $data['approved_by_pm_at'] = null;

                }else{
                    $data['status'] = $request->status;
                    $data['canceled_at'] = null;
                    $data['approved_by_pm_at'] = Carbon::now()->format('Y-m-d H:i:s');

                    DB::table('project_expenses_report')->where('id',$id)->update($data);
                    return redirect()->back()->with('alert-success', 'Data pengeluaran telah disetujui.');
                }
            }else{
                if ($request->status == 1) {
                    $data['status'] = $request->status;
                    $data['submitted_at'] = null;
                    $data['canceled_at'] = null;
                    $data['approved_at'] = null;
                }else{
                    $data['status'] = $request->status;
                    $data['canceled_at'] = null;
                    $data['approved_at'] = Carbon::now()->format('Y-m-d H:i:s');

                    DB::table('project_expenses_report')->where('id',$id)->update($data);
                    return redirect()->back()->with('alert-success', 'Data pengeluaran telah disetujui.');
                }
            }

            DB::table('project_expenses_report')->where('id',$id)->update($data);

            return redirect()->back()->with('alert-success', 'Data pengeluaran telah ditolak.');
            
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Expenses Report.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Expenses Report.');
    }
}
