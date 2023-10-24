<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class PurchaseRequisitionController extends Controller
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
        $userId = Auth::user()->id;
        $userRole = Auth::user()->role;
        $userDepartment = Auth::user()->department_id;
        $prStatus = 2;

        if($userDepartment == 9 || $userRole == 1){
            $data['prDatas'] = DB::table('project_purchase_requisition as pr')
            ->select([
                'pr.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pr.project_id) as project_name'),
                DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = pr.task_id) as task_name'),
                DB::raw('(SELECT status FROM projects WHERE projects.id = pr.project_id) as project_status'),
                DB::raw('(SELECT tech_id FROM projects_task WHERE projects_task.id = pr.task_id) as tech_id'),
            ])
            ->where('status','>=',$prStatus)
            ->where('deleted_at', null)->paginate();

            $data['prStatus'] = DB::table('purchase_requisition_status')->get();
            $data['dataTeknisis'] = DB::table('techs')->get();

            return view('admin.purchase-requisition.index', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses data Purchase Requisition.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan membuat data Purchase Requisition.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan menyimpan data Purchase Requisition.');
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
        $userRole = Auth::user()->role;
        $userLevel = Auth::user()->user_level; 
        $userDepartment = Auth::user()->department_id; //9 for procurement

        if($userDepartment == 9 || $userRole == 1){
            //data
            $data['dataPR'] = DB::table('project_purchase_requisition as pr')
            ->select([
                'pr.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pr.project_id) as project_name'),
                DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = pr.project_id) as task_name'),
                DB::raw('(SELECT firstname FROM users WHERE users.id = pr.publisher_id) as firstname'),
                DB::raw('(SELECT lastname FROM users WHERE users.id = pr.publisher_id) as lastname'),
                DB::raw('(SELECT name FROM department WHERE department.id = pr.department_id) as department_name'),
                DB::raw('(SELECT name FROM items_category WHERE items_category.id = pr.category_id) as category_name'),
            ])
            ->where('id',$id)->first();

            if (!isset($data['dataPR'])) {
                return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses data Purchase Requisition.');
            }

            $projectId = $data['dataPR']->project_id;

            return view('admin.purchase-requisition.show',$data);
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses data Purchase Requisition.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengubah data Purchase Requisition.');
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
        $userRole = Auth::user()->role;
        $userDepartment = Auth::user()->department_id; //9 for procurement

        if($userDepartment == 9 || $userRole == 1){
            if (isset($request->status)) {
                //update status
                $dateNow = Carbon::now();
                $data['approver_id'] = $userId;
                $data['approver_type'] = $userType;
                $data['date_approved'] = $dateNow->toDateTimeString();
                $data['status'] = $request->status;

                DB::table('project_purchase_requisition')->where('id',$id)->update($data);

                return redirect()->route('admin-pr.index');
            }
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengubah data Purchase Requisition.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan menghapus data Purchase Requisition.');
    }
}
