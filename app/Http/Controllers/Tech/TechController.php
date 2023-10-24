<?php

namespace App\Http\Controllers\Tech;

use App\Tech;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;
use DB;

class TechController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:tech');
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
        $userDepartment = 1;

        $manualType = 2; //tipe manual book
        $published = 4; //corporate
        $publishedTroubleshooting = 4;

        //check data diri
        $dataDiriCheck = DB::table('proc_tech_personal_data')->where('tech_id',$userId)->count();
        $testPsychologyCheck = DB::table('proc_test_psychology_results')->where('tech_id',$userId)->count();
        $testAssessmentCheck = DB::table('proc_test_assessment_results')->where('tech_id',$userId)->count();
        if ($dataDiriCheck < 1) {
            return redirect()->route('tech-input-data-diri.index');
        }
        if ($testPsychologyCheck < 1 || $testAssessmentCheck < 1) {
            return redirect()->route('tech-test-training.index');
        }

        $data['dataProjects'] = DB::table('projects_task as pt')
        ->select([
            'pt.*',
            DB::raw('(SELECT COUNT(*) FROM purchase_requisition WHERE purchase_requisition.project_id = pt.project_id) as prCount'),
            DB::raw('(SELECT id FROM purchase_requisition WHERE purchase_requisition.project_id = pt.project_id) as pr_id')
        ])
        ->where('tech_id',$userId)->limit(5)->get();

        $data['currentTask'] = DB::table('projects_task')->where('tech_id',$userId)->orderBy('id','DESC')->first();

        $data['projectStatus'] = DB::table('projects_task_status')->get();
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

        return view('tech.index', $data);
    }

    public function editPassword()
    {
        $user = Tech::find(Auth::user()->id);
        $userType = $user->user_type;

        return view('tech.change-password');
    }

    public function changePassword(Request $request)
    {
        $user = Tech::find(Auth::user()->id);
        $userType = $user->user_type;

        if(Hash::check($request['oldPassword'], $user->password))
        {
            $user->password = Hash::make($request['password']);
            $user->update();

            return redirect()->back()->with('success','Pasword Anda telah berhasil diperbarui.');
        }else{
            return redirect()->route('tech.edit.password')->with('error','Pasword lama Anda tidak sesuai.');
        }
    }

    public function getUserList(Request $request)
    {
        if ($request->input_param == 'tech') {
            $userDatas = DB::table('techs')->where('is_verified',1)->where('deleted_at',null)->get();
        }else{
            $userDatas = DB::table('users')->where('is_verified',1)->where('deleted_at',null)->get();
        }
        
        return response()->json($userDatas);
    }

    public function paymentProcedure()
    {
        return view('tech.payment-procedure');
    }

    public function manualDetail($id)
    {
        $manualType = 2; //tipe manual book
        $published = 1;

        $data['blog'] = DB::table('blogs')->where('id',$id)->where('type',$manualType)->where('status',$published)->first();

        if (isset($data['blog'])) {

            //add view
            DB::table('blogs')->where('id',$id)->increment('views',1);

            $data['users'] = DB::table('users')->get();
            $data['admins'] = DB::table('admins')->get();

            return view('tech.manual-detail', $data);
        }

        return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
    }

    public function troubleshootingDetail($id)
    {
        $publishedTroubleshooting = 4;

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

            return view('tech.troubleshooting-detail', $data);
        }

        return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
    }

    public function getCityList(Request $request)
    {
        $code = $request->code;
        $cities = DB::table('list_of_cities')->where('code',$code)->get();
        
        return response()->json($cities);
    }
}
