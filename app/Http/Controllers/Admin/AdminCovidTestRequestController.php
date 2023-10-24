<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class AdminCovidTestRequestController extends Controller
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
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        if ($userDepartment == 4) {
            
            $data['covidDatas'] = DB::table('covid_test_request as ctr')
            ->select([
                'ctr.*',
                DB::raw('(SELECT image FROM covid_image WHERE covid_image.ctr_id = ctr.id AND covid_image.ctr_id IS NOT NULL) as image'),
                DB::raw('(SELECT type FROM covid_image WHERE covid_image.ctr_id = ctr.id) as image_type'),
                DB::raw('(SELECT name FROM covid_test_request_status WHERE covid_test_request_status.id = ctr.status) as status_name'),
                //requester
                DB::raw('(SELECT name FROM admins WHERE admins.id = ctr.requester_id AND ctr.requester_id IS NOT NULL) as requester_name_by_id'),
            ])
            ->orderBy('id','DESC')
            ->paginate(10);

            return view('admin.covid-test.index', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Daftar Tes Covid.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Daftar Tes Covid.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Daftar Tes Covid.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $userId = Auth::user()->id;
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        if ($userDepartment == 4) {

            $data['departments'] = DB::table('department')->get();

            $dataCount = DB::table('covid_test_request as ctr')
            ->where('id',$id)
            ->count();

            if($dataCount < 1){
                return redirect()->back()->with('alert-danger','Data Covid Test Request tidak tersedia.');
            }

            $data['covidData'] = DB::table('covid_test_request as ctr')
            ->select([
                'ctr.*',
                DB::raw('(SELECT name FROM covid_image WHERE covid_image.id = ctr.image_id) as image'),
                DB::raw('(SELECT type FROM covid_image WHERE covid_image.id = ctr.image_id) as image_type'),
                DB::raw('(SELECT name FROM covid_test_request_status WHERE covid_test_request_status.id = ctr.status) as status_name'),
                //requester
                DB::raw('(SELECT name FROM admins WHERE admins.id = ctr.requester_id AND ctr.requester_id IS NOT NULL) as requester_name_by_id'),
                DB::raw('(SELECT name FROM department WHERE department.id = ctr.department_id AND ctr.department_id IS NOT NULL) as department_name'),
            ])
            ->where('id',$id)
            ->first();

            $data['dataOfficer'] = DB::table('users')->where('id',$userId)->first();

            return view('admin.covid-test.show', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Daftar Tes Covid.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Daftar Tes Covid.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Daftar Tes Covid.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Daftar Tes Covid.');
    }
}
