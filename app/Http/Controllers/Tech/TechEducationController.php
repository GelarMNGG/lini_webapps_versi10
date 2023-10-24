<?php

namespace App\Http\Controllers\Tech;

use App\Tech;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Auth;
use DB;

class TechEducationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:tech');
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
        $userDepartment = 1; //project department

        $firstCheck = DB::table('proc_tech_education_info')->where('tech_id',$userId)->first();

        if (!isset($firstCheck)) {
            return redirect()->route('tech-input-data-pendidikan.create');
        }
        
        $data['educationDatas'] = DB::table('proc_tech_education_info as ptei')
        ->select([
            'ptei.*',
            DB::raw('(SELECT name FROM proc_tech_education_info_level WHERE proc_tech_education_info_level.id = ptei.level) as level_name'),
            DB::raw('(SELECT name FROM list_of_provinces WHERE list_of_provinces.id = ptei.province) as province_name'),
            DB::raw('(SELECT name FROM list_of_cities WHERE list_of_cities.id = ptei.city) as city_name'),
        ])
        ->where('tech_id',$userId)->paginate(10);

        //supporting data
        $data['educationLevels'] = DB::table('proc_tech_education_info_level')->get();
        $data['cityDatas'] = DB::table('list_of_cities')->get();
        $data['provinceDatas'] = DB::table('list_of_provinces')->get();

        return view('tech.data-diri.education-info.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userId = Auth::user()->id;

        //supporting data
        $data['educationLevels'] = DB::table('proc_tech_education_info_level')->get();
        $data['cityDatas'] = DB::table('list_of_cities')->get();
        $data['provinceDatas'] = DB::table('list_of_provinces')->get();

        return view('tech.data-diri.education-info.create', $data);
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

        $errorMessage = [
            'name.required' => 'Field ini tidak boleh kosong.',
            'level.required' => 'Field ini tidak boleh kosong.',
            'city.required' => 'Field ini tidak boleh kosong.',
            'year.required' => 'Field ini tidak boleh kosong.',
        ];
        $validation = Validator::make($request->all(),[
            'name.required' => 'required.',
            'level.required' => 'required.',
            'city.required' => 'required.',
            'year.required' => 'required.',
        ],$errorMessage);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        //custom setting to support file upload
        $data = $request->except(['_token','_method','submit']);
        $data['tech_id'] = $userId;
        
        DB::table('proc_tech_education_info')->insert($data);

        return redirect()->back()->with('alert-success','Data berhasil disimpan');
        //return redirect()->route('tech-input-data-pendidikan.index')->with('alert-success','Data berhasil disimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman input data pendidikan.');
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

        $firstCheck = DB::table('proc_tech_education_info')->where('id',$id)->where('tech_id',$userId)->first();
        
        if (!isset($firstCheck)) {
            return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
        }

        $data['techEducationInfo'] = $firstCheck;

        //supporting data
        $data['educationLevels'] = DB::table('proc_tech_education_info_level')->get();
        $data['cityDatas'] = DB::table('list_of_cities')->get();
        $data['provinceDatas'] = DB::table('list_of_provinces')->get();

        return view('tech.data-diri.education-info.edit',$data);
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
        $errorMessage = [
            'name.required' => 'Field ini tidak boleh kosong.',
            'level.required' => 'Field ini tidak boleh kosong.',
            'city.required' => 'Field ini tidak boleh kosong.',
            'year.required' => 'Field ini tidak boleh kosong.',
        ];
        $validation = Validator::make($request->all(),[
            'name.required' => 'required.',
            'level.required' => 'required.',
            'city.required' => 'required.',
            'year.required' => 'required.',
        ],$errorMessage);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }
        //custom setting to support file upload
        $data = $request->except(['_token','_method','submit']);

        DB::table('proc_tech_education_info')->where('id',$id)->update($data);

        return redirect()->back()->with('alert-success','Data berhasil disimpan.');
        //return redirect()->route('tech-input-data-pendidikan.index')->with('alert-success','Data berhasil disimpan.');
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

        $firstCheck = DB::table('proc_tech_education_info')->where('id',$id)->where('tech_id',$userId)->first();
        
        if (!isset($firstCheck)) {
            return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
        }

        DB::table('proc_tech_education_info')->delete($id);

        return redirect()->back()->with('alert-success','Data berhasil dihapus.');
    }
}
