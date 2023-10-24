<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Auth;
use DB;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;
        
        $liniId = 1;
        $prStatus = 2;
        $data['prDatas'] = [];
        $data['prStatus'] = [];

        $manualType = 2; //tipe manual book
        $published = 4; //corporate
        $publishedTroubleshooting = 4;
        $activeStatus = 1; //active

        ///////////chart start
            ###minutes by its status
                $one_week_ago = Carbon::now()->subDays(7)->format('Y-m-d H:i:s');
                $now = Carbon::now()->format('Y-m-d H:i:s');
                //all user
                $doneAll = DB::table('minutes as min')
                    ->select([
                        'min.date',
                        DB::raw('COUNT(status = 0) as "done"'),
                    ])
                    ->where('status',1)
                    //->where('date', '<', $now)
                    ->where('date', '>', $one_week_ago)
                    ->where('department_id',$userDepartment)
                    ->groupBy(DB::raw('Date(date)'))
                    ->orderBy('date', 'ASC')
                    ->get();
                $inProgressAll = DB::table('minutes as min')
                    ->select([
                        'min.date',
                        DB::raw('COUNT(*) as "inprogress"'),
                    ])
                    ->where('status',0)
                    ->where('date', '>', $one_week_ago)
                    ->where('department_id',$userDepartment)
                    ->groupBy(DB::raw('Date(date)'))
                    ->orderBy('date', 'ASC')
                    ->get();

                $data['minuteAllDone'] = $doneAll;
                $data['minuteAllInProgress'] = $inProgressAll;

                $data['totalAllDone'] = count($doneAll);
                $data['totalAllInProgress'] = count($inProgressAll);
            ###minutes by its status end
            ###minutes by its category
            $minuteCats = DB::table('minutes_category as mincat')
            ->leftJoin('minutes','minutes.minute_cat','mincat.id')
            ->select([
                'mincat.name',
                DB::raw("(SELECT COUNT(*) FROM minutes WHERE minutes.minute_cat = mincat.id AND minutes.publisher_department = mincat.department_id) as cat_count"),
            ])
            ->where('mincat.department_id',$userDepartment)
            ->get()->pluck('name','cat_count');

            $data['catNameDatas'] = array_values($minuteCats->toArray());
            $data['catCountDatas'] = array_keys($minuteCats->toArray());
            $data['datacatCountDatas'] = array_sum($data['catCountDatas']);
            ###minutes by its category end
        ///////////chart end

        if ($userDepartment == 9) {
            $data['prDatas'] = DB::table('purchase_requisition as pr')
            ->select([
                'pr.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pr.project_id) as project_name'),
            ])
            ->where('status',$prStatus)
            ->where('deleted_at', null)->get();

            $data['prStatus'] = DB::table('purchase_requisition_status')->get();
        }

        //team data
        $data['dataAdmin'] = DB::table('admins')
        ->select([
            'admins.*',
            DB::raw('(SELECT name FROM department WHERE department.id = admins.department_id) as dept_name')
        ])
        ->where('active',$activeStatus)->where('department_id',$userDepartment)->limit(1)->first();

        if ($userCompany == $liniId) {
            $data['dataTeams'] = DB::table('users')
            ->select([
                'users.*',
                DB::raw('(SELECT name FROM users_level WHERE users_level.id = users.user_level AND users_level.department_id = users.department_id OR users_level.role = users.user_level AND users_level.role IS NOT NULL) as user_title')
            ])
            ->where('active',$activeStatus)->where('department_id',$userDepartment)->orderBy('user_level','DESC')->limit(10)->get();
        }else{
            $data['dataTeams'] = DB::table('users')
            ->select([
                'users.*',
                DB::raw('(SELECT name FROM users_level WHERE users_level.id = users.user_level AND users_level.department_id = users.department_id OR users_level.role = users.user_level AND users_level.role IS NOT NULL) as user_title')
            ])
            ->where('active',$activeStatus)->where('company_id',$userCompany)->orderBy('user_level','DESC')->limit(10)->get();
        }

        //manuals & troubleshooting
        $data['dataManuals'] = DB::table('blogs')->where('type',$manualType)->where('status',$published)->orderBy('views','DESC')->orderBy('created_at','DESC')->limit(4)->get();
        $data['dataTroubleshootings'] = DB::table('troubleshooting')->where('status',$publishedTroubleshooting)->orderBy('view','DESC')->orderBy('date','DESC')->limit(4)->get();

        //flash message
        $data['flashMessageData'] = DB::table('flash_messages')
            ->where('receiver_id',$userId)
            ->where('receiver_department',$userDepartment)
            ->where('receiver_type',$userType)
            ->where('views','>',0)->first();

        $dataFlashCheck = $data['flashMessageData'];
        if (isset($dataFlashCheck)) {
            DB::table('flash_messages')->where('id',$dataFlashCheck->id)->decrement('views',1);
        }

        //32 basic rules of conduct
        $data['basicRulesofConduct'] = DB::table('basic_rules_of_conducts')->inRandomOrder()->first();

        //Update aplikasi
        $updateAppsCatAll = 1; //users
        $updateAppsCat = 4; //users
        $data['appsUpdateDatas'] = DB::table('apps_update')->where('cat_id',$updateAppsCat)
        ->OrWhere('cat_id',$updateAppsCatAll)->limit(5)
        //->orderBy(DB::raw('YEAR(created_at), MONTH(created_at), HOUR(created_at)'),'DESC')
        ->latest()
        ->get();

        //sliders
        $theStatus = 1;
        $data['sliders'] = DB::table('sliders')
        ->select([
            'sliders.*',
            DB::raw('COUNT(*) as slidersCount')
        ])
        ->where('status',$theStatus)->get();

        return view('admin.index', $data);
    }

    public function allMembersByDepartment()
    {
        $user = Admin::find(Auth::user()->id);
        $userId = $user->id;
        $userType = $user->user_type;
        $userLevel = $user->user_level;
        $userCompany = $user->company_id;
        $userDepartment = $user->department_id;

        $activeStatus = 1; //active
        $liniId = 1;

        $data['dataAdmin'] = DB::table('admins')
        ->select([
            'admins.*',
            DB::raw('(SELECT name FROM department WHERE department.id = admins.department_id) as dept_name')
        ])
        ->where('active',$activeStatus)->where('department_id',$userDepartment)->limit(1)->first();

        $data['dataTeams'] = DB::table('users')
        ->select([
            'users.*',
            DB::raw('(SELECT name FROM users_level WHERE users_level.id = users.user_level AND users_level.department_id = users.department_id OR users_level.role = users.user_level AND users_level.role IS NOT NULL) as user_title')
        ])
        ->where('active',$activeStatus)->where('department_id',$userDepartment)->orderBy('user_level','DESC')->paginate(10);

        return view('admin.all-members-by-department',$data);
    }

    public function editPassword()
    {
        $user = Admin::find(Auth::user()->id);
        $userType = $user->user_type;

        return view('admin.change-password');
    }

    public function changePassword(Request $request)
    {
        $user = Admin::find(Auth::user()->id);
        $userType = $user->user_type;

        if(Hash::check($request['oldPassword'], $user->password))
        {
            $user->password = Hash::make($request['password']);
            $user->update();

            return redirect()->back()->with('success','Pasword Anda telah berhasil diperbarui.');
        }else{
            return redirect()->route('admin.edit.password')->with('error','Pasword lama Anda tidak sesuai.');
        }
    }

    public function getUserList(Request $request)
    {
        $userId = Auth::user()->id;
        $userDepartment = Auth::user()->department_id;

        if ($request->input_param == 'admin') {
            $userDatas = DB::table('admins')->where('is_verified',1)->where('deleted_at',null)->get();
        }elseif($request->input_param == 'user'){
            $userDatas = DB::table('users')->where('is_verified',1)->where('department_id',$userDepartment)->where('deleted_at',null)->get();
        }else{
            $userDatas = DB::table('techs')->where('is_verified',1)->where('deleted_at',null)->get();
        }
        
        return response()->json($userDatas);
    }

    public function getUserListAll(Request $request)
    {
        $userId = Auth::user()->id;
        $userDepartment = Auth::user()->department_id;

        if ($request->input_param == 'admin') {
            $userDatas = DB::table('admins')->where('is_verified',1)->where('deleted_at',null)->get();
        }elseif($request->input_param == 'user'){
            $userDatas = DB::table('users')->where('is_verified',1)->where('deleted_at',null)->get();
        }else{
            $userDatas = DB::table('techs')->where('is_verified',1)->where('deleted_at',null)->get();
        }
        
        return response()->json($userDatas);
    }

    public function troubleshootingDetail($id)
    {
        $publishedTroubleshooting = 4; //all employee

        $data['dataTroubleshooting'] = DB::table('troubleshooting')->where('id',$id)->where('status',$publishedTroubleshooting)->first();

        if (isset($data['dataTroubleshooting'])) {

            //add view
            DB::table('troubleshooting')->where('id',$id)->increment('view',1);

            $data['users'] = DB::table('users')->get();
            $data['admins'] = DB::table('admins')->get();
            $data['techs'] = DB::table('techs')->get();

            ###comments data
                $data['dataComments'] = DB::table('troubleshooting_comments as tc')
                ->select([
                    'tc.*',
                    DB::raw('(SELECT COUNT(*) FROM troubleshooting_comments_files WHERE troubleshooting_comments_files.comment_id = tc.id) countFiles')
                ])
                ->where('troubles_id', $id)
                ->orderBy('id','DESC')
                ->get();

                //comment count
                $data['countComments'] = DB::table('troubleshooting_comments')
                    ->where('troubles_id', $id)
                    ->count();
                
                //task comment files
                $data['commentFiles'] = DB::table('troubleshooting_comments_files')->get();
            ###comments data end

            return view('admin.troubleshooting-detail', $data);
        }

        return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
    }
}
