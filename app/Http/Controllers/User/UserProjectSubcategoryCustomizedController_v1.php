<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class UserProjectSubcategoryCustomizedController extends Controller
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
    public function create()
    {
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

        $projectId = $request->project_id;
        $taskId = $request->task_id;

        $subcatname = $request->subcatname;
        $subcatname_check = DB::table('project_report_subcategory')->where('name',$subcatname)->count();
        //check duplicate
        if ($subcatname_check > 0) {
            return redirect()->back()->with('alert-danger','The name has already been taken.');
        }

        if($userDepartment == 1 && $userLevel == 3){
            $request->validate([
                'subcatname' => 'required|unique:project_report_subcategory_customized,name,'.$request->name
            ]);
    
            $data = $request->except(['_token','_method','submit','subcatname']);
            $data['name'] = $subcatname;
            $data['publisher_id'] = $userId;
            $data['publisher_type'] = $userType;
            $data['date_submitted'] = Carbon::now()->format('Y-m-d H:i:s');
            $data['status'] = 3;
    
            DB::table('project_report_subcategory_customized')->insert($data);
    
            return redirect()->route('user-projects-template.index', 'project_id='.$projectId.'&task_id='.$taskId)->with('alert-success','Data berhasil disimpan.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses data Project Sub Category.');
    }
}
