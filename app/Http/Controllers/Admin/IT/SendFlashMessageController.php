<?php

namespace App\Http\Controllers\Admin\IT;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class SendFlashMessageController extends Controller
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
        $userDepartment = Auth::user()->department_id;

        if ($userDepartment == 5) {
            $data['flashMessageDatas'] = DB::table('flash_messages as fm')
            ->select([
                'fm.*',
                DB::raw('(SELECT name FROM department WHERE department.id = fm.receiver_department) receiver_department_name'),
            ])
            ->where('publisher_id',$userId)
            ->where('publisher_type',$userType)
            ->orderBy('views','DESC')->get();

            //supporting data
            $data['adminsDatas'] = DB::table('admins')->get();
            $data['usersDatas'] = DB::table('users')->get();
            $data['techsDatas'] = DB::table('techs')->get();

            return view('admin.flash-message.index-table',$data);
        }

        return redirect()->back()->with('alert-danger','Sorry belum level!');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        if ($userDepartment == 5) {
            $data['currentUserId'] = Auth::user()->id;
            $data['currentUserType'] = Auth::user()->user_type;
            $data['taskPriorities'] = DB::table('tasks_level')->get();
            $data['userTypes'] = DB::table('user_type')->get();

            return view('admin.flash-message.create',$data);
        }

        return redirect()->back()->with('alert-danger','Sorry belum level!');
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

        if ($userDepartment == 5) {
            $request->validate([
                'message' => 'required|min:10',
                'views' => 'required',
                'receiver_type' => 'required',
                'receiver_id' => 'required',
            ]);
    
            // custom setting
            $receiverId = $request->receiver_id;
            $receiverType = $request->receiver_type;

            $request['created_at'] = Carbon::now()->format('Y-m-d H:i:s');

            if ($receiverType == 'admin') {
                $userData = DB::table('admins')->where('id',$receiverId)->first();
            }elseif($receiverType == 'user'){
                $userData = DB::table('users')->where('id',$receiverId)->first();
            }else{
                $userData = DB::table('techs')->where('id',$receiverId)->first();
                $userData->department_id =1;
            }

            //data
            $data = $request->except('_token','submit');

            $data['receiver_department'] = $userData->department_id;
            $data['publisher_id'] = $userId;
            $data['publisher_type'] = $userType;
            $data['publisher_department'] = $userDepartment;
    
            //insert to database
            DB::table('flash_messages')->insert($data);

            return redirect()->route('flash-messages.index')->with('alert-success','Pesan berhasil dikirimkan.');
        }

        return redirect()->back()->with('alert-danger','Sorry belum level!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Sorry belum level!');
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
        $userDepartment = Auth::user()->department_id;

        if ($userDepartment == 5) {
            $data['flashMessageData'] = DB::table('flash_messages as fm')
            ->select([
                'fm.*',
                DB::raw('(SELECT name FROM department WHERE department.id = fm.receiver_department) receiver_department_name'),
            ])
            ->where('id',$id)
            ->where('publisher_id',$userId)
            ->where('publisher_type',$userType)
            ->orderBy('views','DESC')->first();

            // custom setting
            $dataFlashMessage = $data['flashMessageData'];
            $receiverId = $dataFlashMessage->receiver_id;
            $receiverType = $dataFlashMessage->receiver_type;

            if ($receiverType == 'admin') {
                $userDatas = DB::table('admins')->get();
                $userData = DB::table('admins')->where('id',$receiverId)->first();
            }elseif($receiverType == 'user'){
                $userDatas = DB::table('users')->get();
                $userData = DB::table('users')->where('id',$receiverId)->first();
            }else{
                $userDatas = DB::table('techs')->get();
                $userData = DB::table('techs')->where('id',$receiverId)->first();
                $userData->department_id = 1;
            }
            $data['userData'] = $userData;
            $data['userDatas'] = $userDatas;

            //supporting data
            $data['currentUserId'] = Auth::user()->id;
            $data['currentUserType'] = Auth::user()->user_type;
            //$data['taskPriorities'] = DB::table('tasks_level')->get();
            $data['userTypes'] = DB::table('user_type')->get();

            return view('admin.flash-message.edit',$data);
        }

        return redirect()->back()->with('alert-danger','Sorry belum level!');
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
            $request->validate([
                'message' => 'required|min:10',
                'views' => 'required',
                'receiver_type' => 'required',
                'receiver_id' => 'required',
            ]);
    
            // custom setting
            $receiverId = $request->receiver_id;
            $receiverType = $request->receiver_type;

            $request['created_at'] = Carbon::now()->format('Y-m-d H:i:s');

            if ($receiverType == 'admin') {
                $userData = DB::table('admins')->where('id',$receiverId)->first();
            }elseif($receiverType == 'user'){
                $userData = DB::table('users')->where('id',$receiverId)->first();
            }else{
                $userData = DB::table('techs')->where('id',$receiverId)->first();
                $userData->department_id =1;
            }

            //data
            $data = $request->except('_token','_method','submit');

            $data['receiver_department'] = $userData->department_id;
            $data['publisher_id'] = $userId;
            $data['publisher_type'] = $userType;
            $data['publisher_department'] = $userDepartment;
    
            //insert to database
            DB::table('flash_messages')->where('id',$id)->update($data);

            return redirect()->route('flash-messages.index')->with('alert-success','Pesan berhasil dikirimkan.');
        }

        return redirect()->back()->with('alert-danger','Sorry belum level!');
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
            DB::table('flash_messages')->delete($id);
            return redirect()->route('flash-messages.index')->with('alert-success','Pesan berhasil dikirimkan.');
        }

        return redirect()->back()->with('alert-danger','Sorry belum level!');
    }
}
