<?php

namespace App\Http\Controllers\User\Task\Collaboration\MultiDepartment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class TaskLeaderPicController extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Collaboration Task PIC.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        $taskId = $request->tid;
        $departmentId = $request->did;
        $coAdmin = 22; //userlevel

        $firstCheck = DB::table('tasks_leaders')->where('id',$taskId)->where('coadmin_id','LIKE','%'.$userId.'%')->first();
        $publisherCheck = DB::table('tasks_leaders')->where('id',$taskId)->where('publisher_id',$userId)->where('publisher_type',$userType)->first();

        if ($userLevel == $coAdmin && (isset($firstCheck) || isset($publisherCheck))) {
            $data['picDatas'] = DB::table('users')->where('department_id',$departmentId)->where('active',1)->whereNull('deleted_at')->get();
            if (isset($firstCheck)) {
                $data['taskData'] = $firstCheck;
            }else{
                $data['taskData'] = $publisherCheck;
            }
            $data['departmentId'] = $departmentId;

            return view('user.task.collaboration.task-leader.pic.create',$data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Collaboration Task PIC.');
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
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        $taskId = $request->task_id;
        $departmentId = $request->department_id;
        $picId = $request->pic_id;
        $coAdmin = 22; //userlevel

        $firstCheck = DB::table('tasks_leaders_pic')->where('task_id',$taskId)->where('pic_id',$picId)->first();
        $publisherCheck = DB::table('tasks_leaders')->where('id',$taskId)->where('publisher_id',$userId)->where('publisher_type',$userType)->count();

        if (isset($firstCheck) || $publisherCheck < 1) {
            return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia. Atau user yang Anda pilih telah terdaftar sebagai PIC pada proyek ini.');
        }

        if ($userLevel == $coAdmin && !isset($firstCheck) && $publisherCheck  > 0) {
            $request->validate([
                'pic_id' => 'required',
            ]);
    
            // date setting
            $data = $request->except('_token','submit');
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');

            //insert to database
            DB::table('tasks_leaders_pic')->insert($data);
    
            //send notifications
                $theDepartment = $departmentId;
                $taskLeader = DB::table('tasks_leaders')->select('id','title','publisher_department')->where('id',$taskId)->first();
                $theId = $taskLeader->id;
                $theTitle = $taskLeader->title;
                $pubDepartment = $taskLeader->publisher_department;
    
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
                //admin
                $collaboratorDatas = DB::table('admins')->get();
                foreach ($collaboratorDatas as $dataAlpha) {
                    if ($dataAlpha->department_id != $userDepartment) {
                        if ($dataAlpha->department_id == $theDepartment || $dataAlpha->department_id == $pubDepartment) {
                            $dataNotif['receiver_id'] = $dataAlpha->id;
                            $dataNotif['receiver_type'] = $dataAlpha->user_type;
                            $dataNotif['receiver_department'] = $dataAlpha->department_id;

                            $dataNotif['level'] = 1;
        
                            ###notif message
                            $dataNotif['desc'] = "<strong>".$publisherName."</strong> menambahkan PIC dalam proyek <a href='".route('task-leaders.show',$theId)."'><strong>".ucfirst($theTitle)."</strong></a>.</strong>";
                            ###insert data to notifications table
                            $notifData = DB::table('notifications')->insert($dataNotif);
                        }
                    }
                }
                //staff / pic
                $dataBeta = DB::table('tasks_leaders_pic')->orderBy('id','DESC')->first();
                $dataNotif['receiver_id'] = $dataBeta->pic_id;
                $dataNotif['receiver_type'] = 'user';
                $dataNotif['receiver_department'] = $dataBeta->department_id;

                $dataNotif['level'] = 1;

                ###notif message
                $dataNotif['desc'] = "<strong>".$publisherName."</strong> menunjuk Anda sebagai PIC dalam proyek <a href='".route('user-task-leaders.show',$theId)."'><strong>".ucfirst($theTitle)."</strong></a> untuk departemen Anda.";
                ###insert data to notifications table
                $notifData = DB::table('notifications')->insert($dataNotif);
                
            //send notifications end
    
            return redirect()->route('user-task-leaders.show',$taskId)->with('alert-success','PIC collaborative leader berhasil didaftarkan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Collaboration Task PIC.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Collaboration Task PIC.');
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
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        $theId = explode("&", $id, 2)[0];
        $taskId = explode("&", $id, 2)[1];
        $coAdmin = 22; //userlevel

        $firstCheck = DB::table('tasks_leaders')->where('id',$taskId)->first();
        $secondCheck = DB::table('tasks_leaders_pic')->where('id',$theId)->first();
        $thirdCheck = DB::table('tasks_leaders')->where('id',$taskId)->where('coadmin_id','LIKE','%'.$userId.'%')->first();
        $publisherCheck = DB::table('tasks_leaders')->where('id',$taskId)->where('publisher_id',$userId)->where('publisher_type',$userType)->first();

        if (!isset($firstCheck) || !isset($secondCheck) || (!isset($thirdCheck) && !isset($publisherCheck))) {
            return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
        }

        $departmentId = $secondCheck->department_id;
        $deptCheck = in_array($departmentId,unserialize($firstCheck->receiver_department));

        if ($userLevel == $coAdmin && $deptCheck > 0){
            $data['picDatas'] = DB::table('users')->where('department_id',$departmentId)->where('active',1)->whereNull('deleted_at')->get();

            $data['taskData'] = $firstCheck;
            $data['currentPicData'] = $secondCheck;
            $data['departmentId'] = $departmentId;

            return view('user.task.collaboration.task-leader.pic.edit',$data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Collaboration Task PIC.');
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
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        $taskId = $request->task_id;
        $picId = $request->pic_id;
        $departmentId = $request->department_id;
        $coAdmin = 22; //userlevel

        $firstCheck = DB::table('tasks_leaders')->where('id',$taskId)->first();
        $secondCheck = DB::table('tasks_leaders_pic')->where('task_id',$taskId)->where('department_id',$departmentId)->where('pic_id',$picId)->first();
        
        if (!isset($firstCheck) || isset($secondCheck)) {
            return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia. Atau user yang Anda pilih telah terdaftar sebagai PIC pada proyek ini.');
        }

        $thirdCheck = DB::table('tasks_leaders')->where('id',$taskId)->where('coadmin_id','LIKE','%'.$userId.'%')->count();
        $publisherCheck = DB::table('tasks_leaders')->where('id',$taskId)->where('publisher_id',$userId)->where('publisher_type',$userType)->count();

        if ($userLevel == $coAdmin && ($thirdCheck > 0 || $publisherCheck > 0)) {
            $request->validate([
                'pic_id' => 'required',
            ]);
    
            // date setting
            $data = $request->except('_token','submit','_method');
            $data['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

            //insert to database
            DB::table('tasks_leaders_pic')->where('id',$id)->update($data);
    
            //send notifications
                $theDepartment = $departmentId;
                $taskLeader = DB::table('tasks_leaders')->select('id','title','publisher_department')->where('id',$taskId)->first();
                $theId = $taskLeader->id;
                $theTitle = $taskLeader->title;
                $pubDepartment = $taskLeader->publisher_department;
    
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
                //admin
                $collaboratorDatas = DB::table('admins')->get();
                foreach ($collaboratorDatas as $dataAlpha) {
                    if ($dataAlpha->department_id != $userDepartment) {
                        if ($dataAlpha->department_id == $theDepartment || $dataAlpha->department_id == $pubDepartment) {
                            $dataNotif['receiver_id'] = $dataAlpha->id;
                            $dataNotif['receiver_type'] = $dataAlpha->user_type;
                            $dataNotif['receiver_department'] = $dataAlpha->department_id;

                            $dataNotif['level'] = 1;
        
                            ###notif message
                            $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengubah PIC dalam proyek <a href='".route('task-leaders.show',$theId)."'><strong>".ucfirst($theTitle)."</strong></a>.</strong>";
                            ###insert data to notifications table
                            $notifData = DB::table('notifications')->insert($dataNotif);
                        }
                    }
                }
                //staff / pic
                $dataBeta = DB::table('tasks_leaders_pic')->where('id',$id)->first();
                $dataNotif['receiver_id'] = $dataBeta->pic_id;
                $dataNotif['receiver_type'] = 'user';
                $dataNotif['receiver_department'] = $dataBeta->department_id;

                $dataNotif['level'] = 1;

                ###notif message
                $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengubah PIC/menunjuk Anda sebagai PIC dalam proyek <a href='".route('user-task-leaders.show',$theId)."'><strong>".ucfirst($theTitle)."</strong></a> untuk departemen Anda.";
                ###insert data to notifications table
                $notifData = DB::table('notifications')->insert($dataNotif);
            //send notifications end
    
            return redirect()->route('user-task-leaders.show',$taskId)->with('alert-success','PIC collaborative leader berhasil diubah.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Collaboration Task PIC.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        $taskId = $request->task_id;
        $coAdmin = 22; //userlevel

        $firstCheck = DB::table('tasks_leaders')->where('id',$taskId)->first();
        $secondCheck = DB::table('tasks_leaders_pic')->where('id',$id)->first();
        $thirdCheck = DB::table('tasks_leaders')->where('id',$taskId)->where('coadmin_id','LIKE','%'.$userId.'%')->count();
        $publisherCheck = DB::table('tasks_leaders')->where('id',$taskId)->where('publisher_id',$userId)->where('publisher_type',$userType)->count();
        
        if (isset($firstCheck) && isset($secondCheck) && $userLevel == $coAdmin && ($thirdCheck > 0 || $publisherCheck > 0)) {
            DB::table('tasks_leaders_pic')->delete($id);

            return redirect()->back()->with('alert-success','Data berhasil dihapus.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Collaboration Task PIC.');
    }
}
