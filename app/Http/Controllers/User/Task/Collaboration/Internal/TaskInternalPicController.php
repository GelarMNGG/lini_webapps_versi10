<?php

namespace App\Http\Controllers\User\Task\Collaboration\Internal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;

class TaskInternalPicController extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Internal Department Collaboration Task PIC.');
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
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $taskId = $request->tid;
        $coAdmin = 22; //userlevel
        $liniId = 1; //lini

        $firstCheck = DB::table('tasks_internal')->where('id',$taskId)->where('coadmin_id','LIKE','%'.$userId.'%')->first();
        $publisherCheck = DB::table('tasks_internal')->where('id',$taskId)->where('publisher_id',$userId)->where('publisher_type',$userType)->first();

        if ($userCompany == $liniId && (isset($firstCheck) || isset($publisherCheck))) {
            $data['picDatas'] = DB::table('users')->where('department_id',$userDepartment)->where('active',1)->whereNull('deleted_at')->get();
            if (isset($firstCheck)) {
                $data['taskData'] = $firstCheck;
            }else{
                $data['taskData'] = $publisherCheck;
            }

            return view('user.task.collaboration.task-internal.pic.create',$data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Internal Department Collaboration Task PIC.');
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
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $liniId = 1; //lini

        $taskId = $request->task_id;
        $departmentId = $request->department_id;
        $picId = $request->pic_id;

        $firstCheck = DB::table('tasks_internal_pic')->where('task_id',$taskId)->where('pic_id',$picId)->first();
        $publisherCheck = DB::table('tasks_internal')->where('id',$taskId)->where('publisher_id',$userId)->where('publisher_type',$userType)->count();

        if (isset($firstCheck) || $publisherCheck < 1) {
            return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia. Atau user yang Anda pilih telah terdaftar sebagai PIC pada proyek ini.');
        }

        if ($userCompany == $liniId && !isset($firstCheck) && $publisherCheck > 0) {
            $request->validate([
                'pic_id' => 'required',
            ]);
    
            // date setting
            $data = $request->except('_token','submit');
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');

            //insert to database
            DB::table('tasks_internal_pic')->insert($data);
    
            //send notifications
                $theDepartment = $departmentId;
                $taskLeader = DB::table('tasks_internal')->select('id','title')->where('id',$taskId)->first();
                $theId = $taskLeader->id;
                $theTitle = $taskLeader->title;
    
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
                //staff / pic
                $dataBeta = DB::table('tasks_internal_pic')->orderBy('id','DESC')->first();
                $dataNotif['receiver_id'] = $dataBeta->pic_id;
                $dataNotif['receiver_type'] = 'user';
                $dataNotif['receiver_department'] = $userDepartment;

                $dataNotif['level'] = 1;

                ###notif message
                $dataNotif['desc'] = "<strong>".$publisherName."</strong> menunjuk Anda sebagai PIC dalam proyek <a href='".route('user-task-internal.show',$theId)."'><strong>".ucfirst($theTitle)."</strong></a> untuk departemen Anda.";
                ###insert data to notifications table
                $notifData = DB::table('notifications')->insert($dataNotif);
                
            //send notifications end
    
            return redirect()->route('user-task-internal.show',$taskId)->with('alert-success','PIC collaborative internal berhasil didaftarkan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Internal Department Collaboration Task PIC.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Internal Department Collaboration Task PIC.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-warning','Fitur sedang dalam pengembangan. Cobalah beberapa saat lagi.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Internal Department Collaboration Task PIC.');
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
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $liniId = 1; //lini
        $taskId = $request->task_id;

        $firstCheck = DB::table('tasks_internal')->where('id',$taskId)->where('publisher_id',$userId)->where('publisher_type',$userType)->first();;
        $secondCheck = DB::table('tasks_internal_pic')->where('id',$id)->first();

        if ($userCompany == $liniId && isset($firstCheck)) {
            DB::table('tasks_internal_pic')->delete($id);

            //delete todo list
            $picId = $secondCheck->pic_id;
            $receiverType = $secondCheck->pic_type;
            $todoDatas = DB::table('tasks_internal_todos')->where('task_id',$taskId)->where('receiver_id',$picId)->where('receiver_type',$receiverType)->get();
            foreach ($todoDatas as $todo) {
                DB::table('tasks_internal_todos')->delete($todo->id);
            }

            $thirdCheck = DB::table('tasks_internal_todos_files')->where('task_id',$taskId)->where('todo_id',$id)->get();
            //delete previous image
            if (isset($thirdCheck)) {
                $destinationPath = public_path().'/img/upload-doc/task-internal/';
                foreach ($thirdCheck as $dataImage) {
                    $oldImage = $dataImage->image;
        
                    if($oldImage !== 'default.png'){
                        $image_path = $destinationPath.$oldImage;
                        if(File::exists($image_path)) {
                            File::delete($image_path);
                        }
                    }
                    DB::table('tasks_internal_todos_files')->delete($dataImage->id);
                }
            }

            return redirect()->back()->with('alert-success','Data berhasil dihapus.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Internal Department Collaboration Task PIC.');
    }
}
