<?php

namespace App\Http\Controllers\User\IT;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class AppsDevelopmentLogsController extends Controller
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
    public function index(Request $request)
    {
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

        if($userDepartment == 5){
            //logs data
            if(isset($requestDepartment) || isset($requestStatus) || isset($requestProgrammer)) {
                //data logs
                if(isset($requestDepartment)) {
                    if(isset($requestStatus) && isset($requestProgrammer)) {
                        $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('department_id',$requestDepartment)->where('status',$requestStatus)->where('programmer_id',$requestProgrammer)->orderBy('status','ASC')->orderBy('date','DESC')->orderBy(DB::raw('HOUR(event_start)'),'DESC')->get();
                    }elseif(isset($requestStatus)){
                        $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('department_id',$requestDepartment)->where('status',$requestStatus)->orderBy('status','ASC')->orderBy('date','DESC')->orderBy(DB::raw('HOUR(event_start)'),'DESC')->get();
                    }elseif(isset($requestProgrammer)){
                        $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('department_id',$requestDepartment)->where('programmer_id',$requestProgrammer)->orderBy('status','ASC')->orderBy('date','DESC')->orderBy(DB::raw('HOUR(event_start)'),'DESC')->get();
                    }else{
                        $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('department_id',$requestDepartment)->orderBy('status','ASC')->orderBy('date','DESC')->orderBy(DB::raw('HOUR(event_start)'),'DESC')->get();
                    }
                }elseif(isset($requestStatus)) {
                    if (isset($requestDepartment) && isset($requestProgrammer)) {                        
                        $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('department_id',$requestDepartment)->where('status',$requestStatus)->where('programmer_id',$requestProgrammer)->orderBy('status','ASC')->orderBy('date','DESC')->orderBy(DB::raw('HOUR(event_start)'),'DESC')->get();
                    }elseif(isset($requestDepartment)){
                        $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('department_id',$requestDepartment)->where('status',$requestStatus)->orderBy('status','ASC')->orderBy('date','DESC')->orderBy(DB::raw('HOUR(event_start)'),'DESC')->get();
                    }elseif(isset($requestProgrammer)){
                        $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('status',$requestStatus)->where('programmer_id',$requestProgrammer)->orderBy('status','ASC')->orderBy('date','DESC')->orderBy(DB::raw('HOUR(event_start)'),'DESC')->get();
                    }else{
                        $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('status',$requestStatus)->orderBy('status','ASC')->orderBy('date','DESC')->orderBy(DB::raw('HOUR(event_start)'),'DESC')->get();
                    }
                }elseif(isset($requestProgrammer)){
                    if(isset($requestDepartment) && isset($requestStatus)){
                        $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('department_id',$requestDepartment)->where('status',$requestStatus)->where('programmer_id',$requestProgrammer)->orderBy('status','ASC')->orderBy('date','DESC')->orderBy(DB::raw('HOUR(event_start)'),'DESC')->get();
                    }elseif(isset($requestDepartment)){
                        $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('department_id',$requestDepartment)->where('programmer_id',$requestProgrammer)->orderBy('status','ASC')->orderBy('date','DESC')->orderBy(DB::raw('HOUR(event_start)'),'DESC')->get();
                    }elseif(isset($requestStatus)){
                        $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('status',$requestStatus)->where('programmer_id',$requestProgrammer)->orderBy('status','ASC')->orderBy('status','ASC')->orderBy('date','DESC')->orderBy(DB::raw('HOUR(event_start)'),'DESC')->get();
                    }else{
                        $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('programmer_id',$requestProgrammer)->orderBy('status','ASC')->orderBy('status','ASC')->orderBy('date','DESC')->orderBy(DB::raw('HOUR(event_start)'),'DESC')->get();
                    }
                }else{
                    $data['appsDevLogsDatas'] = DB::table('apps_development_logs')
                    ->orderBy('status','ASC')->orderBy('date','DESC')->orderBy(DB::raw('HOUR(event_start)'),'DESC')->get();
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
                    $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('status',$requestStatus)->where('programmer_id',$requestProgrammer)->orderBy('status','ASC')->orderBy('status','ASC')->orderBy('date','DESC')->orderBy(DB::raw('HOUR(event_start)'),'DESC')->get();
                }elseif (isset($requestStatus)) {
                    $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('status',$requestStatus)->orderBy('status','ASC')->orderBy('date','DESC')->orderBy(DB::raw('HOUR(event_start)'),'DESC')->get();
                }elseif(isset($requestProgrammer)){
                    $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('programmer_id',$requestProgrammer)->orderBy('status','ASC')->orderBy('date','DESC')->orderBy(DB::raw('HOUR(event_start)'),'DESC')->get();
                }else{
                    $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->orderBy('status','ASC')->orderBy('date','DESC')->get();
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
            $data['appsDevLogsDatas'] = DB::table('apps_development_logs')->where('department_id',$userDepartment)
            ->orderBy('status','ASC')->orderBy('date','DESC')->orderBy(DB::raw('HOUR(event_start)'),'DESC')->get();

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

        //skin implementation
        if ($skin == 1) {
            $data['skin'] = 0;
            $data['skinBack'] = 1;
            return view('user.apps-dev-logs.logs-table', $data);
        }else{
            $data['skin'] = 1;
            $data['skinBack'] = 0;
            return view('user.apps-dev-logs.logs', $data);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Apps Development.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Apps Development.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Apps Development.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Apps Development.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Apps Development.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Apps Development.');
    }
}
