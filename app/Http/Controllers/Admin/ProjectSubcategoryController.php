<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class ProjectSubcategoryController extends Controller
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
            $data['projectReportSubcategorys'] = DB::table('project_report_subcategory')->where('deleted_at', null)->get();

            return view('admin.project-subcategory.index', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Subcategory Project.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $userRole = Auth::user()->role;
        $userDepartment = Auth::user()->department_id;

        $catId = $request->cat_id;

        if($userDepartment == 1 || $userRole == 1){
            $data['cat_id'] = $catId;

            return view('admin.project-subcategory.create', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Subcategory Project.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userRole = Auth::user()->role;
        $userDepartment = Auth::user()->department_id;

        if($userDepartment == 1 || $userRole == 1){
            $request->validate([
                'name' => 'required|unique:project_report_subcategory',
            ]);
    
            $data = $request->except(['_token','_method','submit']);
            $data['status'] = 1;
    
            DB::table('project_report_subcategory')->insert($data);
    
            return redirect()->route('admin-projects-category.index')->with('alert-success','Data berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Subcategory Project.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Subcategory Project.');
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
            $data['projectReportSubcategory'] = DB::table('project_report_subcategory as prs')
            ->select([
                'prs.*',
                DB::raw('(SELECT firstname FROM users WHERE users.id = prs.publisher_id) as publisher_firstname'),
                DB::raw('(SELECT lastname FROM users WHERE users.id = prs.publisher_id) as publisher_lastname')
            ])
            ->where('id',$id)->first();

            return view('admin.project-subcategory.edit', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Subcategory Project.');
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
        $userRole = Auth::user()->role;
        $userDepartment = Auth::user()->department_id;

        if($userDepartment == 1 || $userRole == 1){
            $request->validate([
                'name' => 'required',
            ]);
            
            $data = $request->except(['_token','_method','submit']);
    
            DB::table('project_report_subcategory')->where('id', $id)->update($data);
    
            return redirect()->route('admin-projects-category.index')->with('alert-success','Data berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Subcategory Project.');
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
            $data['deleted_at'] = Carbon::now()->format('Y-m-d H:i:s');
    
            DB::table('project_report_subcategory')->where('id', $id)->update($data);
    
            return redirect()->route('admin-projects-category.index')->with('alert-success','Data berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Subcategory Project.');
    }
}
