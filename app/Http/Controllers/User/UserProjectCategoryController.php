<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class UserProjectCategoryController extends Controller
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
        $userDepartment = Auth::user()->department_id;
        $projectId = $request->project_id;

        if($userDepartment == 1 && $userLevel == 3){
            $data['project_id'] = $projectId;
            $data['projectReportCategorys'] = DB::table('project_report_category as prc')
            ->select([
                'prc.*',
                DB::raw('(SELECT name FROM project_report_category_status WHERE project_report_category_status.id = prc.status) as status_name')
            ])
            ->where('publisher_id',$userId)->where('publisher_type', $userType)->where('deleted_at', null)->get();
            
            $data['projectReportSubcategorys'] = DB::table('project_report_subcategory')->where('publisher_id',$userId)->where('publisher_type', $userType)->where('deleted_at', null)->get();

            return view('user.project-category.index', $data);
        }
        if ($userDepartment == 1 && $userLevel == 22 || $userLevel == 4) {
            $data['projectReportCategorys'] = DB::table('project_report_category as prc')
            ->select([
                'prc.*',
                DB::raw('(SELECT name FROM project_report_category_status WHERE project_report_category_status.id = prc.status) as status_name')
            ])
            ->where('deleted_at', null)->get();
            $data['projectReportSubcategorys'] = DB::table('project_report_subcategory')->where('deleted_at', null)->get();

            if ($userLevel == 22) {
                return view('user.project-category.index-co-admin', $data);
            }else{
                return view('user.project-category.index-qc', $data);
            }
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses data Project Category.');
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
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        $data['reportTypes'] = DB::table('project_report_all_format_type')->get();

        //pm
        if($userDepartment == 1 && $userLevel == 3){
            return view('user.project-category.create',$data);
        }

        //co-admin
        if($userDepartment == 1 && $userLevel == 22 || $userLevel == 4){
            return view('user.project-category.create-co-admin',$data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses data Project Category.');
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

        //pm
        if($userDepartment == 1 && $userLevel == 3 || $userLevel == 4){
            $request->validate([
                'name' => 'required|unique:project_report_category',
            ]);
    
            $data = $request->except(['_token','_method','submit']);
            $data['publisher_id'] = $userId;
            $data['publisher_type'] = $userType;
            $data['date_submitted'] = Carbon::now()->format('Y-m-d H:i:s');
            //version 1 (need admin approval)
                //$data['status'] = 3;
            //version 2 (no need admin approval)
            $data['status'] = 1;
    
            DB::table('project_report_category')->insert($data);
    
            return redirect()->route('user-projects-category.index')->with('alert-success','Data berhasil disimpan.');
        }

        //co-admin
        if($userDepartment == 1 && $userLevel == 22){
            $request->validate([
                'name' => 'required|unique:project_report_category',
            ]);
    
            $data = $request->except(['_token','_method','submit']);
            $data['approver_id'] = $userId;
            $data['approver_type'] = $userType;
            $data['status'] = 1;
    
            DB::table('project_report_category')->insert($data);
    
            return redirect()->route('admin-projects-category.index')->with('alert-success','Data berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses data Project Category.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses data Project Category.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        if($userDepartment == 1 && $userLevel == 22 || $userLevel == 4){
            $data['projectReportCategory'] = DB::table('project_report_category as prc')
            ->select([
                'prc.*',
                DB::raw('(SELECT firstname FROM users WHERE users.id = prc.publisher_id) as publisher_firstname'),
                DB::raw('(SELECT lastname FROM users WHERE users.id = prc.publisher_id) as publisher_lastname')
            ])
            ->where('id',$id)->first();

            return view('user.project-category.edit-co-admin', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses data Project Category.');
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
        $userDepartment = Auth::user()->department_id;

        if($userDepartment == 1 && $userLevel == 22 || $userLevel == 4){
            $request->validate([
                'name' => 'required|unique:project_report_category,name,'.$id,
            ]);
            
            $data = $request->except(['_token','_method','submit']);
            $data['approver_id'] = $userId;
            $data['approver_type'] = $userType;
            $data['date_approved'] = Carbon::now()->format('Y-m-d H:i:s');
    
            DB::table('project_report_category')->where('id', $id)->update($data);
    
            return redirect()->route('user-projects-category.index')->with('alert-success','Data berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses data Project Category.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses data Project Category.');
    }
}
