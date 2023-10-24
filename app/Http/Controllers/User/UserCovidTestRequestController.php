<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Auth;
use DB;

class UserCovidTestRequestController extends Controller
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
        $userDepartment = Auth::user()->department_id;

        if ($userDepartment == 4 && $userLevel == 7) {
            
            $data['covidDatas'] = DB::table('covid_test_request as ctr')
            ->select([
                'ctr.*',
                //DB::raw('(SELECT image FROM covid_image WHERE covid_image.ctr_id = ctr.id AND covid_image.image IS NOT NULL) as image'),
                //DB::raw('(SELECT type FROM covid_image WHERE covid_image.ctr_id = ctr.id AND covid_image.type IS NOT NULL) as image_type'),
                DB::raw('(SELECT name FROM covid_test_request_status WHERE covid_test_request_status.id = ctr.status) as status_name'),
            ])
            ->orderBy('id','DESC')
            ->paginate(10);

            $data['covidImagesDatas'] = DB::table('covid_image')->get();

            $data['requesterAdmins'] = DB::table('admins')->where('active',1)->get();
            $data['requesterUsers'] = DB::table('users')->where('active',1)->get();

            return view('user.covid-test.index', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Covid Test Request.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $userId = Auth::user()->id;
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        $data['departments'] = DB::table('department')->get();

        if ($userDepartment == 4 && $userLevel == 7) {

            if ($request->ctr_id != null) {
                $data['ctr_id'] = $request->ctr_id;
                $data['covidData'] = DB::table('covid_test_request as ctr')
                ->select([
                    'ctr.*',
                    DB::raw('(SELECT name FROM covid_image WHERE covid_image.id = ctr.image_id) as image'),
                    DB::raw('(SELECT type FROM covid_image WHERE covid_image.id = ctr.image_id) as image_type'),
                    DB::raw('(SELECT name FROM covid_test_request_status WHERE covid_test_request_status.id = ctr.status) as status_name'),
                    //requester
                    DB::raw('(SELECT name FROM admins WHERE admins.id = ctr.requester_id AND ctr.requester_id IS NOT NULL) as requester_name_by_id'),
                ])
                ->first();

                return view('user.covid-test.upload', $data);
            }

            return view('user.covid-test.create', $data);
        }elseif($userDepartment == 1 && $userLevel == 2){

            $projectId = $request->pid;
            $taskId = $request->tid;
            $data['dataDepartment'] = $userDepartment;

            $data['projectTaskInfo'] = DB::table('projects_task')
                ->select([
                    'projects_task.*',
                    DB::raw('(SELECT firstname FROM techs WHERE techs.id = projects_task.tech_id) as tech_firstname'),
                    DB::raw('(SELECT lastname FROM techs WHERE techs.id = projects_task.tech_id) as tech_lastname'),
                    //user
                    DB::raw('(SELECT firstname FROM users WHERE users.id = projects_task.pc_id) as pc_firstname'),
                    DB::raw('(SELECT lastname FROM users WHERE users.id = projects_task.pc_id) as pc_lastname'),
                    //project
                    DB::raw('(SELECT name FROM projects WHERE projects.id = projects_task.project_id) as project_name'),
                ])
                ->where('id',$taskId)
                ->where('pc_id',$userId)
                ->where('deleted_at',null)->first();

            return view('user.covid-test.create-pc', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Covid Test Request.');
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
        $userDepartment = Auth::user()->department_id;

        $gaDepartment = 4; //ga code
        $satgasCovid = 7; //satgas covid

        $request->validate([
            'name' => 'required',
            'nik' => 'required',
            'project_name' => 'required',
            'department_id' => 'required',
            'title' => 'required',
            'destination' => 'required',
            'address' => 'required',
            'date' => 'required|after_or_equal:'.date('Y-m-d'),
        ]);

        if ($userDepartment == 4 && $userLevel == 7) {
            
            $data = $request->except('_token','submit');
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');

            //insert to database
            DB::table('covid_test_request')->insert($data);

            return redirect()->route('user-covid-test.index')->with('alert-success','Data berhasil disimpan.');
        }elseif($userDepartment == 1 && $userLevel == 2){

            $data = $request->except('_token','submit');
            $data['requester_id'] = $userId;
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');

            //insert to database
            DB::table('covid_test_request')->insert($data);

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
                $receiverDatas = DB::table('users')->where('department_id',$gaDepartment)->where('user_level',$satgasCovid)->get();

                foreach ($receiverDatas as $receiverData) {
                    $dataNotif['receiver_id'] = $receiverData->id;
                    $dataNotif['receiver_type'] = 'user';
                    $dataNotif['receiver_department'] = $gaDepartment;
                    ###notif message
                    $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengajukan pembuatan surat pengantar tes Covid-19 untuk <strong> ".ucwords($data['name'])."</strong> yang akan digunakan tanggal ".date('l, d F Y',strtotime($data['date'])).".</strong>";
                    ###insert data to notifications table
                    $notifData = DB::table('notifications')->insert($dataNotif);
                }
            //send notifications

            return redirect()->route('user-covid-test.index-pc')->with('alert-success','Data berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Covid Test Request.');
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

        if ($userDepartment == 4 && $userLevel == 7) {

            //update status
            $status['status'] = 2;
            DB::table('covid_test_request')->where('id',$id)->update($status);

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

            return view('user.covid-test.show', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Covid Test Request.');
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
        $userDepartment = Auth::user()->department_id;

        $covidStatus = 3;

        if ($userDepartment == 4 && $userLevel == 7) {

            $data['departments'] = DB::table('department')->get();

            $dataCount = DB::table('covid_test_request as ctr')
            ->where('id',$id)
            ->where('status','!=',$covidStatus)
            ->count();

            if($dataCount < 1){
                return redirect()->back()->with('alert-danger','Data Covid Test Request tidak bisa diedit.');
            }

            $data['covidData'] = DB::table('covid_test_request as ctr')
            ->select([
                'ctr.*',
                DB::raw('(SELECT name FROM covid_image WHERE covid_image.id = ctr.image_id) as image'),
                DB::raw('(SELECT type FROM covid_image WHERE covid_image.id = ctr.image_id) as image_type'),
                DB::raw('(SELECT name FROM covid_test_request_status WHERE covid_test_request_status.id = ctr.status) as status_name'),
                //requester
                DB::raw('(SELECT name FROM admins WHERE admins.id = ctr.requester_id AND ctr.requester_id IS NOT NULL) as requester_name_by_id'),
            ])
            ->where('id',$id)
            ->where('status','!=',$covidStatus)
            ->first();

            return view('user.covid-test.edit', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Covid Test Request.');
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

        $covidStatus = 3;

        if ($userDepartment == 4 && $userLevel == 7) {

            if ($request->status == 3) {
                //check priviledge
                $dataCheck = DB::table('covid_test_request')->where('id',$id)->first();

                //sent notifications
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
                $dataAdmin = DB::table('admins')->where('department_id',$userDepartment)->first();
                $dataNotif['receiver_id'] = $dataAdmin->id;
                $dataNotif['receiver_type'] = 'admin';
                $dataNotif['receiver_department'] = $userDepartment;
                ###notif message
                $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengubah status pembuatan surat pengantar tes Covid-19 untuk <strong> ".ucwords($dataCheck->name)."</strong> menjadi done.</strong>";
                ###insert data to notifications table
                $notifData = DB::table('notifications')->insert($dataNotif);

                //update data
                $data = $request->except(['_token','_method','submit']);

                DB::table('covid_test_request')->where('id',$id)->update($data);

                return redirect()->route('user-covid-test.index')->with('success', 'Data berhasil diupdate.');
            }

            //check priviledge
            $dataCheck = DB::table('covid_test_request')->where('id',$id)->where('status','<',$covidStatus)->count();

            if ($dataCheck > 0) {
                
                $request->validate([
                    'name' => 'required',
                    'nik' => 'required',
                    'project_name' => 'required',
                    'department_id' => 'required',
                    'title' => 'required',
                    'destination' => 'required',
                    'address' => 'required',
                ]);

                //update data
                $data = $request->except(['_token','_method','submit']);

                DB::table('covid_test_request')->where('id',$id)->update($data);

                return redirect()->route('user-covid-test.index')->with('success', 'Data berhasil diupdate.');
            }

            return view('user.covid-test.index');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Covid Test Request.');
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
        $userDepartment = Auth::user()->department_id;

        $covidStatus = 3;

        if ($userDepartment == 4 && $userLevel == 7) {

            //check priviledge
            $dataCheck = DB::table('covid_test_request')->where('id',$id)->where('status','<',$covidStatus)->count();

            if ($dataCheck > 0) {
                //delete previous image
                $destinationPath = public_path().'/img/covid-test/';
                $dataImages = DB::table('covid_image')->select('image as image')->where('ctr_id', $id)->get();

                if (count($dataImages) > 0) {
                    foreach($dataImages as $dataImage){
                        $oldImage = $dataImage->image;
        
                        if($oldImage !== 'default.png'){
                            $image_path = $destinationPath.$oldImage;
                            if(File::exists($image_path)) {
                                File::delete($image_path);
                            }
                            //delete from database
                            DB::table('covid_image')->where('ctr_id',$id)->delete();
                        }
                    }
                }

                //delete from database
                DB::table('covid_test_request')->delete($id);

                return redirect()->route('user-covid-test.index')->with('success', 'Data berhasil dihapus.');
            }

            return view('user.covid-test.index');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Covid Test Request.');
    }

    public function uploadImage(Request $request)
    {
        $userId = Auth::user()->id;
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        if ($userDepartment == 4 && $userLevel == 7) {

            //check priviledge
            $dataCheck = DB::table('covid_test_request')->where('id',$request->ctr_id)->count();

            if ($dataCheck > 0) {

                $request->validate([
                    'image' => 'required|mimes:jpeg,jpg,png,pdf|max:9216',
                ]);
        
                //file handler
                $fileName = null;
                $destinationPath = public_path().'/img/covid-test/';
                
                // Retrieving An Uploaded File
                $file = $request->file('image');
                if (!empty($file)) {
                    $extension = $file->getClientOriginalExtension();
                    $fileName = time().'_'.$file->getClientOriginalName();
            
                    // Moving An Uploaded File
                    $request->file('image')->move($destinationPath, $fileName);
                }
        
                //custom setting to support file upload
                $data = $request->except(['_token','submit']);
                
                $data['ctr_id'] = $request->ctr_id;
                $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        
                if (!empty($fileName)) {
                    $data['image'] = $fileName;
                }
        
                DB::table('covid_image')->insert($data);

                return redirect()->route('user-covid-test.index')->with('success', 'Data berhasil dihapus.');
            }

            return view('user.covid-test.index');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Covid Test Request.');
    }
}
