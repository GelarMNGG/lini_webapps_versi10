<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class UserProjectSubcategoryController extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses data Project Sub Category.');
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
        $catId = $request->cat_id;

        //pm
        if($userDepartment == 1 && $userLevel == 3){
            $data['cat_id'] = $catId;

            return view('user.project-subcategory.create', $data);
        }

        //co-admin & QC
        if($userDepartment == 1 && $userLevel == 22 || $userLevel == 4){
            $data['cat_id'] = $catId;

            return view('user.project-subcategory.create-co-admin', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses data Project Sub Category.');
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
        if($userDepartment == 1 && $userLevel == 3){
            $request->validate([
                'name' => 'required',
            ]);
    
            $data = $request->except(['_token','_method','submit']);
            $data['publisher_id'] = $userId;
            $data['publisher_type'] = $userType;
            $data['date_submitted'] = Carbon::now()->format('Y-m-d H:i:s');
            //version 1 (need admin approval)
                //$data['status'] = 3;
            //version 2 (no need admin approval)
            $data['status'] = 1;
    
            DB::table('project_report_subcategory')->insert($data);
    
            return redirect()->route('user-projects-category.index')->with('alert-success','Data berhasil disimpan.');
        }

        //co-admin & QC
        if($userDepartment == 1 && $userLevel == 22 || $userLevel == 4){
            $request->validate([
                'name' => 'required|unique:project_report_subcategory',
            ]);
    
            $data = $request->except(['_token','_method','submit','page']);
            $data['publisher_id'] = $userId;
            $data['publisher_type'] = $userType;
            $data['date_submitted'] = Carbon::now()->format('Y-m-d H:i:s');
            $data['status'] = 1;
    
            DB::table('project_report_subcategory')->insert($data);

            if (isset($request->page)) {
                return redirect()->back()->with('alert-success','Sub kategori berhasil ditambahkan.');
            }else{
                return redirect()->route('user-projects-category.index')->with('alert-success','Data berhasil disimpan.');
            }
    
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses data Project Sub Category.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses data Project Sub Category.');
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
            $data['projectReportSubcategory'] = DB::table('project_report_subcategory as prs')
            ->select([
                'prs.*',
                DB::raw('(SELECT firstname FROM users WHERE users.id = prs.publisher_id) as publisher_firstname'),
                DB::raw('(SELECT lastname FROM users WHERE users.id = prs.publisher_id) as publisher_lastname')
            ])
            ->where('id',$id)->first();

            return view('user.project-subcategory.edit-co-admin', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses data Project Sub Category.');
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
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        if($userDepartment == 1 && $userLevel == 22 || $userLevel == 4){
            $request->validate([
                'name' => 'required',
            ]);
            
            $data = $request->except(['_token','_method','submit']);
    
            DB::table('project_report_subcategory')->where('id', $id)->update($data);
    
            return redirect()->route('user-projects-category.index')->with('alert-success','Data berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses data Project Sub Category.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $userDepartment = Auth::user()->department_id;
        $userLevel = Auth::user()->user_level;

        if($userDepartment == 1 && $userLevel == 22){
            //version 1 - soft delete
                //$data['deleted_at'] = Carbon::now()->format('Y-m-d H:i:s');
                //DB::table('project_report_subcategory')->where('id', $id)->update($data);
                
            //version 2 - delete completely
                DB::table('project_report_subcategory')->delete($id);
    
            return redirect()->route('user-projects-category.index')->with('alert-success','Data berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses data Project Sub Category.');
    }
}
