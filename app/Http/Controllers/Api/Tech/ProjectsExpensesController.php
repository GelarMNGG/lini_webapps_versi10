<?php

namespace App\Http\Controllers\Api\Tech;

use App\Http\Controllers\Api\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class ProjectsExpensesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:tech-api', ['except' => ['login']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request)
    {
        $user = $this->authUser();

        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }

        if (!isset($request->project_id)) {
            return response()->json(['error' => 'Maaf, project id tidak tersedia.']);
        } 

        if (!isset($request->task_id)) {
            return response()->json(['error' => 'Maaf, task id tidak tersedia.']);
        } 

        $projectId = $request->project_id;
        $taskId = $request->task_id;
        $techId = $user->id;

        $expenseStatus = 4; //approved by admin
        $expenseSubmittedStatus = 2; //submitted

        //check priviledge
        $dataCountCheck = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('tech_id',$user->id)->where('deleted_at',null)->count();

        $data['dataFinishedCount'] = DB::table('project_expenses')->where('project_id',$projectId)->where('task_id',$taskId)->where('publisher_id',$user->id)->where('status',$expenseStatus)->count();
        
        if ($dataCountCheck > 0) {

            //getting data
            
            //data task
            $data['projectTask'] = DB::table('projects_task as pt')
            ->select([
                'name',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
            ])
            ->where('id',$taskId)->where('tech_id',$user->id)->where('deleted_at',null)->first();
                
            $data['dataExpenses'] = DB::table('project_expenses as pe')
            ->select([
                'id',
                'name',
                'amount',
                'rejected_at',
                'submitted_at',
                'approved_by_pm_at',
                'created_at',
                'image',

                DB::raw('(SELECT name FROM project_expenses_status WHERE project_expenses_status.id = pe.status) as status_name'),
                DB::raw('(SELECT description FROM project_product_reference WHERE project_product_reference.id = pe.code) as code_name'),
                DB::raw('(SELECT code FROM project_product_reference WHERE project_product_reference.id = pe.code) as code_id'),
                DB::raw('(SELECT COUNT(expense_id) FROM project_expenses_files WHERE project_expenses_files.expense_id = pe.id) as expenses_files_count'),
                DB::raw('(SELECT image FROM project_expenses_files WHERE project_expenses_files.id = pe.image) as image'),
            ])
            ->where('project_id',$projectId)
            ->where('task_id',$taskId)
            ->where('publisher_id',$user->id)->get();

            //additional data
            // $data['dataExpensesStatus'] = DB::table('project_expenses_status')->get();

            $data['dataExpenseCount'] = DB::table('project_expenses')->where('task_id',$taskId)->where('publisher_id',$user->id)->where('status','>',$expenseSubmittedStatus)->count();

            $data['dataReportCount'] = DB::table('project_expenses_report')->where('task_id',$taskId)->where('publisher_id',$user->id)->where('status',$expenseSubmittedStatus)->where('canceled_at',null)->count();

            $data['dataExpensesImages'] = DB::table('project_expenses_files')->where('task_id',$taskId)->where('tech_id',$user->id)->get();
            //$data['dataExpensesImages'] = DB::table('project_expenses_files')->where('tech_id',$userId)->get();

            return response()->json($data);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $this->authUser();

        $data = $request->only(['task_id','name','amount','code']);

        if (!$data) {
            return response()->json(['error' => 'Anda tidak diijinkan mengakses halaman ini.']);
        }

        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }

        if (!isset($data['task_id'])) {
            return response()->json(['error' => 'Maaf, task id tidak tersedia.']);
        }
        if (!isset($data['name'])) {
            return response()->json(['error' => 'Maaf, Anda harus mengisi nama pemngeluaran.']);
        }
        if (!isset($data['amount'])) {
            return response()->json(['error' => 'Maaf, Anda harus mengisi jumlah pengeluaran.']);
        }
        if (!isset($data['code'])) {
            return response()->json(['error' => 'Maaf, Anda harus mengisi kode produk.']);
        }

        $taskId = $data['task_id'];
        $techId = $user->id;
        $userType = $user->user_type;
        $projectId = $request->project_id;
        $taskId = $request->task_id;

        //check priviledge
        $firstCheck = DB::table('projects_task')->where('id',$taskId)->where('tech_id',$user->id)->first();

        if (isset($firstCheck)) {

            $request->validate([
                'name' => 'required',
                'amount' => 'required',
                'code' => 'required'
                //'image' => 'required|mimes:jpeg,jpg,png|max:9216',
            ]);
        
            //custom setting to support file upload
            $data = $request->except(['_token','submit']);
            $data['publisher_id'] = $user->id;
            $data['publisher_type'] = $userType;
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
    
            DB::table('project_expenses')->insert($data);

            return response()->json(['message' => 'Data berhasil disimpan.']);
        }
        return response()->json(['error' => 'Terjadi kesalahan koneksi, data tidak berhasil disimpan.']);
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
        $user = $this->authUser();

        $data = $request->only(['task_id','name','amount','code']);

        if (!$data) {
            return response()->json(['error' => 'Anda tidak diijinkan mengakses halaman ini.']);
        }

        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }

        if (!isset($data['task_id'])) {
            return response()->json(['error' => 'Maaf, task id tidak tersedia.']);
        }
        if (!isset($data['name'])) {
            return response()->json(['error' => 'Maaf, Anda harus mengisi nama pemngeluaran.']);
        }
        if (!isset($data['amount'])) {
            return response()->json(['error' => 'Maaf, Anda harus mengisi jumlah pengeluaran.']);
        }
        if (!isset($data['code'])) {
            return response()->json(['error' => 'Maaf, Anda harus mengisi kode produk.']);
        }

        $taskId = $data['task_id'];
        $techId = $user->id;
        $userType = $user->user_type;
        $projectId = $request->project_id;
        $taskId = $request->task_id;

        //check priviledge
        $dataCountCheck = DB::table('projects_task')->where('id',$taskId)->where('tech_id',$user->id)->where('deleted_at',null)->count();

        if ($dataCountCheck > 0) {
            //update status
            if (isset($request->status)) {
                $data = $request->except(['_token','_method','submit','expense_id','task_name']);
                $data['status'] = $request->status;
                $data['submitted_at'] = Carbon::now()->format('Y-m-d H:i:s');
                
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
                return response()->json(['message' =>'Data berhasil diubah.']);
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
            
            $data['publisher_id'] = $user->id;
            $data['publisher_type'] = $userType;
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
    
            if (!empty($fileName)) {
                $data['image'] = $fileName;
            }
    
            DB::table('project_expenses')->where('id',$id)->update($data);

            return response()->json(['message' =>'Data kwitansi berhasil diubah.']);    
        }
        return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = $this->authUser();
        $techId = $user->id;
        $userType = $user->user_type;

        $data['deleted_at'] = Carbon::now()->format('Y-m-d H:i:s');
        DB::table('project_expenses')->where('id',$id)->update($data);

        return response()->json(['message' => 'Data berhasil dihapus.',$data]);
    
        // return response()->json(['error' => 'Terjadi kesalahan koneksi, data tidak berhasil dihapus.']);
    }

    public function codeproduct()
    {
        $user = $this->authUser();

        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }

        $techId = $user->id;

        $data['productCodes'] = DB::table('project_product_reference')->get();

        return response()->json($data);

    }
}
