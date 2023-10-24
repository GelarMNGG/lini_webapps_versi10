<?php

namespace App\Http\Controllers\Tech;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class TechProjectPaymentSummaryController extends Controller
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

        $expenseStatus = 4; //approved by pm
        $caStatus = 4; //approved by pm
        $prStatus = 3; //approved by pm
        $psStatus = 3; //approved by pm

        //check priviledge
        $dataCountCheck = DB::table('projects_task')->where('id',$taskId)->where('tech_id',$userId)->where('deleted_at',null)->count();
        
        if ($dataCountCheck > 0) {

            ### project task data ###
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
                ->where('project_id',$projectId)->where('id',$taskId)->where('tech_id',$userId)->where('deleted_at',null)->first();
            ### project task data end ###
            
            ### expense data ###
                $data['projectExpensesReport'] = DB::table('project_expenses_report as ptr')->where('project_id',$projectId)->where('task_id',$taskId)->where('publisher_id',$userId)->first();
            ### expense data end ###
            
            ### cash advance data ###
                $data['projectTotalCA'] = DB::table('project_cash_advance')
                ->where('project_id',$projectId)->where('task_id',$taskId)
                ->where('status',$caStatus)
                ->where('publisher_id',$userId)
                ->sum('project_cash_advance.amount');
            ### cash advance data end ###

            ### expense data ###
                $data['projectTotalExpense'] = DB::table('project_expenses')
                ->where('project_id',$projectId)->where('task_id',$taskId)
                ->where('status',$expenseStatus)
                ->where('publisher_id',$userId)
                ->where('deleted_at',null)
                ->sum('project_expenses.amount');

                //check expense report
                $data['checkExpenseReport'] = DB::table('project_expenses_report')
                ->where('project_id',$projectId)->where('task_id',$taskId)
                ->where('status',$expenseStatus)
                ->where('publisher_id',$userId)
                ->first();
            ### expense data end ###

            ### expense data ###
                $data['projectTotalPR'] = DB::table('project_purchase_requisition')
                ->where('project_id',$projectId)->where('task_id',$taskId)
                ->where('status',$prStatus)
                ->where('deleted_at',null)
                //->where('publisher_id',$userId)
                ->sum('project_purchase_requisition.budget');
            ### expense data end ###
                
            ### tech payment data ###
                $data['paymentSummaryDatas'] = DB::table('project_payment_summary')
                ->where('project_id',$projectId)->where('task_id',$taskId)
                ->where('publisher_id',$userId)
                ->where('deleted_at',null)
                ->get();

                $data['paymentSummaryTotal'] = DB::table('project_payment_summary')
                ->where('project_id',$projectId)->where('task_id',$taskId)
                ->where('status',$psStatus)
                ->where('publisher_id',$userId)
                ->where('deleted_at',null)
                ->sum('project_payment_summary.amount');
            ### tech payment data end ###
            
            ### LINI payment data ###
                $data['paymentSummaryLiniDatas'] = DB::table('project_payment_summary')
                ->where('project_id',$projectId)->where('task_id',$taskId)
                ->where('publisher_id','!=',$userId)
                ->where('deleted_at',null)
                ->get();

                $data['paymentSummaryLiniTotal'] = DB::table('project_payment_summary')
                ->where('project_id',$projectId)->where('task_id',$taskId)
                //->where('status',$psStatus)
                ->where('publisher_id','!=',$userId)
                ->where('deleted_at',null)
                ->sum('project_payment_summary.amount');
            ### LINI payment data end ###
            
            ### payment status ###
                $data['paymentSummaryStatus'] = DB::table('project_payment_summary_status')->get();
            ### payment status ###

            return view('tech.report.payment-report', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Payment Summary.');
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

        $expenseStatus = 4; //approved by pm
        $caStatus = 4; //approved by pm
        $prStatus = 3; //approved by pm

        //check priviledge
        $dataCountCheck = DB::table('projects_task')->where('id',$taskId)->where('tech_id',$userId)->where('deleted_at',null)->count();
        
        if ($dataCountCheck > 0) {

            ### expense report data ###
                $data['projectTaskInfo'] = DB::table('projects_task as pt')
                ->select([
                    'pt.*',
                    DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name'),
                ])
                ->where('project_id',$projectId)->where('id',$taskId)->where('tech_id',$userId)->first();
            ### expense report data end ###

            return view('tech.payment.create', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Payment Summary.');
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
        $firstCheck = DB::table('projects_task')->where('id',$taskId)->where('tech_id',$userId)->count();

        if ($firstCheck > 0) {

            $request->validate([
                'title' => 'required|min:5',
                'amount' => 'required',
                'image' => 'required|mimes:jpeg,jpg,png,pdf|max:9216',
            ]);
    
            //file handler
            $fileName = null;
            $destinationPath = public_path().'/img/projects/report/payment/tech/';
            
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
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
    
            if (!empty($fileName)) {
                $data['image'] = $fileName;
            }
    
            DB::table('project_payment_summary')->insert($data);
    
            return redirect()->route('payment-summary-tech.index','project_id='.$projectId.'&task_id='.$taskId)->with('alert-success','Data berhasil disimpan.');
        }
        
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Payment Summary.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Payment Summary.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Payment Summary.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Payment Summary.');
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

        $projectId = $request->project_id;
        $taskId = $request->task_id;

        //check priviledge
        $firstCheck = DB::table('projects_task')->where('id',$taskId)->where('tech_id',$userId)->count();

        if ($firstCheck > 0) {
            $data['deleted_at'] = Carbon::now()->format('Y-m-d H:i:s');

            DB::table('project_payment_summary')->where('id',$id)->update($data);

            return redirect()->back()->with('alert-success','Data berhasil dihapus.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Payment Summary.');
    }
}
