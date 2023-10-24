<?php

namespace App\Http\Controllers\Api\Tech;

use App\Http\Controllers\Api\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use DB;

class ProjectsReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:tech-api', ['except' => ['login']]);
    }

    public function all(Request $request)
    {
        $user = $this->authUser();
        
        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }
        
        if (!isset($request->project_id)) {
            return response()->json(['error' => 'Maaf, project id tidak tersedia.']);
        } 
        
        if (!isset($request->task_id)) {
            return response()->json(['error' => 'Maaf, task id tidak tersedia.']);
        }
        
        $techId = $user->id;
        $taskId = $request->task_id;
        $projectId = $request->project_id;

        $data['reportProject'] = DB::table('projects_task as pt')
        ->select([
            'id as task_id',
            'name as task_name',
            DB::raw('(SELECT id FROM projects WHERE projects.id = pt.project_id) as project_id'),
            DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name'),
        ])
        ->where('id',$taskId)
        ->where('tech_id',$techId)
        ->get();

        //template data
        $data['projectTemplateDatas'] = DB::table('project_report_template_selected as prtss')
        ->select([
            'prtss.id',
            'prtss.name',
            DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = prtss.task_id) as task_name'),
            'prtss.template_id',
            DB::raw('(SELECT type FROM project_report_category as prc WHERE prc.id = prtss.template_id) as template_type')
        ])
        ->where('project_id',$projectId)
        ->where('task_id',$taskId)
        ->get();
        
        if (isset($data['projectTemplateDatas']) < 1) {
            return response()->json('alert-danger','Belum ada template laporan untuk proyek ini.');
        }
        return response()->json($data);
    }

    public function create(Request $request)
    {
        $user = $this->authUser();
        
        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }
        
        if (!isset($request->project_id)) {
            return response()->json(['error' => 'Maaf, project id tidak tersedia.']);
        } 
        
        if (!isset($request->task_id)) {
            return response()->json(['error' => 'Maaf, task id tidak tersedia.']);
        }
        if (!isset($request->template_id)) {
            return response()->json(['error' => 'Maaf, template id tidak tersedia.']);
        }
        if (!isset($request->subcat_id) && !isset($request->subcatcustom_id)) {
            return response()->json(['error' => 'Maaf, subcat/subcatcustom id tidak tersedia.']);
        }
        
        $techId = $user->id;
        $taskId = $request->task_id;
        $projectId = $request->project_id;
        $templateId = $request->template_id;

        $catStatus = 1; //active category
        $subcatStatus = 1; //active subcategory
        $commentStatus = 0; //accessible for technician level user

        //template type
            $templateImage = 1;
            $templateText = 2;
            $templateFile = 6;

        //check priviledge
        $priviledgeCheck = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('tech_id',$techId)->where('deleted_at',null)->count();
        
        if ($priviledgeCheck > 0) {
            $subcatRequestData = $request->subcat_id;
            if ($subcatRequestData !== null) {
                $subcatId = $request->subcat_id;
                $subcatName = 'subcat_id';
            }else{
                $subcatId = $request->subcatcustom_id;
                $subcatName = 'subcatcustom_id';
            }
            ### template data ###
                $data['projectTemplate'] = DB::table('project_report_template_selected as prtss')
                ->select([
                    'prtss.project_id',
                    'prtss.task_id',
                    'prtss.template_id',
                    DB::raw('(SELECT name FROM projects WHERE projects.id = prtss.project_id) as project_name'),
                    DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = prtss.task_id) as task_name'),
                    //DB::raw('(SELECT status FROM projects WHERE projects.id = prtss.project_id) as project_status'),
                    DB::raw('(SELECT name FROM project_report_category WHERE project_report_category.id = prtss.template_id) as name'),
                    //subcategory
                        DB::raw('(SELECT name FROM project_report_subcategory as prsub WHERE prsub.id = '.$subcatId.') as subcat_name'),
                    //tempate folder
                        DB::raw('(SELECT procat_id FROM projects WHERE projects.id = prtss.project_id) as procat_id'),
                        DB::raw('(SELECT folder FROM projects_category WHERE projects_category.id = procat_id) as folder_name'),
                    //tempate type
                        DB::raw('(SELECT type FROM project_report_category WHERE project_report_category.id = prtss.template_id) as type'),
                ])
                ->where('template_id',$templateId)
                ->where('project_id',$projectId)
                ->where('task_id',$taskId)
                ->where('deleted_at',null)->first();
            ### template data end ###

            //third check
                if (!$data['projectTemplate']) {
                    return response()->json('message','Subcategory yang Anda tuju tidak tersedia pada template ini.');
                }
            
            //check template type and redirect
                if ($data['projectTemplate']->type == $templateImage) {
                    ### project pictures ###
                        if ($subcatRequestData !== null) {
                            $data['dataProjectPictures'] = DB::table('project_report_images')
                            ->select([
                                'id',
                                'image',
                                'selected_image',
                                'submitted_at',
                                'approved_at',
                                'approved_by_pm_at'
                            ])
                            ->where('project_id',$projectId)
                            ->where('task_id',$taskId)
                            ->where('template_id',$templateId)
                            ->where('subcat_id',$subcatId)
                            ->where('publisher_id',$techId)
                            ->get();
                        }else{
                            $data['dataProjectPictures'] = DB::table('project_report_images')
                            ->select([
                                'id',
                                'image',
                                'selected_image',
                                'submitted_at',
                                'approved_at',
                                'approved_by_pm_at'
                            ])
                            ->where('project_id',$projectId)
                            ->where('task_id',$taskId)
                            ->where('template_id',$templateId)
                            ->where('subcatcustom_id',$subcatId)
                            ->where('publisher_id',$techId)
                            ->get();
                        }
                    
                    ### comments ###
                        if ($subcatRequestData !== null) {
                            $data['dataComments'] = DB::table('project_report_images_comments as prtc')->where('status',$commentStatus)
                            ->select([
                                'comment',
                                'date',
                                //tech comments
                                    DB::raw('(SELECT firstname FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_firstname'),
                                    DB::raw('(SELECT lastname FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_latname'),
                                    DB::raw('(SELECT title FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_title'),
                                //users comments
                                    DB::raw('(SELECT firstname FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_firstname'),
                                    DB::raw('(SELECT lastname FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_latname'),
                                    DB::raw('(SELECT title FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_title'),
                            ])
                            //->where('pri_id',$data['dataProjectPicturesStatus']->id)
                            ->where('project_id',$projectId)
                            ->where('task_id',$taskId)
                            ->where('subcat_id',$subcatId)
                            ->orderBy('date','DESC')
                            ->get();
                        }else{
                            $data['dataComments'] = DB::table('project_report_images_comments as prtc')->where('status',$commentStatus)
                            ->select([
                                'comment',
                                'date',
                                //tech comments
                                    DB::raw('(SELECT firstname FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_firstname'),
                                    DB::raw('(SELECT lastname FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_latname'),
                                    DB::raw('(SELECT title FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_title'),
                                //users comments
                                    DB::raw('(SELECT firstname FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_firstname'),
                                    DB::raw('(SELECT lastname FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_latname'),
                                    DB::raw('(SELECT title FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_title'),
                            ])
                            //->where('pri_id',$data['dataProjectPicturesStatus']->id)
                            ->where('project_id',$projectId)
                            ->where('task_id',$taskId)
                            ->where('subcatcustom_id',$subcatId)
                            ->orderBy('date','DESC')
                            ->get();
                        }
                    ### comments end ###
                    return response()->json($data);
                }elseif(($data['projectTemplate']->type == $templateText)){
                    ### project text ###
                        if ($subcatRequestData !== null) {
                            $data['dataProjectText'] = DB::table('project_report_text')
                            ->select([
                                'id',
                                'text',
                                'submitted_at',
                                'approved_at',
                                'approved_by_pm_at'
                            ])
                            ->where('project_id',$projectId)
                            ->where('task_id',$taskId)
                            ->where('template_id',$templateId)
                            ->where('subcat_id',$subcatId)
                            ->where('publisher_id',$techId)
                            ->first();
                        }else{
                            $data['dataProjectText'] = DB::table('project_report_text')
                            ->select([
                                'id',
                                'text',
                                'submitted_at',
                                'approved_at',
                                'approved_by_pm_at'
                            ])
                            ->where('project_id',$projectId)
                            ->where('task_id',$taskId)
                            ->where('template_id',$templateId)
                            ->where('subcatcustom_id',$subcatId)
                            ->where('publisher_id',$techId)
                            ->first();
                        }
                    ### project text end ###
                    ### comments ###
                        if ($subcatRequestData !== null) {
                            $data['dataComments'] = DB::table('project_report_text_comments as prtc')->where('status',$commentStatus)
                            ->select([
                                'comment',
                                'date',
                                //tech comments
                                    DB::raw('(SELECT firstname FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_firstname'),
                                    DB::raw('(SELECT lastname FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_latname'),
                                    DB::raw('(SELECT title FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_title'),
                                //users comments
                                    DB::raw('(SELECT firstname FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_firstname'),
                                    DB::raw('(SELECT lastname FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_latname'),
                                    DB::raw('(SELECT title FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_title'),
                            ])
                            //->where('prt_id',$data['dataProjectTextStatus']->id)
                            ->where('project_id',$projectId)
                            ->where('task_id',$taskId)
                            ->where('subcat_id',$subcatId)
                            ->orderBy('date','DESC')
                            ->get();
                        }else{
                            $data['dataComments'] = DB::table('project_report_text_comments')->where('status',$commentStatus)
                            ->select([
                                'comment',
                                'date',
                                //tech comments
                                    DB::raw('(SELECT firstname FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_firstname'),
                                    DB::raw('(SELECT lastname FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_latname'),
                                    DB::raw('(SELECT title FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_title'),
                                //users comments
                                    DB::raw('(SELECT firstname FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_firstname'),
                                    DB::raw('(SELECT lastname FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_latname'),
                                    DB::raw('(SELECT title FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_title'),
                            ])
                            //->where('prt_id',$data['dataProjectTextStatus']->id)
                            ->where('project_id',$projectId)
                            ->where('task_id',$taskId)
                            ->where('subcatcustom_id',$subcatId)
                            ->orderBy('date','DESC')
                            ->get();
                        }
                    ### comments end ###

                    return response()->json($data);
                }
            return response()->json(['message' => 'Type template tidak tersedia, data tidak berhasil ditampilkan.']);
        }
        return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
    }

    public function storeAll(Request $request)
    {        
        $user = $this->authUser();

        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }

        if (!isset($request->project_id)) {
            return response()->json(['error' => 'Maaf, project id tidak tersedia.']);
        } 

        if (!isset($request->task_id)) {
            return response()->json(['error' => 'Maaf, task id tidak tersedia.']);
        }

        if (!isset($request->template_id)) {
            return response()->json(['error' => 'Maaf, template id tidak tersedia.']);
        }

        $techId = $user->id;
        $userType = $user->user_type;
        $userDepartment = 1; //project department

        $taskId = $request->task_id;
        $projectId = $request->project_id;
        $templateId = $request->template_id; //category

        //template type
            $templateImage = 1;
            $templateText = 2;
            $templateFile = 6;

        if (!isset($request->subcat_id)) {
            return response()->json(['error' => 'Maaf, subcat id tidak tersedia.']);
        }
        $subcatRequestData = $request->subcat_id;

        if ($subcatRequestData !== null) {
            $subcatId = $request->subcat_id;
        }else{
            $subcatId = $request->subcatcustom_id;
        }

        $catStatus = 1; //active category
        $subcatStatus = 1; //active subcategory

        //check priviledge
            $firstCheck = DB::table('projects_task as pt')
            ->select([
                'pt.project_id',
                DB::raw('(SELECT procat_id FROM projects WHERE projects.id = pt.project_id) as procat_id'),
                DB::raw('(SELECT folder FROM projects_category WHERE projects_category.id = procat_id) as procat_name'),
            ])
            ->where('project_id',$projectId)->where('tech_id',$techId)->first();
        //secondcheck
            $secondcheck = DB::table('project_report_category')->select('type')->where('id',$templateId)->first();
            $currentReportType = $secondcheck->type;

        if (isset($firstCheck)) {
            $data = $request->only([
                'project_id',
                'task_id',
                'template_id',
                'subcat_id',
                'subcatcustom_id',
                'status',
            ]);
            //folder name
                $folderName = $firstCheck->procat_name;
                if ($folderName == null) {
                    $folderName = 'others';
                }
            if ($currentReportType == $templateImage) {
                if (!isset($request->image)) {
                    return response()->json(['error' => 'Maaf, Anda belum upload gambar.']);
                }
                //image validation
                    $request->validate([
                        'image' => 'mimes:jpeg,jpg,png|max:9216',
                    ]);
                //file handler
                    $fileName = null;
                    $destinationPath = public_path().'/img/projects/'.$folderName.'/';
                // Retrieving An Uploaded File
                    $file = $request->file('image');
                    if (!empty($file)) {
                        $extension = $file->getClientOriginalExtension();
                        //custom filename
                            $projectData = DB::table('projects')->select('name')->where('id',$projectId)->first();
                            $taskData = DB::table('projects_task')->select('name')->where('id',$taskId)->first();
                            $projectName = Str::slug($projectData->name,'-');
                            $taskName = Str::slug($taskData->name,'-');
                            $fileName = $projectName.'_'.$taskName.'_'.time().'_'.$file->getClientOriginalName();
                        // Moving An Uploaded File
                            $request->file('image')->move($destinationPath, $fileName);
                    }
                //custom setting to support file upload
                    $data['publisher_id'] = $techId;
                    $data['publisher_type'] = $userType;
                    $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
                //reseting admin approval
                    $reset['approved_at'] = null;
                    if ($subcatRequestData != null) {
                        DB::table('project_report_images')
                        ->where('project_id',$projectId)
                        ->where('task_id',$taskId)
                        ->where('template_id',$templateId)
                        ->where('subcat_id',$subcatId)
                        ->where('publisher_id',$techId)
                        ->update($reset);
                    }else{
                        DB::table('project_report_images')
                        ->where('project_id',$projectId)
                        ->where('task_id',$taskId)
                        ->where('template_id',$templateId)
                        ->where('subcatcustom_id',$subcatId)
                        ->where('publisher_id',$techId)
                        ->update($reset);
                    }
                    if (!empty($fileName)) {
                        $data['image'] = $fileName;
                    }
                //insert to database
                    DB::table('project_report_images')->insert($data);

                return response()->json(['message' => 'Data berhasil disimpan.']);
            }elseif($currentReportType == $templateText){
                if (!isset($request->text)) {
                    return response()->json(['error' => 'Maaf, Anda belum memasukkan teks.']);
                }
                //custom setting to support file upload
                    $data['text'] = $request->text;
                    $data['publisher_id'] = $techId;
                    $data['publisher_type'] = $userType;
                    $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
                //insert to database
                    DB::table('project_report_text')->insert($data);

                return response()->json(['message' => 'Data berhasil disimpan.']);
            }elseif($currentReportType == $templateFile){
                if (!isset($request->filled)) {
                    return response()->json(['error' => 'Maaf, Anda belum mengupload file.']);
                }
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
                        $filecheck = DB::table('project_report_file')->select(['id','filled as image'])->where('project_id',$projectId)->where('task_id',$taskId)->first();

                        $prfId = $filecheck->id;
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
                    $file = $request->only([
                        'project_id',
                        'task_id',
                        'status',
                        'subcat_id',
                        'subcatcustom_id',
                    ]);
                    $file['prts_id'] = $templateId;
                    $file['tech_id'] = $techId;
                    $file['publisher_id'] = $techId;
                    $file['publisher_type'] = $userType;
                    $file['submitted_at'] = Carbon::now()->format('Y-m-d H:i:s');
        
                    if (!empty($fileName)) {
                        $file['filled'] = $fileName;
                    }
                //insert to database
                DB::table('project_report_file')->where('id',$prfId)->update($file);

                return response()->json(['message' => 'Data berhasil disimpan.']);
            }
            return response()->json(['message' => 'Type template tidak tersedia, data tidak berhasil disimpan.']);
        }
        return response()->json(['error' => 'Terjadi kesalahan koneksi, data tidak berhasil disimpan.']);
    }

    public function storeImage(Request $request)
    {        
        $user = $this->authUser();

        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }

        if (!isset($request->project_id)) {
            return response()->json(['error' => 'Maaf, project id tidak tersedia.']);
        } 

        if (!isset($request->task_id)) {
            return response()->json(['error' => 'Maaf, task id tidak tersedia.']);
        }

        if (!isset($request->template_id)) {
            return response()->json(['error' => 'Maaf, template id tidak tersedia.']);
        }

        $techId = $user->id;
        $userType = $user->user_type;
        $userDepartment = 1; //project department

        $taskId = $request->task_id;
        $projectId = $request->project_id;
        $templateId = $request->template_id; //category

        //template type
            $templateImage = 1;
            $templateText = 2;
            $templateFile = 6;

        if (!isset($request->subcat_id)) {
            return response()->json(['error' => 'Maaf, subcat id tidak tersedia.']);
        }
        $subcatRequestData = $request->subcat_id;

        if ($subcatRequestData !== null) {
            $subcatId = $request->subcat_id;
        }else{
            $subcatId = $request->subcatcustom_id;
        }

        $catStatus = 1; //active category
        $subcatStatus = 1; //active subcategory

        //check priviledge
            $firstCheck = DB::table('projects_task as pt')
            ->select([
                'pt.project_id',
                DB::raw('(SELECT procat_id FROM projects WHERE projects.id = pt.project_id) as procat_id'),
                DB::raw('(SELECT folder FROM projects_category WHERE projects_category.id = procat_id) as procat_name'),
            ])
            ->where('project_id',$projectId)->where('tech_id',$techId)->first();
        //secondcheck
            $secondcheck = DB::table('project_report_category')->select('type')->where('id',$templateId)->first();
            $currentReportType = $secondcheck->type;

        if (isset($firstCheck)) {
            $data = $request->only([
                'project_id',
                'task_id',
                'template_id',
                'subcat_id',
                'subcatcustom_id',
                'status',
            ]);
            //folder name
                $folderName = $firstCheck->procat_name;
                if ($folderName == null) {
                    $folderName = 'others';
                }
            if ($currentReportType == $templateImage) {
                if (!isset($request->image)) {
                    return response()->json(['error' => 'Maaf, Anda belum upload gambar.']);
                }
                // image validation
                    $request->validate([
                        'image' => 'mimes:jpeg,jpg,png|max:9216',
                    ]);
                //file handler
                    $fileName = null;
                    $destinationPath = public_path().'/img/projects/'.$folderName.'/';
                // Retrieving An Uploaded File
                    $file = $request->file('image');
                    if (!empty($file)) {
                        $extension = $file->getClientOriginalExtension();
                        //custom filename
                            $projectData = DB::table('projects')->select('name')->where('id',$projectId)->first();
                            $taskData = DB::table('projects_task')->select('name')->where('id',$taskId)->first();
                            $projectName = Str::slug($projectData->name,'-');
                            $taskName = Str::slug($taskData->name,'-');
                            $fileName = $projectName.'_'.$taskName.'_'.time().'_'.$file->getClientOriginalName();
                        // Moving An Uploaded File
                            $request->file('image')->move($destinationPath, $fileName);
                    }
                //custom setting to support file upload
                    $data['publisher_id'] = $techId;
                    $data['publisher_type'] = $userType;
                    $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
                //reseting admin approval
                    $reset['approved_at'] = null;
                    if ($subcatRequestData != null) {
                        DB::table('project_report_images')
                        ->where('project_id',$projectId)
                        ->where('task_id',$taskId)
                        ->where('template_id',$templateId)
                        ->where('subcat_id',$subcatId)
                        ->where('publisher_id',$techId)
                        ->update($reset);
                    }else{
                        DB::table('project_report_images')
                        ->where('project_id',$projectId)
                        ->where('task_id',$taskId)
                        ->where('template_id',$templateId)
                        ->where('subcatcustom_id',$subcatId)
                        ->where('publisher_id',$techId)
                        ->update($reset);
                    }
                    $fileName = $request->input('image');

                    if (!empty($fileName)) {
                        $data['image'] = $fileName;
                    }
                //insert to database
                    DB::table('project_report_images')->insert($data);

                return response()->json(['message' => 'Data berhasil disimpan.']);
            }
            return response()->json(['message' => 'Type template tidak tersedia, data tidak berhasil disimpan.']);
        }
        return response()->json(['error' => 'Terjadi kesalahan koneksi, data tidak berhasil disimpan.']);
    }

    public function storeText(Request $request)
    {        
        $user = $this->authUser();

        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }

        if (!isset($request->project_id)) {
            return response()->json(['error' => 'Maaf, project id tidak tersedia.']);
        } 

        if (!isset($request->task_id)) {
            return response()->json(['error' => 'Maaf, task id tidak tersedia.']);
        }

        if (!isset($request->template_id)) {
            return response()->json(['error' => 'Maaf, template id tidak tersedia.']);
        }

        $techId = $user->id;
        $userType = $user->user_type;
        $userDepartment = 1; //project department

        $taskId = $request->task_id;
        $projectId = $request->project_id;
        $templateId = $request->template_id; //category

        //template type
            $templateImage = 1;
            $templateText = 2;
            $templateFile = 6;

        if (!isset($request->subcat_id)) {
            return response()->json(['error' => 'Maaf, subcat id tidak tersedia.']);
        }
        $subcatRequestData = $request->subcat_id;

        if ($subcatRequestData !== null) {
            $subcatId = $request->subcat_id;
        }else{
            $subcatId = $request->subcatcustom_id;
        }

        $catStatus = 1; //active category
        $subcatStatus = 1; //active subcategory

        //check priviledge
            $firstCheck = DB::table('projects_task as pt')
            ->select([
                'pt.project_id',
                DB::raw('(SELECT procat_id FROM projects WHERE projects.id = pt.project_id) as procat_id'),
                DB::raw('(SELECT folder FROM projects_category WHERE projects_category.id = procat_id) as procat_name'),
            ])
            ->where('project_id',$projectId)->where('tech_id',$techId)->first();
        //secondcheck
            $secondcheck = DB::table('project_report_category')->select('type')->where('id',$templateId)->first();
            $currentReportType = $secondcheck->type;

        if (isset($firstCheck)) {
            $data = $request->only([
                'project_id',
                'task_id',
                'template_id',
                'subcat_id',
                'subcatcustom_id',
                'status',
            ]);
            //folder name
                $folderName = $firstCheck->procat_name;
                if ($folderName == null) {
                    $folderName = 'others';
                }
            if($currentReportType == $templateText){
                if (!isset($request->text)) {
                    return response()->json(['error' => 'Maaf, Anda belum memasukkan teks.']);
                }
                //custom setting to support file upload
                    $data['text'] = $request->text;
                    $data['publisher_id'] = $techId;
                    $data['publisher_type'] = $userType;
                    $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
                //insert to database
                    DB::table('project_report_text')->insert($data);

                return response()->json(['message' => 'Data berhasil disimpan.']);
            }
            return response()->json(['message' => 'Type template tidak tersedia, data tidak berhasil disimpan.']);
        }
        return response()->json(['error' => 'Terjadi kesalahan koneksi, data tidak berhasil disimpan.']);
    }

    public function storeFile(Request $request)
    {        
        $user = $this->authUser();

        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }

        if (!isset($request->project_id)) {
            return response()->json(['error' => 'Maaf, project id tidak tersedia.']);
        } 

        if (!isset($request->task_id)) {
            return response()->json(['error' => 'Maaf, task id tidak tersedia.']);
        }

        if (!isset($request->template_id)) {
            return response()->json(['error' => 'Maaf, template id tidak tersedia.']);
        }

        $techId = $user->id;
        $userType = $user->user_type;
        $userDepartment = 1; //project department

        $taskId = $request->task_id;
        $projectId = $request->project_id;
        $templateId = $request->template_id; //category

        //template type
            $templateImage = 1;
            $templateText = 2;
            $templateFile = 6;

        if (!isset($request->subcat_id)) {
            return response()->json(['error' => 'Maaf, subcat id tidak tersedia.']);
        }
        $subcatRequestData = $request->subcat_id;

        if ($subcatRequestData !== null) {
            $subcatId = $request->subcat_id;
        }else{
            $subcatId = $request->subcatcustom_id;
        }

        $catStatus = 1; //active category
        $subcatStatus = 1; //active subcategory

        //check priviledge
            $firstCheck = DB::table('projects_task as pt')
            ->select([
                'pt.project_id',
                DB::raw('(SELECT procat_id FROM projects WHERE projects.id = pt.project_id) as procat_id'),
                DB::raw('(SELECT folder FROM projects_category WHERE projects_category.id = procat_id) as procat_name'),
            ])
            ->where('project_id',$projectId)->where('tech_id',$techId)->first();
        //secondcheck
            $secondcheck = DB::table('project_report_category')->select('type')->where('id',$templateId)->first();
            $currentReportType = $secondcheck->type;

        if (isset($firstCheck)) {
            $data = $request->only([
                'project_id',
                'task_id',
                'template_id',
                'subcat_id',
                'subcatcustom_id',
                'status',
            ]);
            //folder name
                $folderName = $firstCheck->procat_name;
                if ($folderName == null) {
                    $folderName = 'others';
                }
            if($currentReportType == $templateFile){
                if (!isset($request->filled)) {
                    return response()->json(['error' => 'Maaf, Anda belum mengupload file.']);
                }
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
                        $filecheck = DB::table('project_report_file')->select(['id','filled as image'])->where('project_id',$projectId)->where('task_id',$taskId)->first();

                        $prfId = $filecheck->id;
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
                    $file = $request->only([
                        'project_id',
                        'task_id',
                        'status',
                        'subcat_id',
                        'subcatcustom_id',
                    ]);
                    $file['prts_id'] = $templateId;
                    $file['tech_id'] = $techId;
                    $file['publisher_id'] = $techId;
                    $file['publisher_type'] = $userType;
                    $file['submitted_at'] = Carbon::now()->format('Y-m-d H:i:s');
        
                    if (!empty($fileName)) {
                        $file['filled'] = $fileName;
                    }
                //insert to database
                DB::table('project_report_file')->where('id',$prfId)->update($file);

                return response()->json(['message' => 'Data berhasil disimpan.']);
            }
            return response()->json(['message' => 'Type template tidak tersedia, data tidak berhasil disimpan.']);
        }
        return response()->json(['error' => 'Terjadi kesalahan koneksi, data tidak berhasil disimpan.']);
    }

    public function show(Request $request, $id)
    {
        $user = $this->authUser();

        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }
        
        if (!isset($request->project_id)) {
            return response()->json(['error' => 'Maaf, project id tidak tersedia.']);
        } 
        
        if (!isset($request->task_id)) {
            return response()->json(['error' => 'Maaf, task id tidak tersedia.']);
        }

        $techId = $user->id;
        $taskId = $request->task_id;
        $projectId = $request->project_id;
        $templateId = $request->template_id; //category
        
        $catStatus = 1; //active category
        $subcatStatus = 1; //active subcategory

        //template type
            $templateImage = 1;
            $templateText = 2;
            $templateFile = 6;

        $firstCheck = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('tech_id',$techId)->where('deleted_at',null)->count();

        if ($firstCheck < 1) {
            return response()->json(['error' => 'Anda tidak diijinkan mengakses halaman Report.']);
        }

        ### template data ###
            $dataProjectTemplate = DB::table('project_report_template_selected as prtss')
            ->select([
                'prtss.subcat_id',
                'prtss.subcatcustom_id',
                DB::raw('(SELECT name FROM projects WHERE projects.id = prtss.project_id) as project_name'),
                DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = prtss.task_id) as task_name'),
                // DB::raw('(SELECT status FROM projects WHERE projects.id = prtss.project_id) as project_status'),
                DB::raw('(SELECT name FROM project_report_category WHERE project_report_category.id = prtss.template_id) as name'),
                //tempate type
                    DB::raw('(SELECT type FROM project_report_category WHERE project_report_category.id = prtss.template_id) as type'),
            ])
            ->where('task_id',$taskId)
            ->where('template_id',$id)
            ->where('project_id',$projectId)
            ->first();

            if (!isset($dataProjectTemplate)) {
                return response()->json(['error' => 'Maaf, template belum tersedia untuk proyek ini.']);
            }
            $prtssId = $id;
            $prtssName = $dataProjectTemplate->name;
            $prtssType = $dataProjectTemplate->type;
            $prtssTaskName = $dataProjectTemplate->task_name;
            $prtssProjectName = $dataProjectTemplate->project_name;
            $data['projectTemplate'] = [
                'project_name' => $prtssProjectName,
                'task_name' => $prtssTaskName,
                'template_name' => $prtssName,
                'template_type' => $prtssType,
                'template_id' => $prtssId,
            ];
        ### template data end ###
       
        ### sub category ###
                //data selected subcat
                if (isset($dataProjectTemplate->subcat_id)) {
                    $dataSubcats = unserialize($dataProjectTemplate->subcat_id);
                }else{
                    $dataSubcats = null;
                }
                if (isset($dataProjectTemplate->subcatcustom_id)) {
                    $dataSubcatsCustom = unserialize($dataProjectTemplate->subcatcustom_id);
                }else{
                    $dataSubcatsCustom = null;
                }
            ###image type
                if ($dataProjectTemplate->type == $templateImage) {
                    //data subcategory
                        if (isset($dataSubcats)) {
                            $data['dataSubcategory'] = DB::table('project_report_subcategory as prs')
                            ->leftjoin('project_report_images','project_report_images.subcat_id','prs.id')
                            ->select([
                                'prs.id',
                                'prs.name',
                                DB::raw('(SELECT COUNT(*) FROM project_report_images as pri WHERE pri.subcat_id = prs.id AND pri.project_id = '.$projectId.' AND pri.task_id = '.$taskId.') as imageCount'),
        
                                DB::raw('(SELECT COUNT(project_report_images.submitted_at) FROM project_report_images WHERE project_report_images.subcat_id = prs.id AND project_report_images.project_id='.$projectId.' AND project_report_images.task_id = '.$taskId.' AND project_report_images.template_id = '.$id.' AND project_report_images.submitted_at IS NOT NULL) as submittedCount'),
        
                                DB::raw('(SELECT COUNT(project_report_images.approved_at) FROM project_report_images WHERE project_report_images.subcat_id = prs.id AND project_report_images.project_id='.$projectId.' AND project_report_images.task_id = '.$taskId.' AND project_report_images.template_id = '.$id.' AND project_report_images.approved_at IS NOT NULL) as approvedCount'),
        
                                DB::raw('(SELECT COUNT(project_report_images.approved_by_pm_at) FROM project_report_images WHERE project_report_images.subcat_id = prs.id AND project_report_images.project_id='.$projectId.' AND project_report_images.task_id = '.$taskId.' AND project_report_images.template_id = '.$id.' AND project_report_images.approved_by_pm_at IS NOT NULL) as approvedPMCount'),
                            ])
                            ->where('prs.status',$subcatStatus)
                            ->where('prs.cat_id',$id)
                            ->whereIn('prs.id',$dataSubcats)
                            ->where('prs.deleted_at',null)
                            ->groupBy('prs.id')
                            ->get();
                        }else{
                            $data['dataSubcategory'] = [];
                        }
                    //customized subcategories
                        if (isset($dataSubcatsCustom)) {
                            $data['dataSubcategoryCustomized'] = DB::table('project_report_subcategory_customized as prsc')
                            ->leftjoin('project_report_images','project_report_images.subcatcustom_id','prsc.id')
                            ->select([
                                'prsc.id',
                                'prsc.name',
                                DB::raw('(SELECT COUNT(*) FROM project_report_images as pri WHERE pri.subcatcustom_id = prsc.id AND pri.project_id = '.$projectId.' AND pri.task_id = '.$taskId.') as imageCount'),
        
                                DB::raw('(SELECT COUNT(project_report_images.submitted_at) FROM project_report_images WHERE project_report_images.subcatcustom_id = prsc.id AND project_report_images.project_id='.$projectId.' AND project_report_images.task_id = '.$taskId.' AND project_report_images.template_id = '.$id.' AND project_report_images.submitted_at IS NOT NULL) as submittedCount'),
        
                                DB::raw('(SELECT COUNT(project_report_images.approved_at) FROM project_report_images WHERE project_report_images.subcatcustom_id = prsc.id AND project_report_images.project_id='.$projectId.' AND project_report_images.task_id = '.$taskId.' AND project_report_images.template_id = '.$id.' AND project_report_images.approved_at IS NOT NULL) as approvedCount'),
        
                                DB::raw('(SELECT COUNT(project_report_images.approved_by_pm_at) FROM project_report_images WHERE project_report_images.subcatcustom_id = prsc.id AND project_report_images.project_id='.$projectId.' AND project_report_images.task_id = '.$taskId.' AND project_report_images.template_id = '.$id.' AND project_report_images.approved_by_pm_at IS NOT NULL) as approvedPMCount'),
                            ])
                            ->where('prsc.cat_id',$id)
                            ->whereIn('prsc.id',$dataSubcatsCustom)
                            ->where('prsc.deleted_at',null)
                            ->groupBy('prsc.id')
                            ->get();
                        }else{
                            $data['dataSubcategoryCustomized'] = [];
                        }
                }
            ###image type end
            ###text type
                if ($dataProjectTemplate->type == $templateText) {
                    //data subcategory
                        if (isset($dataSubcats)) {
                            $data['dataSubcategoryText'] = DB::table('project_report_subcategory as prs')
                            ->leftjoin('project_report_text','project_report_text.subcat_id','prs.id')
                            ->select([
                                'prs.id',
                                'prs.name',
                                DB::raw('(SELECT COUNT(*) FROM project_report_text as pri WHERE pri.subcat_id = prs.id AND pri.project_id = '.$projectId.' AND pri.task_id = '.$taskId.') as textCount'),
        
                                DB::raw('(SELECT COUNT(project_report_text.submitted_at) FROM project_report_text WHERE project_report_text.subcat_id = prs.id AND project_report_text.project_id='.$projectId.' AND project_report_text.task_id = '.$taskId.' AND project_report_text.template_id = '.$id.' AND project_report_text.submitted_at IS NOT NULL) as submittedCount'),
        
                                DB::raw('(SELECT COUNT(project_report_text.approved_at) FROM project_report_text WHERE project_report_text.subcat_id = prs.id AND project_report_text.project_id='.$projectId.' AND project_report_text.task_id = '.$taskId.' AND project_report_text.template_id = '.$id.' AND project_report_text.approved_at IS NOT NULL) as approvedCount'),
        
                                DB::raw('(SELECT COUNT(project_report_text.approved_by_pm_at) FROM project_report_text WHERE project_report_text.subcat_id = prs.id AND project_report_text.project_id='.$projectId.' AND project_report_text.task_id = '.$taskId.' AND project_report_text.template_id = '.$id.' AND project_report_text.approved_by_pm_at IS NOT NULL) as approvedPMCount'),
        
                            ])
                            ->where('prs.status',$subcatStatus)
                            ->where('prs.cat_id',$id)
                            ->whereIn('prs.id',$dataSubcats)
                            ->where('prs.deleted_at',null)
                            ->groupBy('prs.id')
                            ->get();
                        }else{
                            $data['dataSubcategoryText'] = [];
                        }
                    //customized subcategories
                        if (isset($dataSubcatsCustom)) {
                            $data['dataSubcategoryCustomizedText'] = DB::table('project_report_subcategory_customized as prsc')
                            ->leftjoin('project_report_text','project_report_text.subcatcustom_id','prsc.id')
                            ->select([
                                'prsc.id',
                                'prsc.name',
                                DB::raw('(SELECT COUNT(*) FROM project_report_text as pri WHERE pri.subcatcustom_id = prsc.id AND pri.project_id = '.$projectId.' AND pri.task_id = '.$taskId.') as textCount'),
        
                                DB::raw('(SELECT COUNT(project_report_text.submitted_at) FROM project_report_text WHERE project_report_text.subcatcustom_id = prsc.id AND project_report_text.project_id='.$projectId.' AND project_report_text.task_id = '.$taskId.' AND project_report_text.template_id = '.$id.' AND project_report_text.submitted_at IS NOT NULL) as submittedCount'),
        
                                DB::raw('(SELECT COUNT(project_report_text.approved_at) FROM project_report_text WHERE project_report_text.subcatcustom_id = prsc.id AND project_report_text.project_id='.$projectId.' AND project_report_text.task_id = '.$taskId.' AND project_report_text.template_id = '.$id.' AND project_report_text.approved_at IS NOT NULL) as approvedCount'),
        
                                DB::raw('(SELECT COUNT(project_report_text.approved_by_pm_at) FROM project_report_text WHERE project_report_text.subcatcustom_id = prsc.id AND project_report_text.project_id='.$projectId.' AND project_report_text.task_id = '.$taskId.' AND project_report_text.template_id = '.$id.' AND project_report_text.approved_by_pm_at IS NOT NULL) as approvedPMCount'),
                            ])
                            ->where('prsc.cat_id',$id)
                            ->whereIn('prsc.id',$dataSubcatsCustom)
                            ->where('prsc.deleted_at',null)
                            ->groupBy('prsc.id')
                            ->get();
                        }else{
                            $data['dataSubcategoryCustomizedText'] = [];
                        }
                }
            ###text type end
            ###file type
                if ($dataProjectTemplate->type == $templateFile) {
                    //data subcategory
                        if (isset($dataSubcats)) {
                            $data['dataSubcategoryFiles'] = DB::table('project_report_subcategory as prs')
                            ->leftjoin('project_report_file','project_report_file.subcat_id','prs.id')
                            ->select([
                                'prs.id',
                                'prs.name',
                                DB::raw('(SELECT id FROM project_report_file as pri WHERE pri.subcat_id = prs.id AND pri.project_id = '.$projectId.' AND pri.task_id = '.$taskId.') as prf_id'),
        
                                DB::raw('(SELECT COUNT(*) FROM project_report_file as pri WHERE pri.subcat_id = prs.id AND pri.project_id = '.$projectId.' AND pri.task_id = '.$taskId.' AND pri.filled IS NOT NULL) as fileCount'),
        
                                DB::raw('(SELECT name FROM project_report_file as pri WHERE pri.subcat_id = prs.id AND pri.project_id = '.$projectId.' AND pri.task_id = '.$taskId.') as file_name'),
                                
                                DB::raw('(SELECT filled FROM project_report_file as pri WHERE pri.subcat_id = prs.id AND pri.project_id = '.$projectId.' AND pri.task_id = '.$taskId.') as uploaded_file_name'),
        
                                DB::raw('(SELECT COUNT(project_report_file.submitted_at) FROM project_report_file WHERE project_report_file.subcat_id = prs.id AND project_report_file.project_id='.$projectId.' AND project_report_file.task_id = '.$taskId.' AND project_report_file.submitted_at IS NOT NULL) as submittedCount'),
        
                                DB::raw('(SELECT COUNT(project_report_file.approved_at) FROM project_report_file WHERE project_report_file.subcat_id = prs.id AND project_report_file.project_id='.$projectId.' AND project_report_file.task_id = '.$taskId.' AND project_report_file.approved_at IS NOT NULL) as approvedCount'),
        
                                DB::raw('(SELECT COUNT(project_report_file.approved_by_pm_at) FROM project_report_file WHERE project_report_file.subcat_id = prs.id AND project_report_file.project_id='.$projectId.' AND project_report_file.task_id = '.$taskId.' AND project_report_file.approved_by_pm_at IS NOT NULL) as approvedPMCount'),
        
                            ])
                            ->where('prs.status',$subcatStatus)
                            ->where('prs.cat_id',$id)
                            ->whereIn('prs.id',$dataSubcats)
                            ->where('prs.deleted_at',null)
                            ->groupBy('prs.id')
                            ->get();
                        }else{
                            $data['dataSubcategoryFiles'] = [];
                        }
                    //customized subcategories
                        if (isset($dataSubcatsCustom)) {
                            $data['dataSubcategoryCustomizedFile'] = DB::table('project_report_subcategory_customized as prsc')
                            ->leftjoin('project_report_file','project_report_file.subcatcustom_id','prsc.id')
                            ->select([
                                'prsc.id',
                                'prsc.name',
                                DB::raw('(SELECT id FROM project_report_file as pri WHERE pri.subcatcustom_id = prsc.id AND pri.project_id = '.$projectId.' AND pri.task_id = '.$taskId.') as prf_id'),
        
                                DB::raw('(SELECT COUNT(*) FROM project_report_file as pri WHERE pri.subcatcustom_id = prsc.id AND pri.project_id = '.$projectId.' AND pri.task_id = '.$taskId.' AND pri.filled IS NOT NULL) as fileCount'),
        
                                DB::raw('(SELECT name FROM project_report_file as pri WHERE pri.subcatcustom_id = prsc.id AND pri.project_id = '.$projectId.' AND pri.task_id = '.$taskId.') as file_name'),
        
                                DB::raw('(SELECT filled FROM project_report_file as pri WHERE pri.subcatcustom_id = prsc.id AND pri.project_id = '.$projectId.' AND pri.task_id = '.$taskId.') as uploaded_file_name'),
        
                                DB::raw('(SELECT COUNT(project_report_file.submitted_at) FROM project_report_file WHERE project_report_file.subcatcustom_id = prsc.id AND project_report_file.project_id='.$projectId.' AND project_report_file.task_id = '.$taskId.' AND project_report_file.submitted_at IS NOT NULL) as submittedCount'),
        
                                DB::raw('(SELECT COUNT(project_report_file.approved_at) FROM project_report_file WHERE project_report_file.subcatcustom_id = prsc.id AND project_report_file.project_id='.$projectId.' AND project_report_file.task_id = '.$taskId.' AND project_report_file.approved_at IS NOT NULL) as approvedCount'),
        
                                DB::raw('(SELECT COUNT(project_report_file.approved_by_pm_at) FROM project_report_file WHERE project_report_file.subcatcustom_id = prsc.id AND project_report_file.project_id='.$projectId.' AND project_report_file.task_id = '.$taskId.' AND project_report_file.approved_by_pm_at IS NOT NULL) as approvedPMCount'),
                            ])
                            ->where('prsc.cat_id',$id)
                            ->whereIn('prsc.id',$dataSubcatsCustom)
                            ->where('prsc.deleted_at',null)
                            ->groupBy('prsc.id')
                            ->get();
                        }else{
                            $data['dataSubcategoryCustomizedFile'] = [];
                        }
                }
            ###file type end
        ### sub category end ###
        return response()->json($data);
    }

    public function detail(Request $request, $id)
    {
        $user = $this->authUser();
        
        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia.']);
        }
        
        if (!isset($request->project_id)) {
            return response()->json(['error' => 'Maaf, project id tidak tersedia.']);
        } 
        
        if (!isset($request->task_id)) {
            return response()->json(['error' => 'Maaf, task id tidak tersedia.']);
        }
        if (!isset($request->template_id)) {
            return response()->json(['error' => 'Maaf, template id tidak tersedia.']);
        }
        if (!isset($request->subcat_id) && !isset($request->subcatcustom_id)) {
            return response()->json(['error' => 'Maaf, subcat/subcatcustom id tidak tersedia.']);
        }
        
        $techId = $user->id;
        $taskId = $request->task_id;
        $projectId = $request->project_id;
        $templateId = $request->template_id;

        $catStatus = 1; //active category
        $subcatStatus = 1; //active subcategory
        $commentStatus = 0; //accessible for technician level user

        //template type
            $templateImage = 1;
            $templateText = 2;
            $templateFile = 6;

        //check priviledge
        $priviledgeCheck = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('tech_id',$techId)->where('deleted_at',null)->count();
        
        if ($priviledgeCheck > 0) {
            $subcatRequestData = $request->subcat_id;
            if ($subcatRequestData !== null) {
                $subcatId = $request->subcat_id;
                $subcatName = 'subcat_id';
            }else{
                $subcatId = $request->subcatcustom_id;
                $subcatName = 'subcatcustom_id';
            }
            ### template data ###
                $data['projectTemplate'] = DB::table('project_report_template_selected as prtss')
                ->select([
                    'prtss.project_id',
                    'prtss.task_id',
                    'prtss.template_id',
                    DB::raw('(SELECT name FROM projects WHERE projects.id = prtss.project_id) as project_name'),
                    DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = prtss.task_id) as task_name'),
                    //DB::raw('(SELECT status FROM projects WHERE projects.id = prtss.project_id) as project_status'),
                    DB::raw('(SELECT name FROM project_report_category WHERE project_report_category.id = prtss.template_id) as name'),
                    //subcategory
                        DB::raw('(SELECT name FROM project_report_subcategory as prsub WHERE prsub.id = '.$subcatId.') as subcat_name'),
                    //tempate folder
                        DB::raw('(SELECT procat_id FROM projects WHERE projects.id = prtss.project_id) as procat_id'),
                        DB::raw('(SELECT folder FROM projects_category WHERE projects_category.id = procat_id) as folder_name'),
                    //tempate type
                        DB::raw('(SELECT type FROM project_report_category WHERE project_report_category.id = prtss.template_id) as type'),
                ])
                ->where('template_id',$templateId)
                ->where('project_id',$projectId)
                ->where('task_id',$taskId)
                ->where('deleted_at',null)->first();
            ### template data end ###

            //third check
                if (!$data['projectTemplate']) {
                    return response()->json('message','Subcategory yang Anda tuju tidak tersedia pada template ini.');
                }
            
            //check template type and redirect
                if ($data['projectTemplate']->type == $templateImage) {
                    ### project pictures ###
                        if ($subcatRequestData !== null) {
                            $data['dataProjectPictures'] = DB::table('project_report_images')
                            ->select([
                                'id',
                                'image',
                                'selected_image',
                                'submitted_at',
                                'approved_at',
                                'approved_by_pm_at'
                            ])
                            ->where('project_id',$projectId)
                            ->where('task_id',$taskId)
                            ->where('template_id',$templateId)
                            ->where('subcat_id',$subcatId)
                            ->where('publisher_id',$techId)
                            ->get();
                        }else{
                            $data['dataProjectPictures'] = DB::table('project_report_images')
                            ->select([
                                'id',
                                'image',
                                'selected_image',
                                'submitted_at',
                                'approved_at',
                                'approved_by_pm_at'
                            ])
                            ->where('project_id',$projectId)
                            ->where('task_id',$taskId)
                            ->where('template_id',$templateId)
                            ->where('subcatcustom_id',$subcatId)
                            ->where('publisher_id',$techId)
                            ->get();
                        }
                    
                    ### comments ###
                        if ($subcatRequestData !== null) {
                            $data['dataComments'] = DB::table('project_report_images_comments as prtc')->where('status',$commentStatus)
                            ->select([
                                'comment',
                                'date',
                                //tech comments
                                    DB::raw('(SELECT firstname FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_firstname'),
                                    DB::raw('(SELECT lastname FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_latname'),
                                    DB::raw('(SELECT title FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_title'),
                                //users comments
                                    DB::raw('(SELECT firstname FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_firstname'),
                                    DB::raw('(SELECT lastname FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_latname'),
                                    DB::raw('(SELECT title FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_title'),
                            ])
                            //->where('pri_id',$data['dataProjectPicturesStatus']->id)
                            ->where('project_id',$projectId)
                            ->where('task_id',$taskId)
                            ->where('subcat_id',$subcatId)
                            ->orderBy('date','DESC')
                            ->get();
                        }else{
                            $data['dataComments'] = DB::table('project_report_images_comments as prtc')->where('status',$commentStatus)
                            ->select([
                                'comment',
                                'date',
                                //tech comments
                                    DB::raw('(SELECT firstname FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_firstname'),
                                    DB::raw('(SELECT lastname FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_latname'),
                                    DB::raw('(SELECT title FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_title'),
                                //users comments
                                    DB::raw('(SELECT firstname FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_firstname'),
                                    DB::raw('(SELECT lastname FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_latname'),
                                    DB::raw('(SELECT title FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_title'),
                            ])
                            //->where('pri_id',$data['dataProjectPicturesStatus']->id)
                            ->where('project_id',$projectId)
                            ->where('task_id',$taskId)
                            ->where('subcatcustom_id',$subcatId)
                            ->orderBy('date','DESC')
                            ->get();
                        }
                    ### comments end ###
                    return response()->json($data);
                }elseif(($data['projectTemplate']->type == $templateText)){
                    ### project text ###
                        if ($subcatRequestData !== null) {
                            $data['dataProjectText'] = DB::table('project_report_text')
                            ->select([
                                'id',
                                'text',
                                'submitted_at',
                                'approved_at',
                                'approved_by_pm_at'
                            ])
                            ->where('project_id',$projectId)
                            ->where('task_id',$taskId)
                            ->where('template_id',$templateId)
                            ->where('subcat_id',$subcatId)
                            ->where('publisher_id',$techId)
                            ->first();
                        }else{
                            $data['dataProjectText'] = DB::table('project_report_text')
                            ->select([
                                'id',
                                'text',
                                'submitted_at',
                                'approved_at',
                                'approved_by_pm_at'
                            ])
                            ->where('project_id',$projectId)
                            ->where('task_id',$taskId)
                            ->where('template_id',$templateId)
                            ->where('subcatcustom_id',$subcatId)
                            ->where('publisher_id',$techId)
                            ->first();
                        }
                    ### project text end ###
                    ### comments ###
                        if ($subcatRequestData !== null) {
                            $data['dataComments'] = DB::table('project_report_text_comments as prtc')->where('status',$commentStatus)
                            ->select([
                                'comment',
                                'date',
                                //tech comments
                                    DB::raw('(SELECT firstname FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_firstname'),
                                    DB::raw('(SELECT lastname FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_latname'),
                                    DB::raw('(SELECT title FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_title'),
                                //users comments
                                    DB::raw('(SELECT firstname FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_firstname'),
                                    DB::raw('(SELECT lastname FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_latname'),
                                    DB::raw('(SELECT title FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_title'),
                            ])
                            //->where('prt_id',$data['dataProjectTextStatus']->id)
                            ->where('project_id',$projectId)
                            ->where('task_id',$taskId)
                            ->where('subcat_id',$subcatId)
                            ->orderBy('date','DESC')
                            ->get();
                        }else{
                            $data['dataComments'] = DB::table('project_report_text_comments')->where('status',$commentStatus)
                            ->select([
                                'comment',
                                'date',
                                //tech comments
                                    DB::raw('(SELECT firstname FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_firstname'),
                                    DB::raw('(SELECT lastname FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_latname'),
                                    DB::raw('(SELECT title FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_title'),
                                //users comments
                                    DB::raw('(SELECT firstname FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_firstname'),
                                    DB::raw('(SELECT lastname FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_latname'),
                                    DB::raw('(SELECT title FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_title'),
                            ])
                            //->where('prt_id',$data['dataProjectTextStatus']->id)
                            ->where('project_id',$projectId)
                            ->where('task_id',$taskId)
                            ->where('subcatcustom_id',$subcatId)
                            ->orderBy('date','DESC')
                            ->get();
                        }
                    ### comments end ###

                    return response()->json($data);
                }
            return response()->json(['message' => 'Type template tidak tersedia, data tidak berhasil ditampilkan.']);
        }
        return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
    }

    public function detailUpload(Request $request, $id)
    {
        $user = $this->authUser();
        
        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Data belum tersedia.']);
        }
        
        if (!isset($request->project_id)) {
            return response()->json(['error' => 'Maaf, project id tidak tersedia.']);
        } 
        
        if (!isset($request->task_id)) {
            return response()->json(['error' => 'Maaf, task id tidak tersedia.']);
        }
        if (!isset($request->template_id)) {
            return response()->json(['error' => 'Maaf, template id tidak tersedia.']);
        }
        if (!isset($request->subcat_id) && !isset($request->subcatcustom_id)) {
            return response()->json(['error' => 'Maaf, subcat/subcatcustom id tidak tersedia.']);
        }
        
        $techId = $user->id;
        $taskId = $request->task_id;
        $projectId = $request->project_id;
        $templateId = $request->template_id;

        $catStatus = 1; //active category
        $subcatStatus = 1; //active subcategory
        $commentStatus = 0; //accessible for technician level user

        //template type
            $templateImage = 1;
            $templateText = 2;
            $templateFile = 6;

        //check priviledge
        $priviledgeCheck = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('tech_id',$techId)->where('deleted_at',null)->count();
        
        if ($priviledgeCheck > 0) {
            $subcatRequestData = $request->subcat_id;
            if ($subcatRequestData !== null) {
                $subcatId = $request->subcat_id;
                $subcatName = 'subcat_id';
            }else{
                $subcatId = $request->subcatcustom_id;
                $subcatName = 'subcatcustom_id';
            }
            ### template data ###
                $data['projectTemplate'] = DB::table('project_report_template_selected as prtss')
                ->select([
                    'prtss.project_id',
                    'prtss.task_id',
                    'prtss.template_id',
                    DB::raw('(SELECT name FROM projects WHERE projects.id = prtss.project_id) as project_name'),
                    DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = prtss.task_id) as task_name'),
                    //DB::raw('(SELECT status FROM projects WHERE projects.id = prtss.project_id) as project_status'),
                    DB::raw('(SELECT name FROM project_report_category WHERE project_report_category.id = prtss.template_id) as name'),
                    //subcategory
                        DB::raw('(SELECT name FROM project_report_subcategory as prsub WHERE prsub.id = '.$subcatId.') as subcat_name'),
                    //tempate folder
                        DB::raw('(SELECT procat_id FROM projects WHERE projects.id = prtss.project_id) as procat_id'),
                        DB::raw('(SELECT folder FROM projects_category WHERE projects_category.id = procat_id) as folder_name'),
                    //tempate type
                        DB::raw('(SELECT type FROM project_report_category WHERE project_report_category.id = prtss.template_id) as type'),
                ])
                ->where('template_id',$templateId)
                ->where('project_id',$projectId)
                ->where('task_id',$taskId)
                ->where('deleted_at',null)->first();
            ### template data end ###

            //third check
                if (!$data['projectTemplate']) {
                    return response()->json('message','Subcategory yang Anda tuju tidak tersedia pada template ini.');
                }
            
            //check template type and redirect
                if ($data['projectTemplate']->type == $templateImage) {
                    ### project pictures ###
                        if ($subcatRequestData !== null) {
                            $data['dataProjectPictures'] = DB::table('project_report_images')
                            ->select([
                                'id',
                                'image',
                                'selected_image',
                                'submitted_at',
                                'approved_at',
                                'approved_by_pm_at'
                            ])
                            ->where('project_id',$projectId)
                            ->where('task_id',$taskId)
                            ->where('template_id',$templateId)
                            ->where('subcat_id',$subcatId)
                            ->where('publisher_id',$techId)
                            ->get();
                        }else{
                            $data['dataProjectPictures'] = DB::table('project_report_images')
                            ->select([
                                'id',
                                'image',
                                'selected_image',
                                'submitted_at',
                                'approved_at',
                                'approved_by_pm_at'
                            ])
                            ->where('project_id',$projectId)
                            ->where('task_id',$taskId)
                            ->where('template_id',$templateId)
                            ->where('subcatcustom_id',$subcatId)
                            ->where('publisher_id',$techId)
                            ->get();
                        }
                    
                    ### comments ###
                        if ($subcatRequestData !== null) {
                            $data['dataComments'] = DB::table('project_report_images_comments as prtc')->where('status',$commentStatus)
                            ->select([
                                'comment',
                                'date',
                                //tech comments
                                    DB::raw('(SELECT firstname FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_firstname'),
                                    DB::raw('(SELECT lastname FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_latname'),
                                    DB::raw('(SELECT title FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_title'),
                                //users comments
                                    DB::raw('(SELECT firstname FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_firstname'),
                                    DB::raw('(SELECT lastname FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_latname'),
                                    DB::raw('(SELECT title FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_title'),
                            ])
                            //->where('pri_id',$data['dataProjectPicturesStatus']->id)
                            ->where('project_id',$projectId)
                            ->where('task_id',$taskId)
                            ->where('subcat_id',$subcatId)
                            ->orderBy('date','DESC')
                            ->get();
                        }else{
                            $data['dataComments'] = DB::table('project_report_images_comments as prtc')->where('status',$commentStatus)
                            ->select([
                                'comment',
                                'date',
                                //tech comments
                                    DB::raw('(SELECT firstname FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_firstname'),
                                    DB::raw('(SELECT lastname FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_latname'),
                                    DB::raw('(SELECT title FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_title'),
                                //users comments
                                    DB::raw('(SELECT firstname FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_firstname'),
                                    DB::raw('(SELECT lastname FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_latname'),
                                    DB::raw('(SELECT title FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_title'),
                            ])
                            //->where('pri_id',$data['dataProjectPicturesStatus']->id)
                            ->where('project_id',$projectId)
                            ->where('task_id',$taskId)
                            ->where('subcatcustom_id',$subcatId)
                            ->orderBy('date','DESC')
                            ->get();
                        }
                    ### comments end ###
                    return response()->json($data);
                }elseif(($data['projectTemplate']->type == $templateText)){
                    ### project text ###
                        if ($subcatRequestData !== null) {
                            $data['dataProjectText'] = DB::table('project_report_text')
                            ->select([
                                'id',
                                'text',
                                'submitted_at',
                                'approved_at',
                                'approved_by_pm_at'
                            ])
                            ->where('project_id',$projectId)
                            ->where('task_id',$taskId)
                            ->where('template_id',$templateId)
                            ->where('subcat_id',$subcatId)
                            ->where('publisher_id',$techId)
                            ->first();
                        }else{
                            $data['dataProjectText'] = DB::table('project_report_text')
                            ->select([
                                'id',
                                'text',
                                'submitted_at',
                                'approved_at',
                                'approved_by_pm_at'
                            ])
                            ->where('project_id',$projectId)
                            ->where('task_id',$taskId)
                            ->where('template_id',$templateId)
                            ->where('subcatcustom_id',$subcatId)
                            ->where('publisher_id',$techId)
                            ->first();
                        }
                    ### project text end ###
                    ### comments ###
                        if ($subcatRequestData !== null) {
                            $data['dataComments'] = DB::table('project_report_text_comments as prtc')->where('status',$commentStatus)
                            ->select([
                                'comment',
                                'date',
                                //tech comments
                                    DB::raw('(SELECT firstname FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_firstname'),
                                    DB::raw('(SELECT lastname FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_latname'),
                                    DB::raw('(SELECT title FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_title'),
                                //users comments
                                    DB::raw('(SELECT firstname FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_firstname'),
                                    DB::raw('(SELECT lastname FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_latname'),
                                    DB::raw('(SELECT title FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_title'),
                            ])
                            //->where('prt_id',$data['dataProjectTextStatus']->id)
                            ->where('project_id',$projectId)
                            ->where('task_id',$taskId)
                            ->where('subcat_id',$subcatId)
                            ->orderBy('date','DESC')
                            ->get();
                        }else{
                            $data['dataComments'] = DB::table('project_report_text_comments')->where('status',$commentStatus)
                            ->select([
                                'comment',
                                'date',
                                //tech comments
                                    DB::raw('(SELECT firstname FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_firstname'),
                                    DB::raw('(SELECT lastname FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_latname'),
                                    DB::raw('(SELECT title FROM techs WHERE techs.id = prtc.publisher_id AND prtc.publisher_type = \'tech\') as tech_title'),
                                //users comments
                                    DB::raw('(SELECT firstname FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_firstname'),
                                    DB::raw('(SELECT lastname FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_latname'),
                                    DB::raw('(SELECT title FROM users WHERE users.id = prtc.publisher_id AND prtc.publisher_type = \'user\') as tech_title'),
                            ])
                            //->where('prt_id',$data['dataProjectTextStatus']->id)
                            ->where('project_id',$projectId)
                            ->where('task_id',$taskId)
                            ->where('subcatcustom_id',$subcatId)
                            ->orderBy('date','DESC')
                            ->get();
                        }
                    ### comments end ###

                    return response()->json($data);
                }
            return response()->json(['message' => 'Type template tidak tersedia, data tidak berhasil ditampilkan.']);
        }
        return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
    }

    public function template(Request $request)
    {
        $user = $this->authUser();

        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }

        $techId = $user->id;

        $data['template'] = DB::table('project_report_category')->select([
            'id',
            'name',
        ])-> get();

        return response()->json($data);
    }
}
