<?php

namespace App\Http\Controllers\Tech;

use App\Tech;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Auth;
use DB;

class TechFamilyController extends Controller
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

        $firstCheck = DB::table('proc_family_info')->where('tech_id',$userId)->first();

        if (!isset($firstCheck)) {
            return redirect()->route('tech-input-data-keluarga.create');
        }else{
            return redirect()->route('tech-input-data-keluarga.edit',$firstCheck->id);
        }

        return view('tech.data-diri.family-info.index',$data);
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
        $data['tech'] = DB::table('techs')->where('id',$userId)->first();

        return view('tech.data-diri.family-info.create', $data);
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
            'father.required' => 'Field ini tidak boleh kosong.',
            'mother.required' => 'Field ini tidak boleh kosong.',
            //'spouse.required' => 'Field ini tidak boleh kosong.',
            'father_profession.required' => 'Field ini tidak boleh kosong.',
            'mother_profession.required' => 'Field ini tidak boleh kosong.',
            //'spouse_profession.required' => 'Field ini tidak boleh kosong.',
        ];
        $validation = Validator::make($request->all(),[
            'father.required' => 'required.',
            'mother.required' => 'required.',
            //'spouse.required' => 'required.',
            'father_profession.required' => 'required.',
            'mother_profession.required' => 'required.',
            //'spouse_profession.required' => 'required.',
        ],$errorMessage);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        //custom setting to support file upload
        $data = $request->except(['_token','_method','submit']);
        $data['tech_id'] = $userId;
        #dd($data);
        DB::table('proc_family_info')->insert($data);

        //return redirect()->route('tech-input-data-diri.index')->with('alert-success','Data berhasil disimpan');
        return redirect()->back()->with('alert-success','Data berhasil disimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Form Input Data. Family');
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

        $firstCheck = DB::table('proc_family_info')->where('id',$id)->where('tech_id',$userId)->first();
        
        if (!isset($firstCheck)) {
            return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
        }

        $data['techFamilyInfo'] = $firstCheck;

        //supporting data
        $data['tech'] = DB::table('techs')->where('id',$userId)->first();
        // $data['procGenders'] = DB::table('proc_gender')->get();
        // $data['procMaritalStatus'] = DB::table('proc_marital_status')->get();

        return view('tech.data-diri.family-info.edit',$data);

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Form Input Data. Family');
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
            'father.required' => 'Field ini tidak boleh kosong.',
            'mother.required' => 'Field ini tidak boleh kosong.',
            //'spouse.required' => 'Field ini tidak boleh kosong.',
            'father_profession.required' => 'Field ini tidak boleh kosong.',
            'mother_profession.required' => 'Field ini tidak boleh kosong.',
            //'spouse_profession.required' => 'Field ini tidak boleh kosong.',
        ];
        $validation = Validator::make($request->all(),[
            'father.required' => 'required.',
            'mother.required' => 'required.',
            //'spouse.required' => 'required.',
            'father_profession.required' => 'required.',
            'mother_profession.required' => 'required.',
            //'spouse_profession.required' => 'required.',
        ],$errorMessage);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        //custom setting to support file upload
        $data = $request->except(['_token','_method','submit','norek']);

        DB::table('proc_family_info')->where('id',$id)->update($data);

        //return redirect()->route('tech-input-data-keluarga.edit', $id)->with('alert-success','Data berhasil disimpan.');
        return redirect()->route('tech-input-data-diri.index', $id)->with('alert-success','Data berhasil disimpan.');
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

        //delete from database
        DB::table('proc_family_info')->delete($id);
        
        return redirect()->route('form-input-data-keluarga.index')->with('alert-success','Data berhasil dihapus.');  
    }
}
