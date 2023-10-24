<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class UserProjectRequestPaymentController extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Permohonan Pembayaran.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Permohonan Pembayaran.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Permohonan Pembayaran.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type; 
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        $projectId = $request->project_id;
        $taskId = $request->task_id;

        if($userDepartment == 1 && $userLevel == 6){

            //check priviledge & getting the data
            $priviledgeCheck = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
            ])
            ->where('project_id',$projectId)->where('id',$taskId)->where('qce_id',$userId)->where('deleted_at',null)->first();

            $data['projectTaskInfo'] = $priviledgeCheck;
            $data['dataTech'] = DB::table('techs')->where('id',$priviledgeCheck->tech_id)->first();
            $data['dataQC'] = DB::table('users')->where('id',$priviledgeCheck->qce_id)->first();
            $data['dataPM'] = DB::table('users')->where('id',$priviledgeCheck->pm_id)->first();
            $data['dataDeptHead'] = DB::table('admins')->where('department_id',$userDepartment)->first();

            //first check
            if (!isset($priviledgeCheck)) {
                return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
            }

            $data['projectCashAdvance'] = DB::table('project_cash_advance')->where('project_id',$projectId)->where('task_id',$taskId)->where('approved_by_pm_at','!=',null)->first();

            if (!isset($data['projectCashAdvance'])) {
                return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
            }
            

            return view('user.project.request-payment.create', $data);
        }
        
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Permohonan Pembayaran.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Permohonan Pembayaran.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Permohonan Pembayaran.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Permohonan Pembayaran.');
    }
}
