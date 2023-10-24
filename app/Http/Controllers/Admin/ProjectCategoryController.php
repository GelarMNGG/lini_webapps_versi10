<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class ProjectCategoryController extends Controller
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
        $userRole = Auth::user()->role;
        $userDepartment = Auth::user()->department_id;

        if($userDepartment == 1 || $userRole == 1){
            $data['projectReportCategorys'] = DB::table('project_report_category as prc')
            ->select([
                'prc.*',
                DB::raw('(SELECT name FROM project_report_category_status WHERE project_report_category_status.id = prc.status) as status_name')
            ])
            ->where('deleted_at', null)->get();
            $data['projectReportSubcategorys'] = DB::table('project_report_subcategory')->where('deleted_at', null)->get();

            return view('admin.project-category.index', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Category Project.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userRole = Auth::user()->role;
        $userDepartment = Auth::user()->department_id;

        if($userDepartment == 1 || $userRole == 1){
            return view('admin.project-category.create');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Category Project.');
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
        $userRole = Auth::user()->role;
        $userDepartment = Auth::user()->department_id;

        if($userDepartment == 1 || $userRole == 1){
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

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Category Project.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Category Project.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $userRole = Auth::user()->role;
        $userDepartment = Auth::user()->department_id;

        if($userDepartment == 1 || $userRole == 1){
            $data['projectReportCategory'] = DB::table('project_report_category as prc')
            ->select([
                'prc.*',
                DB::raw('(SELECT firstname FROM users WHERE users.id = prc.publisher_id) as publisher_firstname'),
                DB::raw('(SELECT lastname FROM users WHERE users.id = prc.publisher_id) as publisher_lastname')
            ])
            ->where('id',$id)->first();

            return view('admin.project-category.edit', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Category Project.');
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
        $userRole = Auth::user()->role;
        $userDepartment = Auth::user()->department_id;

        if($userDepartment == 1 || $userRole == 1){
            $request->validate([
                'name' => 'required|unique:project_report_category,name,'.$id,
            ]);
            
            $data = $request->except(['_token','_method','submit']);
            $data['approver_id'] = $userId;
            $data['approver_type'] = $userType;
            $data['date_approved'] = Carbon::now()->format('Y-m-d H:i:s');
    
            DB::table('project_report_category')->where('id', $id)->update($data);
    
            return redirect()->route('admin-projects-category.index')->with('alert-success','Data berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Category Project.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $userRole = Auth::user()->role;
        $userDepartment = Auth::user()->department_id;

        if($userDepartment == 1 || $userRole == 1){
            //version 1 - soft delete
                //$data['deleted_at'] = Carbon::now()->format('Y-m-d H:i:s');
                //DB::table('project_report_category')->where('id', $id)->update($data);
                
            //version 2 - delete completely
                DB::table('project_report_category')->delete($id);
    
            return redirect()->route('admin-projects-category.index')->with('alert-success','Data berhasil dihapus.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Category Project.');
    }
}
