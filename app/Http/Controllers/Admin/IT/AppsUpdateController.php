<?php

namespace App\Http\Controllers\Admin\IT;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class AppsUpdateController extends Controller
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
        $userType = Auth::user()->user_type;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if ($userCompany == 1 && $userDepartment == 5) {
            $data['appsUpdateDatas'] = DB::table('apps_update as au')
            ->select([
                'au.*',
                DB::raw('(SELECT firstname FROM admins WHERE admins.id = au.updater_id) updater_firstname'),
                DB::raw('(SELECT lastname FROM admins WHERE admins.id = au.updater_id) updater_lastname'),
                //category name
                DB::raw('(SELECT name FROM apps_update_categories WHERE apps_update_categories.id = au.cat_id) cat_name'),
            ])
            ->where('updater_id',$userId)
            ->where('updater_type',$userType)
            ->orderBy('created_at','DESC')->get();

            return view('admin.apps-update.index-table', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Apps Update.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userId = Auth::user()->id;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if ($userCompany == 1 && $userDepartment == 5) {
            //supporting data
            $data['appsUpdateCats'] = DB::table('apps_update_categories')->get();

            return view('admin.apps-update.create',$data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Apps Update.');
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
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if ($userCompany == 1 && $userDepartment == 5) {
            //supporting data
            $data = $request->except(['_token','submit']);

            $data['updater_id'] = $userId;
            $data['updater_type'] = $userType;
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');

            DB::table('apps_update')->insert($data);

            return redirect()->route('apps-update.index')->with('alert-success','Data berhasil disimpan');
        }
        

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Apps Update.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Apps Update.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $firstCheck = DB::table('apps_update')->where('id',$id)->first();

        if ($userCompany == 1 && $userDepartment == 5 && isset($firstCheck)) {
            //supporting data
            $data['appsUpdateCats'] = DB::table('apps_update_categories')->get();
            $data['appsUpdateData'] = $firstCheck;

            return view('admin.apps-update.edit',$data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Apps Update.');
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
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $firstCheck = DB::table('apps_update')->where('id',$id)->first();

        if ($userCompany == 1 && $userDepartment == 5 && isset($firstCheck)) {
            $data = $request->except(['_token','submit','_method']);

            DB::table('apps_update')->where('id',$id)->update($data);

            return redirect()->route('apps-update.index')->with('alert-success','Data berhasil diperbarui');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Apps Update.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $userId = Auth::user()->id;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if ($userCompany == 1 && $userDepartment == 5) {

            DB::table('apps_update')->delete($id);

            return redirect()->route('apps-update.index')->with('alert-success','Data berhasil disimpan');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Apps Update.');
    }
}
