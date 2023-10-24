<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class PurchaseRequisitionController extends Controller
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
        $userLevel = Auth::user()->user_level; //3 for PM
        $userDepartment = Auth::user()->department_id;

        //project info
        $projectId = $request->project_id;
        $taskId = $request->task_id;
        $data['infoTaskProject'] = DB::table('projects_task')
        ->select([
            'projects_task.*',
            DB::raw('(SELECT id FROM projects WHERE projects.id = projects_task.project_id) as project_id'),
            DB::raw('(SELECT name FROM projects WHERE projects.id = projects_task.project_id) as project_name')
        ])
        ->where('id',$taskId)->first();

        //default data
        $data['dataPR'] = DB::table('project_purchase_requisition as pr')
        ->select([
            'pr.*',
            DB::raw('(SELECT name FROM projects WHERE projects.id = pr.project_id) as project_name'),
            DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = pr.task_id) as task_name'),
        ])
        ->where('publisher_id',$userId)->where('task_id',$taskId)->get();

        //additional info
        $data['draftCount'] = DB::table('project_purchase_requisition')->where('publisher_id',$userId)->where('task_id',$taskId)->where('status',1)->count();
        $data['reviewedCount'] = DB::table('project_purchase_requisition')->where('publisher_id',$userId)->where('task_id',$taskId)->where('status',2)->count();
        $data['approvedCount'] = DB::table('project_purchase_requisition')->where('publisher_id',$userId)->where('task_id',$taskId)->where('status',3)->count();

        //pr status
        $data['prStatus'] = DB::table('project_purchase_requisition_status')->get();

        //authorized user
        if ($userLevel == 3 && $projectId != null && $taskId != null) {
            return view('user.purchase-requisition.index', $data);
        }
        
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Purchase Requisition.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $userId = Auth::user()->id;
        $userLevel = Auth::user()->user_level; //3 for PM
        $userDepartment = Auth::user()->department_id;

        //project info
        $projectId = $request->project_id;
        $taskId = $request->task_id;
        $data['infoTaskProject'] = DB::table('projects_task')
        ->select([
            'projects_task.*',
            DB::raw('(SELECT id FROM projects WHERE projects.id = projects_task.project_id) as project_id'),
            DB::raw('(SELECT name FROM projects WHERE projects.id = projects_task.project_id) as project_name')
        ])
        ->where('id',$taskId)->first();

        //default data
        $data['dataPR'] = DB::table('project_purchase_requisition')->where('id',$projectId)->first();
        $data['dataCategory'] = DB::table('items_category')->get();

        //authorized user
        if ($userLevel == 3 && $projectId != null && $taskId != null) {
            return view('user.purchase-requisition.create', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Purchase Requisition.');
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
        $userLevel = Auth::user()->user_level; //3 for PM
        $userDepartment = Auth::user()->department_id;
        $projectId = $request->project_id;
        $taskId = $request->task_id;

        //default data
        $data['dataProject'] = DB::table('projects')->where('id',$projectId)->first();

        //authorized user
        if ($userLevel == 3) {
            $request->validate([
                'name' => 'required',
                'number' => 'required|unique:project_purchase_requisition,number,'.$request->number,
                'unit' => 'required',
                'amount' => 'required',
                'budget' => 'required',
                'date' => 'required|after:yesterday',
            ]);
    
            // data setting
            $data = $request->except(['_token','submit']);
            $data['date'] = Carbon::createFromFormat('Y-m-d', $request->date)->format('Y-m-d H:i:s');
            $data['project_id'] = $projectId;
            $data['department_id'] = $userDepartment;
            $data['publisher_id'] = Auth::user()->id;
            $data['publisher_type'] = Auth::user()->user_type;
    
            //insert to database
            DB::table('project_purchase_requisition')->insert($data);
    
            return redirect()->route('user-pr.index','project_id='.$projectId.'&task_id='.$taskId)->with('alert-success','Pengajuan PR berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Purchase Requisition.');
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
        $userLevel = Auth::user()->user_level; //3 for PM
        $userDepartment = Auth::user()->department_id;

        //project info
        $projectId = $request->project_id;
        $taskId = $request->task_id;
        $data['infoTaskProject'] = DB::table('projects_task')
        ->select([
            'projects_task.*',
            DB::raw('(SELECT id FROM projects WHERE projects.id = projects_task.project_id) as project_id'),
            DB::raw('(SELECT name FROM projects WHERE projects.id = projects_task.project_id) as project_name')
        ])
        ->where('id',$taskId)->first();

        //authorized user
        if ($userLevel == 3 && $projectId != null && $taskId != null) {

            //data
            $data['dataPR'] = DB::table('project_purchase_requisition as pr')
            ->select([
                'pr.*',
                DB::raw('(SELECT firstname FROM users WHERE users.id = pr.publisher_id) as firstname'),
                DB::raw('(SELECT lastname FROM users WHERE users.id = pr.publisher_id) as lastname'),
                DB::raw('(SELECT name FROM department WHERE department.id = pr.department_id) as department_name'),
                DB::raw('(SELECT name FROM items_category WHERE items_category.id = pr.category_id) as category_name'),
            ])
            ->where('id',$id)->first();
    
            $projectId = $data['dataPR']->project_id;
    
            //default data
            $data['dataProject'] = DB::table('projects as proj')
            ->select([
                'proj.*',
                DB::raw('(SELECT firstname FROM users WHERE users.id = proj.pc_id) as pc_firstname'),
                DB::raw('(SELECT lastname FROM users WHERE users.id = proj.pc_id) as pc_lastname'),
            ])
            ->where('id',$projectId)->first();

            return view('user.purchase-requisition.show',$data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Purchase Requisition.');
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
        $userLevel = Auth::user()->user_level; //3 for PM
        $userDepartment = Auth::user()->department_id;

        //project info
        $data['infoTaskProject'] = DB::table('project_purchase_requisition')
        ->select([
            'project_purchase_requisition.*',
            DB::raw('(SELECT id FROM projects WHERE projects.id = project_purchase_requisition.project_id) as project_id'),
            DB::raw('(SELECT name FROM projects WHERE projects.id = project_purchase_requisition.project_id) as project_name'),
            DB::raw('(SELECT id FROM projects_task WHERE projects_task.id = project_purchase_requisition.task_id) as task_id'),
            DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = project_purchase_requisition.task_id) as task_name')
        ])
        ->where('id',$id)->first();
        $projectId = $data['infoTaskProject']->project_id;
        $taskId = $data['infoTaskProject']->task_id;

        //default data
        $data['dataPR'] = DB::table('project_purchase_requisition')->where('id',$id)->first();
        $data['dataCategory'] = DB::table('items_category')->get();

        //authorized user
        if ($userLevel == 3 && $projectId != null && $taskId != null) {
            return view('user.purchase-requisition.edit', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Purchase Requisition.');
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
        $userLevel = Auth::user()->user_level; //3 for PM
        $userDepartment = Auth::user()->department_id;
        $projectId = $request->project_id;
        $taskId = $request->task_id;

        if (isset($request->status)) {
            //update status
            $dateNow = Carbon::now();
            $data['date_submitted'] = $dateNow->toDateTimeString();
            $data['status'] = $request->status;
            DB::table('project_purchase_requisition')->where('id',$id)->update($data);

            return redirect()->route('user-pr.index','project_id='.$projectId.'&task_id='.$taskId)->with('alert-success','Pengajuan PR berhasil dikirimkan ke procurement.');
        }

        //authorized user
        if ($userLevel == 3 && $projectId != null && $taskId != null) {
            $request->validate([
                'name' => 'required',
                'number' => 'required|unique:project_purchase_requisition,number,'.$id,
                'unit' => 'required',
                'amount' => 'required',
                'budget' => 'required',
                'date' => 'required|after:yesterday',
            ]);
    
            // data setting
            $request['date'] = Carbon::createFromFormat('Y-m-d', $request->date)->format('Y-m-d H:i:s');
            $request['project_id'] = $projectId;
            $request['department_id'] = $userDepartment;
            $request['publisher_id'] = Auth::user()->id;
            $request['publisher_type'] = Auth::user()->user_type;
    
            //insert to database
            DB::table('project_purchase_requisition')->where('id',$id)->update($request->except('_token','_method','submit'));
    
            return redirect()->route('user-pr.index','project_id='.$projectId.'&task_id='.$taskId)->with('alert-success','Pengajuan PR berhasil disimpan.');
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
