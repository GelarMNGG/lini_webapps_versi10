<?php

namespace App\Http\Controllers\Tech\Project;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;

class TechFileProjectReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:tech');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman report project.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman report project.');
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

        $projectId = $request->project_id;
        $taskId = $request->task_id;
        $prtsId = $request->prts_id;
        $prfId = $request->prf_id;
        $subcatId = $request->subcat_id;

        //template check
            $templateCheck = DB::table('project_report_template_selected')->where('id',$prtsId)->first();
            if (!isset($templateCheck)) {
                return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman upload file template Project Report');
            }
        //subcat name
            if (isset($request->scid)) {
                $data['subcatdata'] = DB::table('project_report_subcategory_customized')->select('name','id')->where('id',$subcatId)->first();
            }else{
                $data['subcatdata'] = DB::table('project_report_subcategory')->select('name','id')->where('id',$subcatId)->first();
            }

        //privilege check
            $privilegeCheck = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
            ])
            ->where('project_id',$projectId)->where('id',$taskId)->where('tech_id',$userId)->where('deleted_at',null)->first();
        
        if (isset($privilegeCheck)) {
            $request->validate([
                'filled' => 'mimes:jpeg,jpg,png,doc,docx,xls,xlsx,pdf|max:9216',
            ]);
    
            //file handler
            $fileName = null;
            $destinationPath = public_path().'/files/projects/report/template_files/';
            
            // Retrieving An Uploaded File
            $file = $request->file('filled');
            if (!empty($file)) {
                $extension = $file->getClientOriginalExtension();
                $fileName = 'tech_'.time().'_'.$file->getClientOriginalName();
        
                // Moving An Uploaded File
                $request->file('filled')->move($destinationPath, $fileName);

                //delete previous image
                    $filecheck = DB::table('project_report_file as prf')->select('filled as image')->where('id',$prfId)->first();
                    //redirect
                        if (isset($filecheck)) {
                            //file check end
                            $oldImage = $filecheck->image;
            
                            if($oldImage !== 'default.png'){
                                $image_path = $destinationPath.$oldImage;
                                if(File::exists($image_path)) {
                                    File::delete($image_path);
                                }
                            }
                        }
            }
    
            //custom setting to support file upload
            $data = $request->only(['filled','status']);
            $data['tech_id'] = $userId;
            $data['submitted_at'] = Carbon::now()->format('Y-m-d H:i:s');

            if (!empty($fileName)) {
                $data['filled'] = $fileName;
            }
            DB::table('project_report_file')->where('id',$prfId)->update($data);
            return redirect()->back()->with('success','File berhasil diupload.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman report project.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman report project.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman report project.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman report project.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman report project.');
    }
}
