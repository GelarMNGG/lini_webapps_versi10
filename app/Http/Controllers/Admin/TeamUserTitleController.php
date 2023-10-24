<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class TeamUserTitleController extends Controller
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
        $userId = Auth::user()->id;
        $userDepartment = Auth::user()->department_id;

        $allDepartment = 0; //all department

        $data['userLevels'] = DB::table('users_level')->where('department_id',$allDepartment)->orWhere('department_id',$userDepartment)->where('deleted_at',NULL)->paginate(10);

        return view('admin.teamuser-title.index',$data);
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
            return view('admin.teamuser-title.create');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Title.');
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
        $userDepartment = Auth::user()->department_id;

        $request->validate([
            'name' => 'required|unique:users_level,name,'.$request->name
        ]);

        if ($userType == 'admin') {

            $data = $request->except(['_token','submit']);
            $data['department_id'] = $userDepartment;

            DB::table('users_level')->insert($data);

            return redirect()->route('teamusertitle.index')->with('success','Data berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Title.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Title.');
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
        
        $dataCheck = DB::table('users_level')->where('id',$id)->where('department_id',$userDepartment)->count();
        
        if ($dataCheck > 0 && $userType == 'admin') {

            $data['userLevel'] = DB::table('users_level')->where('id',$id)->where('department_id',$userDepartment)->first();

            if (!isset($data['userLevel'])) {
                return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
            }
    
            return view('admin.teamuser-title.edit',$data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Title.');
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

        $dataCheck = DB::table('users_level')->where('id',$id)->where('department_id',$userDepartment)->count();
        
        if ($dataCheck > 0 && $userType == 'admin') {
    
            $data = $request->except(['_token','_method','submit']);

            DB::table('users_level')->where('id',$id)->update($data);

            return redirect()->route('teamusertitle.index')->with('success','Data berhasil diubah.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Title.');
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

        $dataCheck = DB::table('users_level')->where('id',$id)->where('department_id',$userDepartment)->count();

        if ($dataCheck > 0 && $userType == 'admin') {
            //delete from database
            $data['deleted_at'] = Carbon::now()->format('Y-m-d H:i:s');
            DB::table('users_level')->where('id',$id)->update($data);
    
            return redirect()->back()->with('alert-success', 'Data berhasil dihapus.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Title.');
    }
}
