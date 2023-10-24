<?php

namespace App\Http\Controllers\User\GA\Covid;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class WfhToWfoController extends Controller
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
    public function index()
    {
        $userId = Auth::user()->id;
        $userLevel = Auth::user()->user_level;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if ($userCompany == 1) {
            $data['requesterAdmins'] = DB::table('admins')->where('active',1)->get();
            $data['requesterUsers'] = DB::table('users')->where('active',1)->get();

            if ($userCompany == 1 && $userDepartment == 4 && $userLevel == 7) {
                $data['requestDatas'] = DB::table('wfh_to_wfo_request as wtwr')
                ->select([
                    'wtwr.*',
                    DB::raw('(SELECT name FROM department WHERE department.id = wtwr.department_id) as dept_name'),
                    DB::raw('(SELECT name FROM wfh_to_wfo_request_status WHERE wfh_to_wfo_request_status.id = wtwr.status) as status_name'),
                ])
                ->orderBy('id','DESC')
                ->paginate(10);
                
                return view('user.wfh-to-wfo.index', $data);
            }else{
                $data['requestDatas'] = DB::table('wfh_to_wfo_request as wtwr')
                ->select([
                    'wtwr.*',
                    DB::raw('(SELECT name FROM department WHERE department.id = wtwr.department_id) as dept_name'),
                    DB::raw('(SELECT name FROM wfh_to_wfo_request_status WHERE wfh_to_wfo_request_status.id = wtwr.status) as status_name'),
                ])
                ->where('employee_id',$userId)
                ->orderBy('id','DESC')
                ->paginate(10);
                return view('user.wfh-to-wfo.index-table', $data);
            }
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman WFH to WFO.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman WFH to WFO.');
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

        if ($userCompany == 1 && $userDepartment == 4 && $userLevel == 7) {
            $data = $request->only('status');
            
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
                $receiverDatas = DB::table('wfh_to_wfo_request')->select('leader_id','department_id','employee_id','employee_type','date','clock_in','clock_out')->where('id',$id)->first();

                if ($receiverDatas->employee_type == 'admin') {
                    $employeeData = DB::table('admins')->select('firstname','lastname')->where('id',$receiverDatas->employee_id)->first();
                }else{
                    $employeeData = DB::table('users')->select('firstname','lastname')->where('id',$receiverDatas->employee_id)->first();
                }

                $employeeFirstname = $employeeData->firstname;
                $employeeLastname = $employeeData->lastname;
                $employeeDate = $receiverDatas->date;
                $employeeClockIn = $receiverDatas->clock_in;
                $employeeClockOut = $receiverDatas->clock_out;

                //notif data
                $dataNotif['receiver_id'] = $receiverDatas->leader_id;
                $dataNotif['receiver_type'] = 'admin';
                $dataNotif['receiver_department'] = $receiverDatas->department_id;
                ###notif message
                $dataNotif['desc'] = "<strong>".$publisherName."</strong> (satgas Covid) menyetujui pengubahan WFH to WFO untuk <strong> ".ucwords($employeeFirstname)." ".ucwords($employeeLastname)."</strong> pada tanggal ".date('l, d F Y',strtotime($employeeDate))."</strong> jam: ".date('H:i A',strtotime($employeeClockIn))."-".date('H:i A',strtotime($employeeClockOut)).".";
                ###insert data to notifications table
                $notifData = DB::table('notifications')->insert($dataNotif);
                
            //send notifications

            DB::table('wfh_to_wfo_request')->where('id',$id)->update($data);

            return redirect()->back()->with('alert-success','Data berhasil diubah.');
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman WFH to WFO.');
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
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if ($userCompany == 1 && $userDepartment == 4 && $userLevel == 7) {
            DB::table('wfh_to_wfo_request')->delete($id);

            return redirect()->back()->with('alert-success','Data berhasil dihapus.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman WFH to WFO.');
    }
}
