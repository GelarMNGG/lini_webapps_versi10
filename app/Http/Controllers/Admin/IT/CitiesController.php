<?php

namespace App\Http\Controllers\Admin\IT;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class CitiesController extends Controller
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
            $data['citiesDatas'] = DB::table('list_of_cities')->get();
            $data['provincesDatas'] = DB::table('list_of_provinces')->get();

            return view('admin.cities.index', $data);
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman administrasi data Kota.');
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
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if ($userCompany == 1 && $userDepartment == 5) {
            $data['propinsiId'] = $request->pid;
            $data['provincesDatas'] = DB::table('list_of_provinces')->get();

            return view('admin.cities.create', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman administrasi data Kota.');
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
            $storeKota = DB::table('list_of_cities');
            $storeKota->insert($request->except(['_token','_method','submit']));
            #$storeKota->save();
            
            return redirect()->route('admin-cities.index')->with('alert-success','Data berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman administrasi data Kota.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman administrasi data Kota.');
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

        if ($userCompany == 1 && $userDepartment == 5) {
            $data['citiesData'] = DB::table('list_of_cities')->where('id', $id)->first();
            $data['provincesDatas'] = DB::table('list_of_provinces')->get();

            return view('admin.cities.edit', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman administrasi data Kota.');
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
        $request->validate([
            'name' => 'required|unique:list_of_cities,name,'.$id,
        ]);

        $updateKota = DB::table('list_of_cities')->where('id', $id);
        $updateKota->update($request->except(['_token','_method','submit']));

        if ($updateKota) {
            return redirect()->route('admin-cities.index')->with('success','Data berhasil ditambahkan');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman administrasi data Kota.');
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
        $userType = Auth::user()->user_type;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if ($userCompany == 1 && $userDepartment == 5) {
            $delKota = DB::table('list_of_cities')->where('id', $id);
            $delKota->delete();

            return redirect()->back()->with('success','Data pengiriman berhasil dihapus.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman administrasi data Kota.');
    }
}
