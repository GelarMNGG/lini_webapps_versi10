<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class WfhToWfoController extends Controller
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
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if ($userCompany == 1) {
            $data['requestDatas'] = DB::table('wfh_to_wfo_request as wtwr')
            ->select([
                'wtwr.*',
                DB::raw('(SELECT name FROM wfh_to_wfo_request_status WHERE wfh_to_wfo_request_status.id = wtwr.status) as status_name'),
            ])
            ->where('leader_id',$userId)
            ->where('department_id',$userDepartment)
            ->orderBy('id','DESC')
            ->get();

            $data['requesterAdmins'] = DB::table('admins')->where('active',1)->get();
            $data['requesterUsers'] = DB::table('users')->where('active',1)->get();

            return view('admin.wfh-to-wfo.index-table', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman WFH to WFO.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userId = Auth::user()->id;
        $userLevel = Auth::user()->user_level;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $data['departments'] = DB::table('department')->get();
        $data['users'] = DB::table('users')->where('department_id',$userDepartment)->where('active',1)->get();

        if ($userCompany == 1) {
            return view('admin.wfh-to-wfo.create', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman WFH to WFO.');
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
        $userLevel = Auth::user()->user_level;
        $userType = Auth::user()->user_type;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $request->validate([
            'date' => 'required',
            'employee_id' => 'required',
            'clock_in' => 'required',
            'clock_out' => 'required|after:clock_in',
            'description' => 'required',
        ]);

        $data = $request->except(['_token','submit']);
        $data['leader_id'] = $userId;
        $data['department_id'] = $userDepartment;

        $employeeData = $request->employee_id;
        if ($employeeData == 'lid') {
            $data['employee_id'] = $userId;
            $data['employee_type'] = 'admin';
        }else{
            $data['employee_id'] = $employeeData;
            $data['employee_type'] = 'user';
        }

        $employeeId = $data['employee_id'];
        $employeeType = $data['employee_type'];
        $employeeDateCheck = date('Y-m-d',strtotime($request->date));
        $firstCheck = DB::table('wfh_to_wfo_request')->where('employee_id',$employeeId)->where('employee_type',$employeeType)->where('date',$employeeDateCheck)->count();

        if ($firstCheck > 0) {
            return redirect()->back()->with('alert-danger','Maaf, gagal input! Terindikasi duplikat data.');
        }

        $data['date'] = date('Y-m-d H:i:s',strtotime($request->date));
        $data['clock_in'] = date('H:i:s',strtotime($request->clock_in));
        $data['clock_out'] = date('H:i:s',strtotime($request->clock_out));
        
        $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('wfh_to_wfo_request')->insert($data);

        //send notifications
            $dataNotif['publisher_id'] = $userId;
            $dataNotif['publisher_type'] = $userType;
            $dataNotif['publisher_department'] = $userDepartment;
            $publisherName = Auth::user()->name;
            $publisherFirstname = Auth::user()->firstname;
            $publisherLastname = Auth::user()->lastname;
            if ($publisherFirstname !== null) {
                $publisherName = ucfirst($publisherFirstname).' '.ucfirst($publisherLastname);
            }
            ###receiver id & type
            $gaDepartment = 4; //ga code
            $satgasCovid = 7; //satgas covid
            $employeeDate = $request->date;
            $employeeClockIn = $request->clock_in;
            $employeeClockOut = $request->clock_out;
            
            $satgasDatas = DB::table('users')->where('department_id',$gaDepartment)->where('user_level',$satgasCovid)->get();

            $receiverDatas = DB::table('wfh_to_wfo_request')->select('department_id','employee_id','employee_type','date','clock_in','clock_out')->orderBy('id','DESC')->first();

            if ($receiverDatas->employee_type == 'user') {
                $employeeData = DB::table('users')->select('id','firstname','lastname','user_type','department_id')->where('id',$receiverDatas->employee_id)->first();

                $employeeFirstname = $employeeData->firstname;
                $employeeLastname = $employeeData->lastname;
                $employeeDate = $receiverDatas->date;
                $employeeClockIn = $receiverDatas->clock_in;
                $employeeClockOut = $receiverDatas->clock_out;
    
                //notif data
                $dataNotif['receiver_id'] = $employeeData->id;
                $dataNotif['receiver_type'] = $employeeData->user_type;
                $dataNotif['receiver_department'] = $employeeData->department_id;
                ###notif message
                $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengajukan WFH to WFO pada tanggal ".date('l, d F Y',strtotime($employeeDate))."</strong> jam: ".date('H:i A',strtotime($employeeClockIn))."-".date('H:i A',strtotime($employeeClockOut))." untuk Anda.";
                ###insert data to notifications table
                $notifData = DB::table('notifications')->insert($dataNotif);
                
                // send notif to satgas
                foreach ($satgasDatas as $satgasData) {
                    $dataNotif['receiver_id'] = $satgasData->id;
                    $dataNotif['receiver_type'] = 'user';
                    $dataNotif['receiver_department'] = $gaDepartment;
                    ###notif message
                    $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengajukan WFH to WFO untuk <strong> ".ucwords($employeeFirstname)." ".ucwords($employeeLastname)."</strong> pada tanggal ".date('l, d F Y',strtotime($employeeDate))."</strong> jam: ".date('H:i A',strtotime($employeeClockIn))."-".date('H:i A',strtotime($employeeClockOut)).".";
                    ###insert data to notifications table
                    $notifData = DB::table('notifications')->insert($dataNotif);
                }
            }else{
                // send notif to satgas
                foreach ($satgasDatas as $satgasData) {
                    $dataNotif['receiver_id'] = $satgasData->id;
                    $dataNotif['receiver_type'] = 'user';
                    $dataNotif['receiver_department'] = $gaDepartment;
                    ###notif message
                    $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengajukan WFH to WFO pada tanggal ".date('l, d F Y',strtotime($employeeDate))."</strong> jam: ".date('H:i A',strtotime($employeeClockIn))."-".date('H:i A',strtotime($employeeClockOut)).".";
                    ###insert data to notifications table
                    $notifData = DB::table('notifications')->insert($dataNotif);
                }
            }
        //send notifications

        return redirect()->route('admin-wfh-to-wfo.index')->with('alert-success','Data berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman WFH to WFO.');
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
        $userLevel = Auth::user()->user_level;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $firstCheck = DB::table('wfh_to_wfo_request')->where('id',$id)->where('leader_id',$userId)->where('department_id',$userDepartment)->first();

        if (!isset($firstCheck) || $userCompany != 1) {
            return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman WFH to WFO.');
        }

        $data['requestData'] = $firstCheck;
        $data['departments'] = DB::table('department')->get();
        $data['users'] = DB::table('users')->where('department_id',$userDepartment)->where('active',1)->get();

        return view('admin.wfh-to-wfo.edit', $data);
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
        $userLevel = Auth::user()->user_level;
        $userType = Auth::user()->user_type;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $firstCheck = DB::table('wfh_to_wfo_request')->where('id',$id)->where('leader_id',$userId)->where('department_id',$userDepartment)->first();

        if (!isset($firstCheck) || $userCompany != 1) {
            return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman WFH to WFO.');
        }

        $request->validate([
            'date' => 'required',
            'employee_id' => 'required',
            'clock_in' => 'required',
            'clock_out' => 'required|after:clock_in',
            'description' => 'required',
        ]);

        $data = $request->except(['_token','submit','_method']);
        $data['leader_id'] = $userId;
        $data['department_id'] = $userDepartment;

        $employeeData = $request->employee_id;
        if ($employeeData == 'lid') {
            $data['employee_id'] = $userId;
            $data['employee_type'] = 'admin';
        }else{
            $data['employee_id'] = $employeeData;
            $data['employee_type'] = 'user';
        }

        $data['date'] = date('Y-m-d H:i:s',strtotime($request->date));
        $data['clock_in'] = date('H:i:s',strtotime($request->clock_in));
        $data['clock_out'] = date('H:i:s',strtotime($request->clock_out));

        $data['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        //send notifications
            $newEmployeeId = $data['employee_id'];
            $newEmployeeType = $data['employee_type'];

            $dataCheck = DB::table('wfh_to_wfo_request')->where('id',$id)->first();
            $oldEmployeeId = $dataCheck->employee_id;
            $oldEmployeeType = $dataCheck->employee_type;

            if ($newEmployeeId != $oldEmployeeId && $newEmployeeType != $oldEmployeeType) {
                $dataNotif['publisher_id'] = $userId;
                $dataNotif['publisher_type'] = $userType;
                $dataNotif['publisher_department'] = $userDepartment;
                $publisherName = Auth::user()->name;
                $publisherFirstname = Auth::user()->firstname;
                $publisherLastname = Auth::user()->lastname;
                if ($publisherFirstname !== null) {
                    $publisherName = ucfirst($publisherFirstname).' '.ucfirst($publisherLastname);
                }
                ###receiver id & type
                $receiverDatas = DB::table('wfh_to_wfo_request')->select('department_id','employee_id','employee_type','date','clock_in','clock_out')->where('id',$id)->first();
    
                if ($receiverDatas->employee_type == 'user') {
                    $employeeData = DB::table('users')->select('id','firstname','lastname','user_type','department_id')->where('id',$receiverDatas->employee_id)->first();
    
                    $employeeFirstname = $employeeData->firstname;
                    $employeeLastname = $employeeData->lastname;
                    $employeeDate = $receiverDatas->date;
                    $employeeClockIn = $receiverDatas->clock_in;
                    $employeeClockOut = $receiverDatas->clock_out;
    
                    //notif data
                    $dataNotif['receiver_id'] = $employeeData->id;
                    $dataNotif['receiver_type'] = $employeeData->user_type;
                    $dataNotif['receiver_department'] = $employeeData->department_id;
                    ###notif message
                    $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengajukan WFH to WFO pada tanggal ".date('l, d F Y',strtotime($employeeDate))."</strong> jam: ".date('H:i A',strtotime($employeeClockIn))."-".date('H:i A',strtotime($employeeClockOut))." untuk Anda.";
                    ###insert data to notifications table
                    $notifData = DB::table('notifications')->insert($dataNotif);
                }
                // send notif to satgas
                $gaDepartment = 4; //ga code
                $satgasCovid = 7; //satgas covid
    
                $satgasDatas = DB::table('users')->where('department_id',$gaDepartment)->where('user_level',$satgasCovid)->get();
    
                foreach ($satgasDatas as $satgasData) {
                    $dataNotif['receiver_id'] = $satgasData->id;
                    $dataNotif['receiver_type'] = 'user';
                    $dataNotif['receiver_department'] = $gaDepartment;
                    ###notif message
                    $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengajukan WFH to WFO untuk <strong> ".ucwords($employeeFirstname)." ".ucwords($employeeLastname)."</strong> pada tanggal ".date('l, d F Y',strtotime($employeeDate))."</strong> jam: ".date('H:i A',strtotime($employeeClockIn))."-".date('H:i A',strtotime($employeeClockOut)).".";
                    ###insert data to notifications table
                    $notifData = DB::table('notifications')->insert($dataNotif);
                }
            }
        //send notifications

        DB::table('wfh_to_wfo_request')->where('id',$id)->update($data);

        return redirect()->route('admin-wfh-to-wfo.index')->with('alert-success','Data berhasil diubah.');
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
        $userLevel = Auth::user()->user_level;
        $userType = Auth::user()->user_type;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $firstCheck = DB::table('wfh_to_wfo_request')->where('id',$id)->where('leader_id',$userId)->where('department_id',$userDepartment)->count();

        if ($userCompany == 1 && $firstCheck > 0) {
            DB::table('wfh_to_wfo_request')->delete($id);
    
            return redirect()->route('admin-wfh-to-wfo.index')->with('alert-success','Data berhasil dihapus.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman WFH to WFO.');
    }
}
