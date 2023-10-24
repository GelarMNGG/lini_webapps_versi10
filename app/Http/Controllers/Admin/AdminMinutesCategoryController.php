<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class AdminMinutesCategoryController extends Controller
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
        $userDepartment = Auth::user()->department_id;

        $data['minuteCategory'] = DB::table('minutes_category')->where('department_id',$userDepartment)->orderBy('id','DESC')->paginate(10);

        return view('admin.minutes-category.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        if ($userType == 'admin') {
            return view('admin.minutes-category.create');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Add Minute Category.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userType = Auth::user()->user_type;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $request->validate([
            'name' => 'required',
        ]);

        if ($userType == 'admin') {
    
            $data = $request->except(['_token','_method','submit']);
            $data['company_id'] = $userCompany;
            $data['department_id'] = $userDepartment;

            DB::table('minutes_category')->insert($data);

            return redirect()->route('admin-minutes-category.index')->with('success','Data berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Add Minute Category.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Add Minute Category.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        
        $dataCheck = DB::table('minutes_category')->where('id',$id)->where('department_id',$userDepartment)->count();
        
        if ($dataCheck > 0 && $userType == 'admin') {

            $data['minuteCategory'] = DB::table('minutes_category')->where('id',$id)->where('department_id',$userDepartment)->first();

            if (!isset($data['minuteCategory'])) {
                return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
            }
    
            return view('admin.minutes-category.edit',$data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Add Minute Category.');
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
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        $request->validate([
            'name' => 'required',
        ]);

        $dataCheck = DB::table('minutes_category')->where('id',$id)->where('department_id',$userDepartment)->count();
        
        if ($dataCheck > 0 && $userType == 'admin') {
    
            $data = $request->except(['_token','_method','submit']);

            DB::table('minutes_category')->where('id',$id)->update($data);

            return redirect()->route('admin-minutes-category.index')->with('success','Data berhasil diubah.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Add Minute Category.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        $dataCheck = DB::table('minutes_category')->where('id',$id)->where('department_id',$userDepartment)->count();

        if ($dataCheck > 0 && $userType == 'admin') {
            //delete from database
            DB::table('minutes_category')->delete($id);
    
            return redirect()->route('admin-minutes-category.index')->with('alert-success', 'Data berhasil dihapus.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Minute Category.');
    }
}
