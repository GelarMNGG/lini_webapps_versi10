<?php

namespace App\Http\Controllers\Admin\IT;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class AppsDevelopmentLogsController extends Controller
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
            return view('admin.apps-dev-logs.logs-table', $data);
        }else{
            $data['skin'] = 1;
            $data['skinBack'] = 0;
            return view('admin.apps-dev-logs.logs', $data);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $userDepartment = Auth::user()->department_id;
        $requestDepartment = $request->did;

        if (isset($requestDepartment)) {
            $data['requestDepartment'] = $requestDepartment;
        }else{
            $data['requestDepartment'] = null;
        }

        if ($userDepartment == 5) {
            $data['departmensDatas'] = DB::table('department')->get();
            $data['appsStatusDatas'] = DB::table('apps_development_status')->get();
            $data['programmersDatas'] = DB::table('users')->where('department_id',$userDepartment)->get();
    
            return view('admin.apps-dev-logs.create', $data);
        }

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
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        $requestDepartment = $request->did;

        if ($userDepartment == 5) {
            $request->validate([
                'name' => 'required',
                'event_end' => 'after:event_start',
            ]);
    
            //custom setting to support file upload
            $data = $request->except(['_token','did','submit']);

            $data['event_start'] = date('H:i:s', strtotime($request->event_start));
            $data['event_end'] = date('H:i:s', strtotime($request->event_end));
    
            DB::table('apps_development_logs')->insert($data);

            //sent notifications
                $notificationData = DB::table('apps_development_logs')->orderBy('id','DESC')->first();
                $logName = $request->name;
                $teamId = $notificationData->programmer_id;

                $receiverDepartment = $request->department_id;
                $dataReceiverDepartment = DB::table('admins')->where('department_id',$receiverDepartment)->first();
                $dataDepartment = DB::table('department')->where('id',$receiverDepartment)->first();
                $dataDepartmentName = $dataDepartment->name;

                $dataNotif['publisher_id'] = $userId;
                $dataNotif['publisher_type'] = $userType;
                $dataNotif['publisher_department'] = $userDepartment;

                $publisherName = Auth::user()->name;
                $publisherFirstname = Auth::user()->firstname;
                $publisherLastname = Auth::user()->lastname;
                if ($publisherFirstname !== null) {
                    $publisherName = ucfirst($publisherFirstname).' '.ucfirst($publisherLastname);
                }else{
                    $publisherName = ucfirst($publisherName);
                }
                ###SEND NOTIFICATION MESSAGE TO RELATED DEPARTMENT HEAD
                    ###receiver id & type
                    $dataNotif['receiver_id'] = $dataReceiverDepartment->id;
                    $dataNotif['receiver_type'] = $dataReceiverDepartment->user_type;
                    $dataNotif['receiver_department'] = $receiverDepartment;
                    $dataNotif['level'] = 1;
                    ###notif message
                    $dataNotif['desc'] = "Menambah log <a href='".route('apps-dev-logs.index','did='.$receiverDepartment)."'><strong>".ucfirst($logName)."</strong></a> pada pembuatan aplikasi Department Anda.";
                    ###insert data to notifications table
                    $notifData = DB::table('notifications')->insert($dataNotif);
                ###SEND NOTIFICATION MESSAGE TO RELATED DEPARTMENT HEAD END

                ###SEND NOTIFICATION MESSAGE TO RELATED PROGRAMMER
                if ($request->department_id == 5) {
                    //send message to all team
                    $dataReceiverDepartments = DB::table('users')->where('department_id',$userDepartment)->get();
                    foreach ($dataReceiverDepartments as $teamData) {
                        $dataNotif['receiver_id'] = $teamData->id;
                        $dataNotif['receiver_type'] = $teamData->user_type;
                        $dataNotif['receiver_department'] = $teamData->department_id;
                        $dataNotif['level'] = 1;
                        
                        ###notif message
                        $dataNotif['desc'] = "Menambah log <a href='".route('user-apps-dev-logs.index','did='.$userDepartment)."'><strong>".ucfirst($logName)."</strong></a> pada pembuatan aplikasi <strong>".ucwords($dataDepartmentName)."</strong>.";
                        ###insert data to notifications table
                        DB::table('notifications')->insert($dataNotif);
                    }
                }
                //send message to asigned programmer
                if($teamId != 0){
                    $dataNotif['receiver_id'] = $teamId;
                    $dataNotif['receiver_type'] = 'user';
                    $dataNotif['receiver_department'] = $userDepartment;
                    $dataNotif['level'] = 2;
                    
                    ###notif message
                    $dataNotif['desc'] = "Menambah log <a href='".route('user-apps-dev-logs.index','did='.$receiverDepartment)."'><strong>".ucfirst($logName)."</strong></a> dan menugaskan Anda sebagai programmer pada pembuatan aplikasi <strong>".ucwords($dataDepartmentName)."</strong>.";
                    ###insert data to notifications table
                    DB::table('notifications')->insert($dataNotif);
                }
                ###SEND NOTIFICATION MESSAGE TO RELATED PROGRAMMER END
            //sent notifications end

            if (isset($requestDepartment)) {
                return redirect()->route('apps-dev-logs.index','did='.$requestDepartment)->with('alert-success','Data berhasil disimpan.');
            }else{
                return redirect()->route('apps-dev-logs.index')->with('alert-success','Data berhasil disimpan.');
            }

        }

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
        $userDepartment = Auth::user()->department_id;
        $itDepartment = 5;

        if ($userDepartment == 5) {
            //first check
            $firstCheck = DB::table('apps_development_logs')->where('id',$id)->count();

            if ($firstCheck < 1) {
                return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
            }

            //logs datas
            $data['appsDevLogsData'] = DB::table('apps_development_logs')->where('id',$id)->first();

            //supporting data
            $data['departmensDatas'] = DB::table('department')->get();
            $data['appsStatusDatas'] = DB::table('apps_development_status')->get();
            $data['programmersDatas'] = DB::table('users')->where('department_id',$itDepartment)->get();
    
            return view('admin.apps-dev-logs.edit', $data);
        }

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
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        if ($userDepartment == 5) {
            //first check
            $firstCheck = DB::table('apps_development_logs')->where('id',$id)->count();
            $statusCheck = DB::table('apps_development_logs')->where('id',$id)->first();
            $oldStatus = $statusCheck->status;
            $newStatus = $request->status;
            $logDepartment = $request->department_id;

            if ($firstCheck < 1) {
                return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
            }

            $request->validate([
                'name' => 'required',
                'event_end' => 'after:event_start',
            ]);
    
            //custom setting
            $data = $request->except(['_token','_method','submit']);

            $data['event_start'] = date('H:i:s', strtotime($request->event_start));
            $data['event_end'] = date('H:i:s', strtotime($request->event_end));
    
            //sent notifications
                $logName = $request->name;
                $logStatus = DB::table('apps_development_status')->where('id',$request->status)->first();
                $receiverDepartment = $request->department_id;
                $dataReceiverDepartment = DB::table('admins')->where('department_id',$receiverDepartment)->first();
                $dataDepartment = DB::table('department')->where('id',$receiverDepartment)->first();
                $dataDepartmentName = $dataDepartment->name;

                if ($oldStatus != $newStatus) {
                    ###SEND NOTIFICATION MESSAGE TO RELATED DEPARTMENT HEAD
                        $dataNotif['publisher_id'] = $userId;
                        $dataNotif['publisher_type'] = $userType;
                        $dataNotif['publisher_department'] = $userDepartment;
        
                        $publisherName = Auth::user()->name;
                        $publisherFirstname = Auth::user()->firstname;
                        $publisherLastname = Auth::user()->lastname;
                        if ($publisherFirstname !== null) {
                            $publisherName = ucfirst($publisherFirstname).' '.ucfirst($publisherLastname);
                        }else{
                            $publisherName = ucfirst($publisherName);
                        }
                        ###receiver id & type
                        $dataNotif['receiver_id'] = $dataReceiverDepartment->id;
                        $dataNotif['receiver_type'] = $dataReceiverDepartment->user_type;
                        $dataNotif['receiver_department'] = $dataReceiverDepartment->department_id;
                        $dataNotif['level'] = 1;
                        ###notif message
                        $dataNotif['desc'] = "Mengubah status log <a href='".route('apps-dev-logs.index','did='.$receiverDepartment)."'><strong>".ucfirst($logName)."</strong></a> menjadi <strong>".ucfirst($logStatus->name)."</strong> pada pembuatan aplikasi <strong>".ucwords($dataDepartmentName)."</strong>.";
                        ###insert data to notifications table
                        DB::table('notifications')->insert($dataNotif);
                    ###SEND NOTIFICATION MESSAGE TO RELATED DEPARTMENT HEAD END

                    ###SEND NOTIFICATION MESSAGE TO ALL PROGRAMMER
                    if ($request->department_id == 5) {
                        //send message to all team
                        $dataReceiverDepartments = DB::table('users')->where('department_id',$userDepartment)->get();
                        foreach ($dataReceiverDepartments as $teamData) {
                            $dataNotif['receiver_id'] = $teamData->id;
                            $dataNotif['receiver_type'] = $teamData->user_type;
                            $dataNotif['receiver_department'] = $teamData->department_id;
                            $dataNotif['level'] = 1;
                            
                            ###notif message
                            $dataNotif['desc'] = "Mengubah status log <a href='".route('user-apps-dev-logs.index','did='.$receiverDepartment)."'><strong>".ucfirst($logName)."</strong></a> menjadi <strong>".ucfirst($logStatus->name)."</strong> pada pembuatan aplikasi <strong>".ucwords($dataDepartmentName)."</strong>.";
                            ###insert data to notifications table
                            DB::table('notifications')->insert($dataNotif);
                        }
                    }
                    ###SEND NOTIFICATION MESSAGE TO ALL PROGRAMMER END
                }

                ###SEND NOTIFICATION MESSAGE TO RELATED PROGRAMMER
                $oldData = DB::table('apps_development_logs')->where('id',$id)->first();
                $oldProgrammerId = $oldData->programmer_id;
                $newProgrammerId = $request->programmer_id;

                if($oldProgrammerId != $newProgrammerId){
                    $dataNotif['receiver_id'] = $newProgrammerId;
                    $dataNotif['receiver_type'] = 'user';
                    $dataNotif['receiver_department'] = $userDepartment;
                    $dataNotif['level'] = 2;
                    
                    ###notif message
                    $dataNotif['desc'] = "Menambah log <a href='".route('user-apps-dev-logs.index','did='.$receiverDepartment)."'><strong>".ucfirst($logName)."</strong></a> dan menugaskan Anda sebagai programmer pada pembuatan aplikasi <strong>".ucwords($dataDepartmentName)."</strong>.";
                    ###insert data to notifications table
                    DB::table('notifications')->insert($dataNotif);
                }
            //sent notifications end
            
            //update logs data
            DB::table('apps_development_logs')->where('id',$id)->update($data);
    
            return redirect()->route('apps-dev-logs.index','did='.$logDepartment)->with('alert-success','Data berhasil diupdate.');
        }

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
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        if ($userDepartment == 5) {
            DB::table('apps_development_logs')->delete($id);
            return redirect()->back()->with('alert-success','Data berhasil dihapus.');
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Apps Development.');
    }
}
