<?php

namespace App\Http\Controllers\Tech;

use App\Tech;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\MailController;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use Auth;
use DB;

class TechExpensesController extends Controller
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

        $expenseStatus = 4; //approved by admin
        $expenseSubmittedStatus = 2; //submitted

        //check priviledge
        $dataCountCheck = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('tech_id',$userId)->where('deleted_at',null)->count();

        $data['dataFinishedCount'] = DB::table('project_expenses')->where('project_id',$projectId)->where('task_id',$taskId)->where('publisher_id',$userId)->where('status',$expenseStatus)->count();
        
        if ($dataCountCheck > 0) {

            //getting data
            $data['dataExpenses'] = DB::table('project_expenses as pe')
            ->select([
                'pe.*',
                DB::raw('(SELECT name FROM project_expenses_status WHERE project_expenses_status.id = pe.status) as status_name'),
                DB::raw('(SELECT description FROM project_product_reference WHERE project_product_reference.id = pe.code) as code_name'),
                DB::raw('(SELECT code FROM project_product_reference WHERE project_product_reference.id = pe.code) as code_id'),
                DB::raw('(SELECT COUNT(expense_id) FROM project_expenses_files WHERE project_expenses_files.expense_id = pe.id) as expenses_files_count'),
            ])
            ->where('project_id',$projectId)
            ->where('task_id',$taskId)
            ->where('publisher_id',$userId)
            ->whereNull('deleted_at')
            ->orderBy('created_at','DESC')
            ->paginate(10);

            //data task
            $data['projectTask'] = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
            ])
            ->where('id',$taskId)->where('tech_id',$userId)->where('deleted_at',null)->first();

            //additional data
            $data['dataExpensesStatus'] = DB::table('project_expenses_status')->get();

            $data['dataExpenseCount'] = DB::table('project_expenses')->where('task_id',$taskId)->where('publisher_id',$userId)->where('status','>',$expenseSubmittedStatus)->count();

            $data['dataReportCount'] = DB::table('project_expenses_report')->where('task_id',$taskId)->where('publisher_id',$userId)->where('status',$expenseSubmittedStatus)->where('canceled_at',null)->count();

            $data['dataExpensesImages'] = DB::table('project_expenses_files')->where('task_id',$taskId)->where('tech_id',$userId)->get();
            //$data['dataExpensesImages'] = DB::table('project_expenses_files')->where('tech_id',$userId)->get();

            return view('tech.expenses.index', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Expenses.');
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

        //check priviledge
        $dataCountCheck = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('tech_id',$userId)->where('deleted_at',null)->count();

        if ($dataCountCheck > 0) {

            //getting data
            $data['projectTask'] = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
            ])
            ->where('id',$taskId)->where('tech_id',$userId)->where('deleted_at',null)->first();

            //supporting data
            $data['productCodes'] = DB::table('project_product_reference')->get();
    
            //check priviledge
            $secondCheck = DB::table('projects_task')->where('project_id',$projectId)->where('tech_id',$userId)->count();
    
            if ($secondCheck > 0) {
                return view('tech.expenses.create',$data);
            }
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Expenses.');
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

        $projectId = $request->project_id;
        $taskId = $request->task_id;

        //check priviledge
        $firstCheck = DB::table('projects_task')->where('id',$taskId)->where('tech_id',$userId)->first();

        if (isset($firstCheck)) {

            $request->validate([
                'name' => 'required',
                'amount' => 'required',
                //'image' => 'required|mimes:jpeg,jpg,png|max:9216',
            ]);
    
            //file handler
            $fileName = null;
            $destinationPath = public_path().'/img/expenses/tech/';
            
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
            
            $data['publisher_id'] = $userId;
            $data['publisher_type'] = $userType;
            $data['submitted_at'] = Carbon::now()->format('Y-m-d H:i:s');
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
    
            if (!empty($fileName)) {
                $data['image'] = $fileName;
            }
    
            DB::table('project_expenses')->insert($data);

            return redirect()->route('expenses-tech.index', 'project_id='.$projectId.'&task_id='.$taskId)->with('alert-success','Data berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Expenses.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Expenses.');
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
        $userType = Auth::user()->user_type;

        ### project & task check ###
            $dataExpensesCheck = DB::table('project_expenses')->where('id',$id)->where('publisher_id',$userId)->where('deleted_at',null)->first();

            if ($dataExpensesCheck == null) {
                return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
            }

            $projectId = $dataExpensesCheck->project_id;
            $taskId = $dataExpensesCheck->task_id;
        ### project & task check end ###

        ### check privilege ###
            $dataCountCheck = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
            ])
            ->where('id',$taskId)->where('tech_id',$userId)->where('deleted_at',null)->first();

            if ($dataCountCheck == null) {
                return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
            }
        ### check privilege end ###
        
        ### getting data ###
            $data['dataExpenses'] = $dataExpensesCheck;
            $data['projectTask'] = $dataCountCheck;
        ### data end ###

        return view('tech.expenses.edit',$data);
        
        #return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Expenses.');
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
        $projectId = $request->project_id;
        $taskId = $request->task_id;

        //check priviledge
        $dataCountCheck = DB::table('projects_task')->where('id',$taskId)->where('tech_id',$userId)->where('deleted_at',null)->count();

        if ($dataCountCheck > 0) {
            //update status
            $status = $request->status;
            if (isset($status)) {
                $data = $request->except(['_token','_method','submit','expense_id','task_name']);

                $data['status'] = $request->status;
                
                if ($status == 5) {
                    DB::table('project_expenses')->where('id',$id)->update($data);
                    //send email to odoo
                        //$emailTaskData = DB::table('projects_task')->where('id',$taskId)->first();
                        $emailExpenseData = DB::table('project_expenses')->where('id',$id)->first();
                        $emailTaskData = DB::table('projects_task as pt')
                        ->select([
                            'pt.*',
                            DB::raw('(SELECT email FROM users WHERE users.id = pt.pm_id AND pt.pm_id IS NOT NULL) as pm_email'),
                            DB::raw('(SELECT email FROM users WHERE users.id = pt.pc_id AND pt.pc_id IS NOT NULL) as pc_email'),
                            DB::raw('(SELECT email FROM users WHERE users.id = pt.qce_id AND pt.qce_id IS NOT NULL) as qce_email'),
                        ])
                        //->select('pm_email','pc_email')
                        ->where('id',$taskId)->first();

                        ///need improvement
                            $ccEmailDefault = array('anto@limaintisinergi.com','adi.nariswara@limaintisinergi.com','ouwyeaaah@gmail.com');

                            $pm_email = $emailTaskData->pm_email;
                            $pc_email = $emailTaskData->pc_email;
                            $qce_email = $emailTaskData->qce_email;

                            $teamEmail = [$pm_email,$pc_email,$qce_email];
                            
                            $ccEmail = array_merge($ccEmailDefault,$teamEmail);
                        ///need improvement

                        $taskName = $emailTaskData->name;
                        $expenseId = $emailExpenseData->id;
                        $expenseName = $emailExpenseData->name;
                        $expenseAmount = $emailExpenseData->amount;
                        $expenseCodeId = $emailExpenseData->code;

                        //$testEmail = 'anto@limaintisinergi.com';
                        $erpEmail = 'expense@erp.limaintisinergi.com';
                        $codeData = DB::table('project_product_reference')->where('id',$expenseCodeId)->first();
                        $expenseCode = $codeData->code;

                        //temporary sitename = taskname
                        $siteName = $taskName;

                        ### send email to odoo start
                            //MailController::sendExpenseEmail($expenseName, $testEmail, $expenseCode, $expenseAmount, $taskName);
                            MailController::sendExpenseEmail($expenseName, $erpEmail, $expenseCode, $expenseAmount, $taskName, $ccEmail, $expenseId);
                        ### send email to odoo end
                    //send email to odoo end
                }else{
                    $data['submitted_at'] = Carbon::now()->format('Y-m-d H:i:s');
                    DB::table('project_expenses')->where('id',$id)->update($data);
                }

                return redirect()->back()->with('alert-success', 'Data berhasil disimpan');
            }

            //update kwitansi
            $fileName = null;
            $destinationPath = public_path().'/img/expenses/tech/';
            
            // Retrieving An Uploaded File
            $file = $request->file('image');
            if (!empty($file)) {
                $extension = $file->getClientOriginalExtension();
                $fileName = time().'_'.$file->getClientOriginalName();
        
                // Moving An Uploaded File
                $request->file('image')->move($destinationPath, $fileName);

                //removing previous image
                $dataImage = DB::table('project_expenses')->select('image')->where('id', $id)->first();
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
            
            $data['publisher_id'] = $userId;
            $data['publisher_type'] = $userType;
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
    
            if (!empty($fileName)) {
                $data['image'] = $fileName;
            }
    
            DB::table('project_expenses')->where('id',$id)->update($data);

            return redirect()->route('expenses-tech.index','project_id='.$projectId.'&task_id='.$taskId)->with('alert-success','Data berhasil diubah.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Expenses.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data['deleted_at'] = Carbon::now()->format('Y-m-d H:i:s');

        //delete from database
        DB::table('project_expenses')->where('id',$id)->update($data);

        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }

    //customize
    public function report(Request $request)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $projectId = $request->project_id;
        $taskId = $request->task_id;

        $approverDepartment = 3; //finance
        $expenseStatus = 4; //approved by admin
        $expenseSubmittedStatus = 2; //submitted

        //check priviledge
        $dataCountCheck = DB::table('project_expenses')->where('project_id',$projectId)->where('task_id',$taskId)->where('status',$expenseStatus)->where('publisher_id',$userId)->count();

        if ($dataCountCheck > 0) {
            
            ### expense report data ###
                $data['projectTaskInfo'] = DB::table('projects_task as pt')
                ->select([
                    'pt.*',
                    DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name'),
                    //technician
                    DB::raw('(SELECT firstname FROM techs WHERE techs.id = pt.tech_id AND techs.id IS NOT NULL) as tech_firstname'),
                    DB::raw('(SELECT lastname FROM techs WHERE techs.id = pt.tech_id AND techs.id IS NOT NULL) as tech_lastname'),
                    //QC doc
                    DB::raw('(SELECT firstname FROM users WHERE users.id = pt.qcd_id AND users.id IS NOT NULL) as qcd_firstname'),
                    DB::raw('(SELECT lastname FROM users WHERE users.id = pt.qcd_id AND users.id IS NOT NULL) as qcd_lastname'),
                    //QC expense
                    DB::raw('(SELECT firstname FROM users WHERE users.id = pt.qce_id AND users.id IS NOT NULL) as qce_firstname'),
                    DB::raw('(SELECT lastname FROM users WHERE users.id = pt.qce_id AND users.id IS NOT NULL) as qce_lastname'),
                    //pm
                    DB::raw('(SELECT firstname FROM users WHERE users.id = pt.pm_id AND users.id IS NOT NULL) as pm_firstname'),
                    DB::raw('(SELECT lastname FROM users WHERE users.id = pt.pm_id AND users.id IS NOT NULL) as pm_lastname'),
                ])
                ->where('project_id',$projectId)->where('id',$taskId)->where('tech_id',$userId)->first();
            ### expense report data end ###
            
            $data['dataReportExpenses'] = DB::table('project_expenses')->where('project_id',$projectId)->where('task_id',$taskId)->where('status',$expenseStatus)->where('publisher_id',$userId)->get();

            $data['dataReportExpensesCount'] = DB::table('project_expenses')
            ->select(DB::raw('SUM(amount) as total_amount'))
            ->where('project_id',$projectId)
            ->where('task_id',$taskId)
            ->where('status',$expenseStatus)
            ->where('publisher_id',$userId)
            ->where('publisher_type',$userType)
            ->where('deleted_at',null)
            ->first();

            $data['userProfile'] = DB::table('techs')->where('id',$userId)->first();
            $data['approverProfile'] = DB::table('admins')->where('department_id',$approverDepartment)->first();
            $data['dataReportCount'] = DB::table('project_expenses_report')->where('task_id',$taskId)->where('publisher_id',$userId)->where('canceled_at',null)->where('status',$expenseSubmittedStatus)->count();

            return view('tech.expenses.report', $data);
        }

        return redirect()->back()->with('alert-danger','Data masih direview. Anda tidak diijinkan mengakses halaman Expenses.');
    }
}
