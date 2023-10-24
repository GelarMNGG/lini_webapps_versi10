<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class UserMinutesCategoryController extends Controller
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
        $userLevel = Auth::user()->user_level;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $coAdmin = 22; //coadmin
        $liniId = 1;

        if ($userLevel == $coAdmin) {
            if ($userCompany == $liniId) {
                $data['minuteCategory'] = DB::table('minutes_category')->where('department_id',$userDepartment)->orderBy('id','DESC')->paginate(10);
            }else{
                $data['minuteCategory'] = DB::table('minutes_category')->where('company_id',$userCompany)->orderBy('id','DESC')->paginate(10);
            }
    
            return view('user.minutes-category.index', $data);
        }


        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Minutes Category.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userLevel = Auth::user()->user_level;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $coAdmin = 22; //coadmin
        $liniId = 1;

        if ($userLevel == $coAdmin) {
            if ($userCompany == $liniId) {
                $data['minuteCategory'] = DB::table('minutes_category')->where('department_id',$userDepartment)->orderBy('id','DESC')->paginate(10);
            }else{
                $data['minuteCategory'] = DB::table('minutes_category')->where('company_id',$userCompany)->orderBy('id','DESC')->paginate(10);
            }

            return view('user.minutes-category.create');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Minutes Category.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userLevel = Auth::user()->user_level;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $coAdmin = 22; //coadmin
        $liniId = 1;

        $request->validate([
            'name' => 'required',
        ]);

        if ($userLevel == $coAdmin) {
    
            $data = $request->except(['_token','_method','submit']);
            $data['company_id'] = $userCompany;
            $data['department_id'] = $userDepartment;

            DB::table('minutes_category')->insert($data);

            return redirect()->route('user-minutes-category.index')->with('success','Data berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Minutes Category.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Minutes Category.');
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
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $coAdmin = 22; //coadmin
        $liniId = 1;

        if ($userCompany == $liniId) {
            $dataCheck = DB::table('minutes_category')->where('id',$id)->where('department_id',$userDepartment)->count();
        }else{
            $dataCheck = DB::table('minutes_category')->where('id',$id)->where('company_id',$userCompany)->count();
        }
        
        if ($dataCheck > 0 && $userLevel == $coAdmin) {

            $data['minuteCategory'] = DB::table('minutes_category')->where('id',$id)->where('department_id',$userDepartment)->first();

            if (!isset($data['minuteCategory'])) {
                return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
            }
    
            return view('user.minutes-category.edit',$data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Minutes Category.');
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
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $coAdmin = 22; //coadmin
        $liniId = 1;

        $request->validate([
            'name' => 'required',
        ]);

        if ($userCompany == $liniId) {
            $dataCheck = DB::table('minutes_category')->where('id',$id)->where('department_id',$userDepartment)->count();
        }else{
            $dataCheck = DB::table('minutes_category')->where('id',$id)->where('company_id',$userCompany)->count();
        }
        
        if ($dataCheck > 0 && $userLevel == $coAdmin) {
    
            $data = $request->except(['_token','_method','submit']);

            DB::table('minutes_category')->where('id',$id)->update($data);

            return redirect()->route('user-minutes-category.index')->with('success','Data berhasil diubah.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Minutes Category.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Minutes Category.');
    }
}
