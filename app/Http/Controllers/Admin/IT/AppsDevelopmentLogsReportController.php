<?php

namespace App\Http\Controllers\Admin\IT;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class AppsDevelopmentLogsReportController extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman report Apps Development Logs.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $userId = Auth::user()->id;
        $userDepartment = Auth::user()->department_id;

        $requestDepartment = $request->did;
        $requestStatus = $request->sid;
        $requestProgrammer = $request->pid;
        $skin = $request->skin;

        $itDepartment = 5;

        $firstCheck = DB::table('apps_development_logs')->where('department_id',$userDepartment)->count();

        if (isset($requestProgrammer)) {
            $secondCheck = DB::table('apps_development_logs')->where('programmer_id',$requestProgrammer)->count();

            if ($secondCheck < 1) {
                return redirect()->back()->with('alert-danger','Maaf, belum ada aplikasi yang diprogram oleh programmer yang Anda pilih.');
            }
        }

        //supporting data
        $data['requestDepartment'] = $request->did;
        $data['requestStatus'] = $request->sid;
        $data['requestProgrammer'] = $request->pid;
        $data['departmensDatas'] = DB::table('department')->get();
        $data['programmersDatas'] = DB::table('users')->where('department_id',$itDepartment)->get();
        $data['adminProfile'] = DB::table('admins')
        ->select([
            'admins.*',
            DB::raw('(SELECT name FROM department WHERE department.id = admins.department_id) as department_name')
        ])
        ->where('id',$userId)->where('department_id',$userDepartment)->first();

        if($userDepartment == 5){
            //logs data
            if(isset($requestDepartment) || isset($requestStatus) || isset($requestProgrammer)) {
                //data logs
                if(isset($requestDepartment)) {
                    if(isset($requestStatus) && isset($requestProgrammer)) {
                        $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('department_id',$requestDepartment)->where('status',$requestStatus)->where('programmer_id',$requestProgrammer)->orderBy('date','DESC')->paginate(21);
                    }elseif(isset($requestStatus)){
                        $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('department_id',$requestDepartment)->where('status',$requestStatus)->orderBy('date','DESC')->paginate(21);
                    }elseif(isset($requestProgrammer)){
                        $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('department_id',$requestDepartment)->where('programmer_id',$requestProgrammer)->orderBy('date','DESC')->paginate(21);
                    }else{
                        $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('department_id',$requestDepartment)->orderBy('date','DESC')->paginate(21);
                    }
                }elseif(isset($requestStatus)) {
                    if (isset($requestDepartment) && isset($requestProgrammer)) {                        
                        $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('department_id',$requestDepartment)->where('status',$requestStatus)->where('programmer_id',$requestProgrammer)->orderBy('date','DESC')->paginate(21);
                    }elseif(isset($requestDepartment)){
                        $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('department_id',$requestDepartment)->where('status',$requestStatus)->orderBy('date','DESC')->paginate(21);
                    }elseif(isset($requestProgrammer)){
                        $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('status',$requestStatus)->where('programmer_id',$requestProgrammer)->orderBy('date','DESC')->paginate(21);
                    }else{
                        $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('status',$requestStatus)->orderBy('date','DESC')->paginate(21);
                    }
                }elseif(isset($requestProgrammer)){
                    if(isset($requestDepartment) && isset($requestStatus)){
                        $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('department_id',$requestDepartment)->where('status',$requestStatus)->where('programmer_id',$requestProgrammer)->orderBy('date','DESC')->paginate(21);
                    }elseif(isset($requestDepartment)){
                        $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('department_id',$requestDepartment)->where('programmer_id',$requestProgrammer)->orderBy('date','DESC')->paginate(21);
                    }elseif(isset($requestStatus)){
                        $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('status',$requestStatus)->where('programmer_id',$requestProgrammer)->orderBy('date','DESC')->paginate(21);
                    }else{
                        $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('programmer_id',$requestProgrammer)->orderBy('date','DESC')->paginate(21);
                    }
                }else{
                    $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->orderBy('date','DESC')->orderBy('event_start','DESC')->paginate(21);
                }
                //log status
                if(isset($requestDepartment) || isset($requestStatus) || isset($requestProgrammer)) {
                    if (isset($requestDepartment)) {
                        if(isset($requestProgrammer)){
                            $data['appsStatusDatas'] = DB::table('apps_development_status as ads')
                            ->select([
                                'ads.*',
                                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 0 AND apps_development_logs.department_id = '.$requestDepartment.' AND apps_development_logs.programmer_id = '.$requestProgrammer.' AND apps_development_logs.status IS NOT NULL) as noStatusCount'),
                                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 1 AND apps_development_logs.department_id = '.$requestDepartment.' AND apps_development_logs.programmer_id = '.$requestProgrammer.' AND apps_development_logs.status IS NOT NULL) as onProgressCount'),
                                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 2 AND apps_development_logs.department_id = '.$requestDepartment.' AND apps_development_logs.programmer_id = '.$requestProgrammer.' AND apps_development_logs.status IS NOT NULL) as postponeCount'),
                                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 3 AND apps_development_logs.department_id = '.$requestDepartment.' AND apps_development_logs.programmer_id = '.$requestProgrammer.' AND apps_development_logs.status IS NOT NULL) as cancelledCount'),
                                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 4 AND apps_development_logs.department_id = '.$requestDepartment.' AND apps_development_logs.programmer_id = '.$requestProgrammer.' AND apps_development_logs.status IS NOT NULL) as doneCount'),
                            ])
                            ->get();
                        }else{
                            $data['appsStatusDatas'] = DB::table('apps_development_status as ads')
                            ->select([
                                'ads.*',
                                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 0 AND apps_development_logs.department_id = '.$requestDepartment.' AND apps_development_logs.status IS NOT NULL) as noStatusCount'),
                                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 1 AND apps_development_logs.department_id = '.$requestDepartment.' AND apps_development_logs.status IS NOT NULL) as onProgressCount'),
                                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 2 AND apps_development_logs.department_id = '.$requestDepartment.' AND apps_development_logs.status IS NOT NULL) as postponeCount'),
                                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 3 AND apps_development_logs.department_id = '.$requestDepartment.' AND apps_development_logs.status IS NOT NULL) as cancelledCount'),
                                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 4 AND apps_development_logs.department_id = '.$requestDepartment.' AND apps_development_logs.status IS NOT NULL) as doneCount'),
                            ])
                            ->get();
                        }
                    //if requestprogrammer existed
                    }elseif(isset($requestProgrammer)){
                        if (isset($requestDepartment)) {
                            $data['appsStatusDatas'] = DB::table('apps_development_status as ads')
                            ->select([
                                'ads.*',
                                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 0 AND apps_development_logs.department_id = '.$requestDepartment.' AND apps_development_logs.programmer_id = '.$requestProgrammer.' AND apps_development_logs.status IS NOT NULL) as noStatusCount'),
                                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 1 AND apps_development_logs.department_id = '.$requestDepartment.' AND apps_development_logs.programmer_id = '.$requestProgrammer.' AND apps_development_logs.status IS NOT NULL) as onProgressCount'),
                                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 2 AND apps_development_logs.department_id = '.$requestDepartment.' AND apps_development_logs.programmer_id = '.$requestProgrammer.' AND apps_development_logs.status IS NOT NULL) as postponeCount'),
                                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 3 AND apps_development_logs.department_id = '.$requestDepartment.' AND apps_development_logs.programmer_id = '.$requestProgrammer.' AND apps_development_logs.status IS NOT NULL) as cancelledCount'),
                                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 4 AND apps_development_logs.department_id = '.$requestDepartment.' AND apps_development_logs.programmer_id = '.$requestProgrammer.' AND apps_development_logs.status IS NOT NULL) as doneCount'),
                            ])
                            ->get();
                        }else{
                            $data['appsStatusDatas'] = DB::table('apps_development_status as ads')
                            ->select([
                                'ads.*',
                                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 0 AND apps_development_logs.programmer_id = '.$requestProgrammer.' AND apps_development_logs.status IS NOT NULL) as noStatusCount'),
                                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 1 AND apps_development_logs.programmer_id = '.$requestProgrammer.' AND apps_development_logs.status IS NOT NULL) as onProgressCount'),
                                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 2 AND apps_development_logs.programmer_id = '.$requestProgrammer.' AND apps_development_logs.status IS NOT NULL) as postponeCount'),
                                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 3 AND apps_development_logs.programmer_id = '.$requestProgrammer.' AND apps_development_logs.status IS NOT NULL) as cancelledCount'),
                                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 4 AND apps_development_logs.programmer_id = '.$requestProgrammer.' AND apps_development_logs.status IS NOT NULL) as doneCount'),
                            ])
                            ->get();
                        }
                    }else{
                        $data['appsStatusDatas'] = DB::table('apps_development_status as ads')
                            ->select([
                                'ads.*',
                                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 0 AND apps_development_logs.status IS NOT NULL) as noStatusCount'),
                                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 1 AND apps_development_logs.status IS NOT NULL) as onProgressCount'),
                                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 2 AND apps_development_logs.status IS NOT NULL) as postponeCount'),
                                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 3 AND apps_development_logs.status IS NOT NULL) as cancelledCount'),
                                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 4 AND apps_development_logs.status IS NOT NULL) as doneCount'),
                            ])
                            ->get();
                    }
                }else{
                    $data['appsStatusDatas'] = DB::table('apps_development_status as ads')
                    ->select([
                        'ads.*',
                        DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 0 AND apps_development_logs.status IS NOT NULL) as noStatusCount'),
                        DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 1 AND apps_development_logs.status IS NOT NULL) as onProgressCount'),
                        DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 2 AND apps_development_logs.status IS NOT NULL) as postponeCount'),
                        DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 3 AND apps_development_logs.status IS NOT NULL) as cancelledCount'),
                        DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 4 AND apps_development_logs.status IS NOT NULL) as doneCount'),
                    ])
                    ->get();
                }
            }else{
                //data logs
                if (isset($requestStatus) && isset($requestProgrammer)) {
                    $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('status',$requestStatus)->where('programmer_id',$requestProgrammer)->orderBy('date','DESC')->paginate(21);
                }elseif (isset($requestStatus)) {
                    $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('status',$requestStatus)->orderBy('date','DESC')->paginate(21);
                }elseif(isset($requestProgrammer)){
                    $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('programmer_id',$requestProgrammer)->orderBy('date','DESC')->paginate(21);
                }else{
                    $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->orderBy('date','DESC')->paginate(21);
                }
                //log status
                $data['appsStatusDatas'] = DB::table('apps_development_status as ads')
                ->select([
                    'ads.*',
                    DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 0 AND apps_development_logs.status IS NOT NULL) as noStatusCount'),
                    DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 1 AND apps_development_logs.status IS NOT NULL) as onProgressCount'),
                    DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 2 AND apps_development_logs.status IS NOT NULL) as postponeCount'),
                    DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 3 AND apps_development_logs.status IS NOT NULL) as cancelledCount'),
                    DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 4 AND apps_development_logs.status IS NOT NULL) as doneCount'),
                ])
                ->get();
            }
        }elseif($firstCheck > 0){
            //logs data
            $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('department_id',$userDepartment)->orderBy('date','DESC')->paginate(21);

            $data['appsStatusDatas'] = DB::table('apps_development_status as ads')
            ->select([
                'ads.*',
                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 0 AND apps_development_logs.department_id = '.$userDepartment.' AND apps_development_logs.status IS NOT NULL) as noStatusCount'),
                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 1 AND apps_development_logs.department_id = '.$userDepartment.' AND apps_development_logs.status IS NOT NULL) as onProgressCount'),
                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 2 AND apps_development_logs.department_id = '.$userDepartment.' AND apps_development_logs.status IS NOT NULL) as postponeCount'),
                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 3 AND apps_development_logs.department_id = '.$userDepartment.' AND apps_development_logs.status IS NOT NULL) as cancelledCount'),
                DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.status = 4 AND apps_development_logs.department_id = '.$userDepartment.' AND apps_development_logs.status IS NOT NULL) as doneCount'),
            ])
            ->get();
        }
        return view('admin.apps-dev-logs.report',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman report Apps Development Logs.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman report Apps Development Logs.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman report Apps Development Logs.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman report Apps Development Logs.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman report Apps Development Logs.');
    }

    public function customReport(Request $request)
    {
        dd($request);

        $userId = Auth::user()->id;
        $userDepartment = Auth::user()->department_id;
        

        return view('admin.minutes-report.report-custom',$data);
    }
}
