<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Auth;
use DB;

class UserProjectCashAdvanceController extends Controller
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
    public function index(Request $request)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->departement_id;

        $projectId = $request->project_id;
        $taskId = $request->task_id;

        $cashAdvanceStatus = 3; //3 approved ca
        $expenseStatus = 4; //2 approved expense

        if ($userLevel == 3) {
            $userRole = 'pm_id';
        }else{
            $userRole = 'qce_id';
        }

        //check priviledge
        $dataCountCheck = DB::table('projects_task')->where('id',$taskId)->where($userRole,$userId)->where('deleted_at',null)->count();
        
        if ($dataCountCheck > 0) {
            //getting project data
            $data['projectTask'] = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
            ])
            ->where('id',$taskId)->where($userRole,$userId)->first();

            //data cash advance
            $data['dataCashAdvance'] = DB::table('project_cash_advance as pca1')
            ->select([
                'pca1.*',
                DB::raw('(SELECT name FROM project_cash_advance_status WHERE project_cash_advance_status.id = pca1.status) as status_name'),
                DB::raw('(SELECT status FROM project_purchase_requisition WHERE project_purchase_requisition.id = pca1.status) as status_name'),
            ])
            ->where('task_id',$taskId)->where('publisher_id',$data['projectTask']->tech_id)->get();
            //cash advance sum
            $data['dataCashAdvanceCount'] = DB::table('project_cash_advance as pca')
            ->select([
                'pca.*',
                DB::raw('SUM(pca.amount) as total_dana'),
                DB::raw('(SELECT SUM(amount) FROM project_expenses WHERE project_expenses.status = '.$expenseStatus.' && project_expenses.task_id = pca.task_id) as total_pengeluaran'),
            ])
            ->where('status',$cashAdvanceStatus)->where('task_id',$taskId)->where('publisher_id',$data['projectTask']->tech_id)->first();

            $data['dataCAStatus'] = DB::table('project_cash_advance_status')->get();

            return view('user.project.cash-advance.index',$data);
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
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        $projectId = $request->project_id;
        $taskId = $request->task_id;
        $id = $request->id;

        $expenseStatus = 4; //approved by admin
        $expenseSubmittedStatus = 2; //submitted

        if ($userLevel == 3) {
            $userRole = 'pm_id';
        }else{
            $userRole = 'qce_id';
        }

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
        if ($userLevel == 6 && $userDepartment == 1) {

            ### expense report data ###
                $data['projectTask'] = DB::table('projects_task as pt')
                ->select([
                    'pt.*',
                    DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name'),
                ])
                ->where('project_id',$projectId)->where('id',$taskId)->where($userRole,$userId)->first();
            ### expense report data end ###

            ### data cash advance ###
            $data['dataCashAdvance'] = DB::table('project_cash_advance')->where('id',$id)->where('project_id',$projectId)->where('task_id',$taskId)->first();
            ### data cash advance ###

            $data['theId'] = $id;

            return view('user.project.cash-advance.create', $data);
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
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        $projectId = $request->project_id;
        $taskId = $request->task_id;
        $id = $request->id;

        $expenseStatus = 4; //approved by admin
        $expenseSubmittedStatus = 2; //submitted

        if ($userLevel == 3) {
            $userRole = 'pm_id';
        }else{
            $userRole = 'qce_id';
        }

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
        if ($userLevel == 6 && $userDepartment == 1) {

            $request->validate([
                'image' => 'required|mimes:jpeg,jpg,png,pdf|max:9216',
            ]);

            //file handler
            $fileName = null;
            $destinationPath = public_path().'/img/cash-advance/tech/';
            
            // Retrieving An Uploaded File
            $file = $request->file('image');
            if (!empty($file)) {
                $extension = $file->getClientOriginalExtension();
                $fileName = time().'_'.$file->getClientOriginalName();
        
                // Moving An Uploaded File
                $request->file('image')->move($destinationPath, $fileName);
            }

            //custom setting to support file upload
            $data = $request->except(['_token','submit']);
            
            $data['approver_id'] = $userId;
            $data['approver_type'] = $userType;
            $data['approved_at'] = Carbon::now()->format('Y-m-d H:i:s');

            if (!empty($fileName)) {
                $data['image'] = $fileName;
            }

            DB::table('project_cash_advance')->where('id',$id)->update($data);

            return redirect()->route('user-projects-ca.index','project_id='.$projectId.'&task_id='.$taskId)->with('alert-success','Bukti transfer Cash Advance berhasil disimpan.');
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
        $userLevel = Auth::user()->user_level;
        $userDepartment = 1; //project department

        if ($userDepartment != 1 || $userLevel != 6) {
            return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Cash Advance.');
        }

        //check priviledge
        $checkPriviledge = DB::table('project_cash_advance')->where('id',$id)->count();

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
            ->where('id',$id)->first();

            $projectId = $data['dataCashAdvance']->project_id;
            $taskId = $data['dataCashAdvance']->task_id;

            //task data
            $data['projectTask'] = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
            ])
            ->where('project_id',$projectId)
            ->where('id',$taskId)->where('qce_id',$userId)->first();

            if (!isset($data['projectTask'])) {
                return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Cash Advance.');
            }

            return view('user.project.cash-advance.edit',$data);
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
        if ($userDepartment == 1) {

            if ($userLevel == 3) {
                //update status
                $updateStatus = $request->status;
                if (isset($updateStatus)) {
    
                    if ($updateStatus == 1) {
    
                        ### reject note ###
                        $request->validate([
                            'amount' => 'required',
                            'reject_note' => 'required|min:5',
                        ]);
                        ### reject note end ###
    
                        $data = $request->except(['_token','_method','submit']);
                        $data['status'] = $request->status;
                        $data['submitted_at'] = null;
                        $data['approved_at'] = null;
                        $data['rejected_at'] = Carbon::now()->format('Y-m-d H:i:s');
    
                        DB::table('project_cash_advance')->where('id',$id)->update($data);
        
                        return redirect()->back()->with('alert-success', 'Permohonan cash advance telah ditolak.');
                    }elseif($updateStatus == 4){
                        $data = $request->except(['_token','_method','submit']);
                        $data['status'] = $request->status;
                        $data['approved_at'] = Carbon::now()->format('Y-m-d H:i:s');
                        $data['approved_by_pm_at'] = Carbon::now()->format('Y-m-d H:i:s');
    
                        DB::table('project_cash_advance')->where('id',$id)->update($data);
        
                        return redirect()->back()->with('alert-success', 'Permohonan cash advance telah disetujui.');
                    }
                }
            }

            if ($userLevel == 6) {

                $request->validate([
                    'image' => 'required|mimes:jpeg,jpg,png,pdf|max:9216',
                ]);
    
                //file handler
                $fileName = null;
                $destinationPath = public_path().'/img/cash-advance/tech/';
                
                // Retrieving An Uploaded File
                $file = $request->file('image');
                if (!empty($file)) {
                    $extension = $file->getClientOriginalExtension();
                    $fileName = time().'_'.$file->getClientOriginalName();
            
                    // Moving An Uploaded File
                    $request->file('image')->move($destinationPath, $fileName);

                    //delete previous image
                    $dataImage = DB::table('project_cash_advance')->select('image as image')->where('id', $id)->first();
                    $oldImage = $dataImage->image;

                    if($oldImage !== 'default.png'){
                        $image_path = $destinationPath.$oldImage;
                        if(File::exists($image_path)) {
                            File::delete($image_path);
                        }
                    }
                }
    
                //custom setting to support file upload
                $data = $request->except(['_token','submit','_method']);
    
                if (!empty($fileName)) {
                    $data['image'] = $fileName;
                }
    
                DB::table('project_cash_advance')->where('id',$id)->update($data);
    
                return redirect()->route('user-projects-ca.index','project_id='.$projectId.'&task_id='.$taskId)->with('alert-success','Bukti transfer Cash Advance berhasil disimpan.');
            }




        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Cash Advance.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Cash Advance.');
    }
}
