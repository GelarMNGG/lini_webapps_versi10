<?php

namespace App\Http\Controllers\Tech;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Auth;
use DB;

class TechInputPersonalData extends Controller
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

        //personal data input
        $data['techPersonalData'] = DB::table('proc_tech_personal_data')->where('tech_id',$userId)->first();
        //supporting data
        $data['tech'] = DB::table('techs')->where('id',$userId)->first();
        $data['procGenders'] = DB::table('proc_gender')->get();
        $data['procReligions'] = DB::table('religions')->get();
        $data['procMaritalStatus'] = DB::table('proc_marital_status')->get();
        $data['cityDatas'] = DB::table('list_of_cities')->get();
        $data['provinceDatas'] = DB::table('list_of_provinces')->get();


        //famimily data input
        $data['techFamilyInfo'] = DB::table('proc_family_info')->where('tech_id',$userId)->first();

        //education data input
        $data['educationDatas'] = DB::table('proc_tech_education_info as ptei')
        ->select([
            'ptei.*',
            DB::raw('(SELECT name FROM proc_tech_education_info_level WHERE proc_tech_education_info_level.id = ptei.level) as level_name'),
            DB::raw('(SELECT name FROM list_of_provinces WHERE list_of_provinces.id = ptei.province) as province_name'),
            DB::raw('(SELECT name FROM list_of_cities WHERE list_of_cities.id = ptei.city) as city_name'),
        ])
        ->where('tech_id',$userId)->paginate(10);

        $data['educationLevels'] = DB::table('proc_tech_education_info_level')->get();

        //upload document
        $data['documentDatas'] = DB::table('proc_tech_documents as ptd')
        ->select([
            'ptd.*',
            DB::raw('(SELECT name FROM proc_tech_documents_type WHERE proc_tech_documents_type.id = ptd.doc_type) as doc_name')
        ])
        ->where('tech_id',$userId)->paginate(10);

        //supporting data
        $data['documentsTypesCount'] = DB::table('proc_tech_documents_type')->count();
        $data['docTypes'] = DB::table('proc_tech_documents_type')->get();

        return view('tech.data-diri.index', $data);
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
        $data['procGenders'] = DB::table('proc_gender')->get();
        $data['procReligions'] = DB::table('religions')->get();
        $data['procMaritalStatus'] = DB::table('proc_marital_status')->get();

        return view('tech.data-diri.create',$data);
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
            'date_of_birth.required' => 'Field ini tidak boleh kosong.',
            'gender.required' => 'Field ini tidak boleh kosong.',
            'marital_status.required' => 'Field ini tidak boleh kosong.',
            'ktp.required' => 'Field ini tidak boleh kosong.',
            //'npwp.required' => 'Field ini tidak boleh kosong.',
            'bpjs_health.required' => 'Field ini tidak boleh kosong.',
            // 'bpjs_ketenagakerjaan.required' => 'Field ini tidak boleh kosong.',
            'recitation_place.required' => 'Field ini tidak boleh kosong dan maksimum 255 karakter.',
            'ustad.required' => 'Field ini tidak boleh kosong dan maksimum 255 karakter.',
            'book_title.required' => 'Field ini tidak boleh kosong.',
            'last_book_read.required' => 'Field ini tidak boleh kosong.',
            'book_summary.required' => 'Field ini tidak boleh kosong.',
            'ktp_address.min:20' => 'Minimum 20 karakter.',
            'current_address.min:20' => 'Minimum 20 karakter.',
        ];
        $validation = Validator::make($request->all(),[
            'date_of_birth' => 'required',
            'gender' => 'required',
            'marital_status' => 'required',
            'ktp' => 'required',
            //'npwp' => 'required',
            'bpjs_health' => 'required',
            // 'bpjs_ketenagakerjaan' => 'required',
            'recitation_place' => 'required|max:255',
            'ustad' => 'required|max:255',
            'book_title' => 'required|max:255',
            'last_book_read' => 'required',
            'book_summary' => 'required',
            'ktp_address' => 'required|min:20',
            'current_address' => 'required|min:20',
        ],$errorMessage);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }
        //custom setting
        $data = $request->except(['_token','_method','submit','norek']);
        $data['tech_id'] = $userId;

        DB::table('proc_tech_personal_data')->insert($data);

        return redirect()->route('tech-input-data-diri.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Input Data Diri.');
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

        $firstCheck = DB::table('proc_tech_personal_data')->where('id',$id)->where('tech_id',$userId)->first();
        
        if (!isset($firstCheck)) {
            return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
        }

        $data['techPersonalData'] = $firstCheck;

        //supporting data
        $data['tech'] = DB::table('techs')->where('id',$userId)->first();
        $data['procGenders'] = DB::table('proc_gender')->get();
        $data['procReligions'] = DB::table('religions')->get();
        $data['procMaritalStatus'] = DB::table('proc_marital_status')->get();

        return view('tech.data-diri.edit',$data);
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
            'date_of_birth.required' => 'Field ini tidak boleh kosong.',
            'gender.required' => 'Field ini tidak boleh kosong.',
            'marital_status.required' => 'Field ini tidak boleh kosong.',
            'ktp.required' => 'Field ini tidak boleh kosong.',
            //'npwp.required' => 'Field ini tidak boleh kosong.',
            'bpjs_health.required' => 'Field ini tidak boleh kosong.',
            // 'bpjs_ketenagakerjaan.required' => 'Field ini tidak boleh kosong.',
            'recitation_place.required' => 'Field ini tidak boleh kosong dan maksimum 255 karakter.',
            'ustad.required' => 'Field ini tidak boleh kosong dan maksimum 255 karakter.',
            'book_title.required' => 'Field ini tidak boleh kosong.',
            'last_book_read.required' => 'Field ini tidak boleh kosong.',
            'book_summary.required' => 'Field ini tidak boleh kosong.',
            'ktp_address.min:20' => 'Minimum 20 karakter.',
            'current_address.min:20' => 'Minimum 20 karakter.',
        ];
        $validation = Validator::make($request->all(),[
            'date_of_birth' => 'required',
            'gender' => 'required',
            'marital_status' => 'required',
            'ktp' => 'required',
            //'npwp' => 'required',
            'bpjs_health' => 'required',
            // 'bpjs_ketenagakerjaan' => 'required',
            'recitation_place' => 'required|max:255',
            'ustad' => 'required|max:255',
            'book_title' => 'required|max:255',
            'last_book_read' => 'required',
            'book_summary' => 'required',
            'ktp_address' => 'required|min:20',
            'current_address' => 'required|min:20',
        ],$errorMessage);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }
        //custom setting to support file upload
        $data = $request->except(['_token','_method','submit','norek']);

        DB::table('proc_tech_personal_data')->where('id',$id)->update($data);

        return redirect()->route('tech-input-data-diri.index')->with('alert-success','Data berhasil disimpan.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Input Data Diri.');
    }
}
