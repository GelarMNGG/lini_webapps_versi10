<?php

namespace App\Http\Controllers\User\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Auth;
use DB;

class UserProjectReportFileController extends Controller
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
        return redirect()->back()->with('alert-danger','Terjadi kesalahan jaringan, silahkan mencoba beberapa saat lagi.');
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

        $pm = 3; //pm
        $qcDocument = 4; //qc document
        $projectDepartment = 1; //project department

        $projectId = $request->pid;
        $taskId = $request->tid;
        $prtsId = $request->prts;
        if (isset($request->scid)) {
            $subcatId = $request->scid;
        }else{
            $subcatId = $request->sid;
        }

        //firstcheck
            if ($userDepartment != $projectDepartment) {
                return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman upload file template Project Report');
            }
        //check priviledge
            $filecheck = DB::table('project_report_file as prf')
            ->select([
                'prf.*',
                DB::raw('(SELECT name FROM project_report_subcategory WHERE project_report_subcategory.id = prf.subcat_id) as subcat_name'),
                DB::raw('(SELECT name FROM project_report_subcategory_customized WHERE project_report_subcategory_customized.id = prf.subcatcustom_id) as subcatcus_name'),
            ])
            ->where('project_id',$projectId)->where('task_id',$taskId)->where('prts_id',$prtsId)->where('publisher_id',$userId)->where('publisher_type',$userType)->first();
            //redirect
                if (isset($filecheck)) {
                    return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman upload file template Project Report');
                }
            //template check
                $templateCheck = DB::table('project_report_template_selected')->where('project_id',$projectId)->where('task_id',$taskId)->where('id',$prtsId)->where('publisher_id',$userId)->where('publisher_type',$userType)->first();
                if (!isset($templateCheck)) {
                    return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman upload file template Project Report');
                }
            //subcat name
                if (isset($request->scid)) {
                    $data['subcatdata'] = DB::table('project_report_subcategory_customized')->select('name','id')->where('id',$subcatId)->first();
                }else{
                    $data['subcatdata'] = DB::table('project_report_subcategory')->select('name','id')->where('id',$subcatId)->first();
                }

            if ($userLevel == $qcDocument) {
                $privilegeCheck = DB::table('projects_task as pt')
                ->select([
                    'pt.*',
                    DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
                ])
                ->where('project_id',$projectId)->where('id',$taskId)->where('qcd_id',$userId)->where('deleted_at',null)->first();
            }else{
                $pmCheck = DB::table('projects')->where('id',$projectId)->where('pm_id',$userId)->where('deleted_at',null)->count();
                //privilege
                    if ($pmCheck > 0) {
                        $privilegeCheck = DB::table('projects_task as pt')
                        ->select([
                            'pt.*',
                            DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name'),
                        ])
                        ->where('project_id',$projectId)->where('id',$taskId)->where('deleted_at',null)->first();
                    }else{
                        $privilegeCheck = NULL;
                    }
            }
        
        if (isset($privilegeCheck)) {
            $data['projectTemplate'] = $templateCheck;
            $data['projectTask'] = $privilegeCheck;
            return view('user.project.project-template-file.create',$data);
        }
        return redirect()->back()->with('alert-danger','Terjadi kesalahan jaringan, silahkan mencoba beberapa saat lagi.');
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

        $pm = 3; //pm
        $qcDocument = 4; //qc document
        $projectDepartment = 1; //project department

        $projectId = $request->project_id;
        $taskId = $request->task_id;
        $prtsId = $request->prts_id;
        $subcatId = $request->subcat_id;

        //firstcheck
            if ($userDepartment != $projectDepartment) {
                return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman upload file template Project Report');
            }
        //check priviledge
            $filecheck = DB::table('project_report_file as prf')
            ->select([
                'prf.*',
                DB::raw('(SELECT name FROM project_report_subcategory WHERE project_report_subcategory.id = prf.subcat_id) as subcat_name'),
                DB::raw('(SELECT name FROM project_report_subcategory_customized WHERE project_report_subcategory_customized.id = prf.subcatcustom_id) as subcatcus_name'),
            ])
            ->where('project_id',$projectId)->where('task_id',$taskId)->where('prts_id',$prtsId)->where('publisher_id',$userId)->where('publisher_type',$userType)->first();
            //redirect
                if (isset($filecheck)) {
                    return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman upload file template Project Report');
                }
            //template check
                $templateCheck = DB::table('project_report_template_selected')->where('project_id',$projectId)->where('task_id',$taskId)->where('id',$prtsId)->where('publisher_id',$userId)->where('publisher_type',$userType)->first();
                if (!isset($templateCheck)) {
                    return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman upload file template Project Report');
                }
            //subcat name
                if (isset($request->scid)) {
                    $data['subcatdata'] = DB::table('project_report_subcategory_customized')->select('name','id')->where('id',$subcatId)->first();
                }else{
                    $data['subcatdata'] = DB::table('project_report_subcategory')->select('name','id')->where('id',$subcatId)->first();
                }

            if ($userLevel == $qcDocument) {
                $privilegeCheck = DB::table('projects_task as pt')
                ->select([
                    'pt.*',
                    DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
                ])
                ->where('project_id',$projectId)->where('id',$taskId)->where('qcd_id',$userId)->where('deleted_at',null)->first();
            }else{
                $pmCheck = DB::table('projects')->where('id',$projectId)->where('pm_id',$userId)->where('deleted_at',null)->count();
                //privilege
                    if ($pmCheck > 0) {
                        $privilegeCheck = DB::table('projects_task as pt')
                        ->select([
                            'pt.*',
                            DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name'),
                        ])
                        ->where('project_id',$projectId)->where('id',$taskId)->where('deleted_at',null)->first();
                    }else{
                        $privilegeCheck = NULL;
                    }
            }
        
        if (isset($privilegeCheck)) {
            $request->validate([
                'name' => 'mimes:jpeg,jpg,png,doc,docx,xls,xlsx,pdf|max:9216',
            ]);
    
            //file handler
            $fileName = null;
            $destinationPath = public_path().'/files/projects/report/template_files/';
            
            // Retrieving An Uploaded File
            $file = $request->file('name');
            if (!empty($file)) {
                $extension = $file->getClientOriginalExtension();
                $fileName = time().'_'.$file->getClientOriginalName();
        
                // Moving An Uploaded File
                $request->file('name')->move($destinationPath, $fileName);
            }
    
            //custom setting to support file upload
            $data = $request->except(['_token','submit']);
            $data['publisher_id'] = $userId;
            $data['publisher_type'] = $userType;

            if (!empty($fileName)) {
                $data['name'] = $fileName;
            }
            DB::table('project_report_file')->insert($data);
            return redirect()->route('user-projects-template.edit',$prtsId)->with('success','File berhasil diupload.');
        }

        return redirect()->back()->with('alert-danger','Terjadi kesalahan jaringan, silahkan mencoba beberapa saat lagi.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Terjadi kesalahan jaringan, silahkan mencoba beberapa saat lagi.');
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

        $pm = 3; //pm
        $qcDocument = 4; //qc document
        $projectDepartment = 1; //project department

        //firstcheck
            if ($userDepartment != $projectDepartment) {
                return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman upload file template Project Report');
            }
        //check priviledge
            $filecheck = DB::table('project_report_file as prf')
            ->select([
                'prf.*',
                DB::raw('(SELECT name FROM project_report_subcategory WHERE project_report_subcategory.id = prf.subcat_id) as subcat_name'),
                DB::raw('(SELECT name FROM project_report_subcategory_customized WHERE project_report_subcategory_customized.id = prf.subcatcustom_id) as subcatcus_name'),
            ])
            ->where('id',$id)->where('publisher_id',$userId)->where('publisher_type',$userType)->first();
            //redirect
                if (!isset($filecheck)) {
                    return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman upload file template Project Reports');
                }
            //project id & task id
            $projectId = $filecheck->project_id;
            $taskId = $filecheck->task_id;

            if ($userLevel == $qcDocument) {
                $privilegeCheck = DB::table('projects_task as pt')
                ->select([
                    'pt.*',
                    DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
                ])
                ->where('project_id',$projectId)->where('id',$taskId)->where('qcd_id',$userId)->where('deleted_at',null)->first();
            }else{
                $pmCheck = DB::table('projects')->where('id',$projectId)->where('pm_id',$userId)->where('deleted_at',null)->count();
                //privilege
                    if ($pmCheck > 0) {
                        $privilegeCheck = DB::table('projects_task as pt')
                        ->select([
                            'pt.*',
                            DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name'),
                        ])
                        ->where('project_id',$projectId)->where('id',$taskId)->where('deleted_at',null)->first();
                    }else{
                        $privilegeCheck = NULL;
                    }
            }
        
        if (isset($privilegeCheck)) {
            //data
                $data['projectFile'] = $filecheck;
                $data['projectTask'] = $privilegeCheck;
            //return view
                return view('user.project.project-template-file.edit',$data);
        }
        return redirect()->back()->with('alert-danger','Terjadi kesalahan jaringan, silahkan mencoba beberapa saat lagi.');
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

        $pm = 3; //pm
        $qcDocument = 4; //qc document
        $projectDepartment = 1; //project department

        //firstcheck
            if ($userDepartment != $projectDepartment) {
                return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman upload file template Project Report');
            }

        $errorMessage = [
            'name.required' => 'Field ini tidak boleh kosong. File yang diijinkan: jpeg, jpg, png, pdf, docx, xlsx.',
        ];
        $validation = Validator::make($request->all(),[
            'name' => 'mimes:jpeg,jpg,png,pdf,docx,xlsx|max:4096', //2048,4096
        ],$errorMessage);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }
        //file handler
        $fileName = null;
        $destinationPath = public_path().'/files/projects/report/template_files/';
        
        // Retrieving An Uploaded File
        $file = $request->file('name');
        if (!empty($file)) {
            $extension = $file->getClientOriginalExtension();
            $fileName = time().'_'.$file->getClientOriginalName();
    
            // Moving An Uploaded File
            $file->move($destinationPath, $fileName);

            //delete previous image
            $dataImage = DB::table('project_report_file')->select('name as image')->where('id', $id)->first();
            $oldImage = $dataImage->image;

            if($oldImage !== 'default.png'){
                $image_path = $destinationPath.$oldImage;
                if(File::exists($image_path)) {
                    File::delete($image_path);
                }
            }
        }
        //custom setting to support file upload
            $data = $request->except(['_token','_method','submit']);
            if (!empty($fileName)) {
                $data['name'] = $fileName;
            }
        //insert to database
            DB::table('project_report_file')->where('id',$id)->update($data);
        //redirect page
            $postData = DB::table('project_report_file')->select('prts_id')->where('id',$id)->first();
            $prtsId = $postData->prts_id;
        return redirect()->route('user-projects-template.edit',$prtsId)->with('success','Data file berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Terjadi kesalahan jaringan, silahkan mencoba beberapa saat lagi.');
    }
}
