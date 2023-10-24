<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class UserProjectImageController extends Controller
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
    public function index(Request $request)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Images.');
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
        $userDepartment = 1; //project department

        $projectId = $request->project_id;
        $taskId = $request->task_id;
        $templateId = $request->template_id; //category
        
        $subcatRequestData = $request->subcat_id;
        if ($subcatRequestData !== null) {
            $subcatId = $request->subcat_id;
            $subcatName = 'subcat_id';
        }else{
            $subcatId = $request->subcatcustom_id;
            $subcatName = 'subcatcustom_id';
        }

        $data['subcatId'] = $subcatId;
        $data['subcatName'] = $subcatName;
                
        $catStatus = 1; //active category
        $subcatStatus = 1; //active subcategory
        $commentStatus = 0; //accessible for technician level user

        
        //check priviledge
        $privilegeCheck = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('qcd_id',$userId)->where('deleted_at',null)->count();
        
        if ($privilegeCheck > 0) {
            
            //getting the data
            $data['projectTemplate'] = DB::table('project_report_template_selected as prtss')
            ->select([
                'prtss.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = prtss.project_id) as project_name'),
                DB::raw('(SELECT procat_id FROM projects WHERE projects.id = prtss.project_id) as procat_id'),
                DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = prtss.task_id) as task_name'),
                DB::raw('(SELECT status FROM projects WHERE projects.id = prtss.project_id) as project_status'),
                DB::raw('(SELECT name FROM project_report_category WHERE project_report_category.id = prtss.template_id) as name'),
            ])
            ->where('template_id',$templateId)
            ->where('project_id',$projectId)
            ->where('task_id',$taskId)
            ->where('deleted_at',null)->first();

            //third check
            if ($subcatRequestData !== null) {
                $dataSubcat = unserialize($data['projectTemplate']->subcat_id);
            }else{
                $dataSubcat = unserialize($data['projectTemplate']->subcatcustom_id);
            }
            if (!in_array($subcatId,$dataSubcat)) {
                return redirect()->back()->with('alert-danger','Subcategory yang Anda tuju tidak tersedia pada template ini.');
            }

            //project category used for image folder placement
            $data['dataProjectCategory'] = DB::table('projects_category')->where('id',$data['projectTemplate']->procat_id)->first();
            
            //project pictures
            if ($subcatRequestData !== null) {
                $data['dataProjectPictures'] = DB::table('project_report_images')
                ->where('project_id',$projectId)
                ->where('task_id',$taskId)
                ->where('template_id',$templateId)
                ->where('subcat_id',$subcatId)
                //->where('publisher_id',$techId)
                ->get();
            }else{
                $data['dataProjectPictures'] = DB::table('project_report_images')
                ->where('project_id',$projectId)
                ->where('task_id',$taskId)
                ->where('template_id',$templateId)
                ->where('subcatcustom_id',$subcatId)
                //->where('publisher_id',$techId)
                ->get();
            }

            $data['dataProjectPicturesStatus'] = DB::table('project_report_images as pri')
            ->select([
                'pri.*',
                DB::raw('COUNT(pri.submitted_at) as countSubmitted'),
                DB::raw('COUNT(pri.approved_at) as countApproved'),
                DB::raw('COUNT(pri.approved_by_pm_at) as countPMApproved'),
                //comments
                DB::raw('(SELECT COUNT(*) FROM project_report_images_comments WHERE project_report_images_comments.pri_id = pri.id) as commentsCount')
            ])
            ->where('template_id',$templateId)
            ->where('project_id',$projectId)
            ->where('task_id',$taskId)
            ->where($subcatName,$subcatId)
            #->where('selected_image',0)
            ->first();
            //submit count
                if ($subcatRequestData !== null) {
                    $data['submittedCount'] = DB::table('project_report_images')
                    ->where('project_id',$projectId)
                    ->where('task_id',$taskId)
                    ->where('subcat_id',$subcatId)
                    ->where('submitted_at','!=',null)->count();
                }else{
                    $data['submittedCount'] = DB::table('project_report_images')
                    ->where('project_id',$projectId)
                    ->where('task_id',$taskId)
                    ->where('subcatcustom_id',$subcatId)
                    ->where('submitted_at','!=',null)->count();
                }

            $data['subcatsPictureByCatCount'] = DB::table('project_report_images as pri')
            ->select([
                'pri.*', 
                DB::raw('COUNT(pri.cat_id) as total')
            ])
            ->where('task_id',$taskId)
            ->groupBy('cat_id')
            ->get();

            if ($subcatRequestData !== null) {
                $data['dataSubcategory'] = DB::table('project_report_subcategory as prs')
                ->select([
                    'prs.*',
                    DB::raw('(SELECT COUNT(*) FROM project_report_images WHERE project_report_images.task_id = '.$taskId.' AND project_report_images.subcat_id = prs.id) as subcatcount')
                ])
                //->where('id',$subcatId)
                ->where('status',$subcatStatus)
                ->where('deleted_at',null)->get();
            }else{
                $data['dataSubcategory'] = DB::table('project_report_subcategory_customized as prsc')
                ->select([
                    'prsc.*',
                    DB::raw('(SELECT COUNT(*) FROM project_report_images WHERE project_report_images.task_id = '.$taskId.' AND project_report_images.subcatcustom_id = prsc.id) as subcatcount')
                ])
                //->where('id',$subcatId)
                ->where('deleted_at',null)->get();
            }

            //supporting data
            $data['projectTask'] = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
            ])
            ->where('id',$taskId)->where('qcd_id',$userId)->where('deleted_at',null)->first();

            ### approver count ###
            //submit count
            if ($subcatRequestData !== null) {
                $data['approverData'] = DB::table('project_report_images')
                ->where('project_id',$projectId)
                ->where('task_id',$taskId)
                ->where('subcat_id',$subcatId)
                ->orderBy('selected_image','DESC')
                ->first();
            }else{
                $data['approverData'] = DB::table('project_report_images')
                ->where('project_id',$projectId)
                ->where('task_id',$taskId)
                ->where('subcatcustom_id',$subcatId)
                ->orderBy('selected_image','DESC')
                ->first();
            }
            ### approver count ###

            ### comments ###
            if ($subcatRequestData !== null) {
                $data['dataComments'] = DB::table('project_report_images_comments')->where('status',$commentStatus)
                ->where('pri_id',$data['dataProjectPicturesStatus']->id)
                ->where('project_id',$projectId)
                ->where('task_id',$taskId)
                ->where('subcat_id',$subcatId)
                ->orderBy('date','DESC')
                ->get();
            }else{
                $data['dataComments'] = DB::table('project_report_images_comments')->where('status',$commentStatus)
                ->where('pri_id',$data['dataProjectPicturesStatus']->id)
                ->where('project_id',$projectId)
                ->where('task_id',$taskId)
                ->where('subcatcustom_id',$subcatId)
                ->orderBy('date','DESC')
                ->get();
            }

            $data['techs'] = DB::table('techs')->get();
            $data['users'] = DB::table('users')
            ->select([
                'users.*',
                DB::raw('(SELECT name FROM users_level WHERE users_level.id = users.user_level) as title')
            ])
            ->get();
            ### comments end ###
            
            return view('user.project.image.create',$data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Images.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Images.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        $projectId = $request->project_id;
        $taskId = $request->task_id;

        $catStatus = 1; //active category
        $subcatStatus = 1; //active subcategory

        //first check
        $firstCheck = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('qcd_id',$userId)->where('deleted_at',null)->count();

        if ($firstCheck > 0) {

            //second check
            $dataCheck = DB::table('project_report_category')->where('id',$id)->where('deleted_at',null)->count();
            if ($dataCheck < 1) {
                return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
            }
            
            $dataCheck2 = DB::table('project_report_template_selected')->where('template_id',$id)->where('project_id',$projectId)->where('task_id',$taskId)->where('deleted_at',null)->count();
            
            if ($dataCheck2 < 1) {
                return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
            }

            $data['projectTemplate'] = DB::table('project_report_template_selected as prtss')
            ->select([
                'prtss.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = prtss.project_id) as project_name'),
                DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = prtss.task_id) as task_name'),
                DB::raw('(SELECT status FROM projects WHERE projects.id = prtss.project_id) as project_status'),
                DB::raw('(SELECT name FROM project_report_category WHERE project_report_category.id = prtss.template_id) as name'),
            ])
            ->where('template_id',$id)
            ->where('project_id',$projectId)
            ->where('task_id',$taskId)
            ->where('deleted_at',null)->first();

            //data selected subcat
            $data['subcats'] = unserialize($data['projectTemplate']->subcat_id);
            $data['subcatcustoms'] = unserialize($data['projectTemplate']->subcatcustom_id);
            
            //data subcategory
            $data['dataSubcategory'] = DB::table('project_report_subcategory as prs')
            ->leftjoin('project_report_images','project_report_images.subcat_id','prs.id')
            ->select([
                'prs.*',
                DB::raw('(SELECT COUNT(*) FROM project_report_images as pri WHERE pri.subcat_id = prs.id AND pri.project_id='.$projectId.' AND pri.task_id = '.$taskId.' AND pri.template_id = '.$id.') as imageCount'),

                DB::raw('(SELECT COUNT(project_report_images.submitted_at) FROM project_report_images WHERE project_report_images.subcat_id = prs.id AND project_report_images.project_id='.$projectId.' AND project_report_images.task_id = '.$taskId.' AND project_report_images.template_id = '.$id.' AND project_report_images.submitted_at IS NOT NULL) as submittedCount'),

                DB::raw('(SELECT COUNT(project_report_images.approved_at) FROM project_report_images WHERE project_report_images.subcat_id = prs.id AND project_report_images.project_id='.$projectId.' AND project_report_images.task_id = '.$taskId.' AND project_report_images.template_id = '.$id.' AND project_report_images.approved_at IS NOT NULL) as approvedCount'),

                DB::raw('(SELECT COUNT(project_report_images.approved_by_pm_at) FROM project_report_images WHERE project_report_images.subcat_id = prs.id AND project_report_images.project_id='.$projectId.' AND project_report_images.task_id = '.$taskId.' AND project_report_images.template_id = '.$id.' AND project_report_images.approved_by_pm_at IS NOT NULL) as approvedPMCount'),
            ])
            ->where('prs.status',$subcatStatus)
            ->where('prs.cat_id',$id)
            ->where('prs.deleted_at',null)
            ->groupBy('prs.id')
            ->get();

            //customized subcategories
            $data['dataSubcategoryCustomized'] = DB::table('project_report_subcategory_customized as prsc')
            ->leftjoin('project_report_images','project_report_images.subcatcustom_id','prsc.id')
            ->select([
                'prsc.*',
                DB::raw('(SELECT COUNT(*) FROM project_report_images as pri WHERE pri.subcatcustom_id = prsc.id AND pri.project_id='.$projectId.' AND pri.task_id = '.$taskId.' AND pri.template_id = '.$id.') as imageCount'),

                DB::raw('(SELECT COUNT(project_report_images.submitted_at) FROM project_report_images WHERE project_report_images.subcatcustom_id = prsc.id AND project_report_images.project_id='.$projectId.' AND project_report_images.task_id = '.$taskId.' AND project_report_images.template_id = '.$id.' AND project_report_images.submitted_at IS NOT NULL) as submittedCount'),

                DB::raw('(SELECT COUNT(project_report_images.approved_at) FROM project_report_images WHERE project_report_images.subcatcustom_id = prsc.id AND project_report_images.project_id='.$projectId.' AND project_report_images.task_id = '.$taskId.' AND project_report_images.template_id = '.$id.' AND project_report_images.approved_at IS NOT NULL) as approvedCount'),

                DB::raw('(SELECT COUNT(project_report_images.approved_by_pm_at) FROM project_report_images WHERE project_report_images.subcatcustom_id = prsc.id AND project_report_images.project_id='.$projectId.' AND project_report_images.task_id = '.$taskId.' AND project_report_images.template_id = '.$id.' AND project_report_images.approved_by_pm_at IS NOT NULL) as approvedPMCount'),
            ])
            ->where('prsc.project_id',$projectId)
            ->where('prsc.task_id',$taskId)
            ->where('prsc.cat_id',$id)
            ->where('prsc.deleted_at',null)
            ->groupBy('prsc.id')
            ->get();
            
            return view('user.project.image.show', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Images.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Images.');
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
        $userDepartment = Auth::user()->department_id;

        $projectId = $request->project_id;
        $taskId = $request->task_id;
        $templateId = $request->template_id;
        
        $subcatRequestData = $request->subcat_id;
        if ($subcatRequestData !== null) {
            $subcatId = $request->subcat_id;
            $subcatName = 'subcat_id';
        }else{
            $subcatId = $request->subcatcustom_id;
            $subcatName = 'subcatcustom_id';
        }

        $catStatus = 1; //active category
        $subcatStatus = 1; //active subcategory

        //user level 4 is Document admin, user level 3 is Project Manager
        if ($userLevel == 4 || $userLevel == 3 && $userDepartment == 1) {
            //share to customer
            if ($request->shared == 1) {
                $data['shared'] = $request->shared;

                DB::table('project_report_images')->where('project_id',$projectId)->where('task_id',$taskId)->where('template_id',$templateId)->where('id',$id)->update($data);

                return redirect()->back()->with('alert-success','Laporan berhasil dikirimkan ke pelanggan.');
            }
            //getting request data
                $selectedImages = $request->selectedImage;
            if ($selectedImages !== null) {

                $selectedImagesCount = sizeof($selectedImages);
                $data = $request->except(['_token','_method','submit','selectedImage','subcat_name']);
                
                if ($selectedImagesCount > 0) {
                    //reset selected image
                    if ($userLevel == 4) {
                        $data['approver_id'] = null;
                        $data['approver_type'] = null;
                        $data['approved_at'] = null;
                    }else{
                        $data['approved_by_pm_at'] = null;
                    }
                    $data['status'] = $request->status;
                    $data['selected_image'] = 0;

                    //udpate data
                    DB::table('project_report_images')
                    ->where('project_id',$projectId)
                    ->where('task_id',$taskId)
                    ->where('template_id',$templateId)
                    ->where($subcatName,$subcatId)
                    ->update($data);
                    
                    //insert new selected image
                    if ($userLevel == 4) {
                        $data['approver_id'] = $userId;
                        $data['approver_type'] = $userType;
                        $data['approved_at'] = Carbon::now()->format('Y-m-d H:i:s');
                        //request by mr sultan in meeting dated 9 june 2021, IT Room. attended by: Mr Okta, Mr Sultan, Anto S, Ubay M, Gelar M. pm no need to approve report
                        $data['approved_by_pm_at'] = Carbon::now()->format('Y-m-d H:i:s');
                        //end
                    }else{
                        $data['approved_by_pm_at'] = Carbon::now()->format('Y-m-d H:i:s');
                    }
                    $data['status'] = $request->status;
                    $data['selected_image'] = 1;

                    foreach ($selectedImages as $p) {
                        DB::table('project_report_images')->where('id',$p)->update($data);
                    }
                }
                return redirect()->back()->with('alert-success','Laporan berhasil disetujui.');
            }else{
                ### reject technician image report
                if ($request->status == 1) {
                    $data['status'] = $request->status;
                    $data['approver_id'] = null;
                    $data['approver_type'] = null;

                    //reset selected image
                    $data['selected_image'] = 0;
                    
                    if ($userLevel == 3) {
                        $data['approved_by_pm_at'] = null;
                        $data['approved_at'] = null;

                        if ($request->all == 1) {
                            DB::table('project_report_images')->where('project_id',$projectId)->where('task_id',$taskId)->where('template_id',$templateId)->where('selected_image',1)->update($data);

                            return redirect()->route('user-projects-template.index','project_id='.$projectId.'&task_id='.$taskId)->with('alert-success','Semua laporan telah ditolak. QC wajib memilih ulang laporan foto.');
                        }else{
                            DB::table('project_report_images')->where('project_id',$projectId)->where('task_id',$taskId)->where('template_id',$templateId)->where('id',$id)->update($data);

                            return redirect()->route('user-projects-template.index','project_id='.$projectId.'&task_id='.$taskId)->with('alert-success','Laporan telah ditolak. QC wajib memilih ulang laporan foto.');
                        }

                    }else{
                        $data['submitted_at'] = null;
                        $data['approved_at'] = null;

                        DB::table('project_report_images')->where('project_id',$projectId)->where('task_id',$taskId)->where('template_id',$templateId)->where($subcatName,$subcatId)->update($data);

                        return redirect()->route('user-projects-template.index','project_id='.$projectId.'&task_id='.$taskId)->with('alert-success','Laporan telah ditolak. Teknisi wajib mengirim ulang laporan foto.');
                    }

                }elseif($request->status == 4){

                    $data['status'] = $request->status;
                    $data['approver_id'] = $userId;
                    $data['approver_type'] = $userType;
                    $data['approved_by_pm_at'] = Carbon::now()->format('Y-m-d H:i:s');

                    if ($request->all == 1) {
                        DB::table('project_report_images')->where('project_id',$projectId)->where('task_id',$taskId)->where('template_id',$templateId)->where('selected_image',1)->update($data);
                        
                        return redirect()->back()->with('alert-success','Semua laporan berhasil disetujui.');
                    }else{
                        DB::table('project_report_images')->where('project_id',$projectId)->where('task_id',$taskId)->where('template_id',$templateId)->where('id',$id)->where($subcatName,$subcatId)->update($data);

                        return redirect()->back()->with('alert-success','Laporan berhasil disetujui.');
                    }
                }
                return redirect()->back()->with('alert-danger','Tidak ada perubahan data. Anda harus memilih salah satu gambar.');
            }
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Images.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Project Images.');
    }

    /**
     * Report image
     */
    public function report(Request $request, $id)
    {
        $userId = Auth::user()->id;
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        $projectId = $request->project_id;
        $taskId = $request->task_id;
        $templateId = $id;
        $data['templateId'] = $id;

        $catStatus = 1; //active category
        $subcatStatus = 1; //active subcategory
        $commentStatus = 1; //accessable for PM PC AD level user
        $externalCommentStatus = 0; //accesable for both external and internal team
        
        ### check priviledge & getting the data
            if ($userLevel == 3) {
                $infoProjectTask = DB::table('projects_task as taskTableCheck')
                ->select([
                    'taskTableCheck.*',
                    DB::raw('(SELECT COUNT(project_report_images.approved_at) FROM project_report_images WHERE project_report_images.task_id = taskTableCheck.id AND project_report_images.template_id = '.$templateId.' AND project_report_images.approved_at IS NOT NULL) as approvedCount'),
                ])
                ->where('id',$taskId)
                ->where('project_id',$projectId)
                ->where('pm_id',$userId)
                ->first();
            }else{
                $infoProjectTask = DB::table('projects_task as taskTableCheck')
                ->select([
                    'taskTableCheck.*',
                    DB::raw('(SELECT COUNT(project_report_images.approved_at) FROM project_report_images WHERE project_report_images.task_id = taskTableCheck.id AND project_report_images.template_id = '.$templateId.' AND project_report_images.approved_at IS NOT NULL) as approvedCount'),
                ])
                ->where('id',$taskId)
                ->where('project_id',$projectId)
                ->where('qcd_id',$userId)
                ->first();
            }

            $data['infoProjectTask'] = $infoProjectTask;

            //old setting
            if (!isset($infoProjectTask) || $infoProjectTask->approvedCount < 1){
                //redirect to the current page with error
                if ($userLevel == 3) {
                    return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia atau gambar belum dipilih oleh QC Document.');
                }else{
                    return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia atau Anda belum memilih gambar untuk laporan.');
                }
            }
        ### check priviledge & getting the data end

        //user level 4 is Document admin, user level 3 is Project Manager
        if ($userLevel == 3 || $userLevel == 4 && $userDepartment == 1) {

            ### template data ###
                $data['projectTemplate'] = DB::table('project_report_template_selected as prt')
                ->select([
                    'prt.*',
                    DB::raw('(SELECT id FROM projects WHERE projects.id = prt.project_id) as project_id'),
                    DB::raw('(SELECT procat_id FROM projects WHERE projects.id = prt.project_id) as procat_id'),
                    DB::raw('(SELECT name FROM projects WHERE projects.id = prt.project_id) as project_name'),
                    DB::raw('(SELECT name FROM project_report_category WHERE project_report_category.id = '.$templateId.') as category_name'),
                ])
                ->where('project_id',$projectId)
                ->where('task_id',$taskId)
                ->where('template_id',$templateId)
                ->first();

                if ($data['projectTemplate'] == null) {
                    return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
                }

                $data['subcatIds'] = unserialize($data['projectTemplate']->subcat_id);
                $data['subcatcustomIds'] = unserialize($data['projectTemplate']->subcatcustom_id);

            ### template data end ###

            //////////////// project image start ///////////////

                //project
                if ($userLevel == 3) {
                    $data['project'] = DB::table('projects as proj')
                    ->select([
                        'proj.*',
                        //task
                        DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = '.$taskId.') as task_name'),
                        //partner & customer company
                        DB::raw('(SELECT name FROM clients WHERE clients.id = proj.customer_id AND proj.customer_id IS NOT NULL) as customer_name'),
                        DB::raw('(SELECT logo FROM clients WHERE clients.id = proj.customer_id AND proj.customer_id IS NOT NULL) as customer_logo'),
                        DB::raw('(SELECT name FROM clients WHERE clients.id = proj.partner_id AND proj.partner_id IS NOT NULL) as partner_name'),
                        DB::raw('(SELECT logo FROM clients WHERE clients.id = proj.partner_id AND proj.partner_id IS NOT NULL) as partner_logo'),
                        //partner & customer contact person
                        DB::raw('(SELECT firstname FROM customers WHERE customers.id = proj.customer_id AND proj.customer_id IS NOT NULL) as customer_pic_firstname'),
                        DB::raw('(SELECT lastname FROM customers WHERE customers.id = proj.customer_id AND proj.customer_id IS NOT NULL) as customer_pic_lastname'),
                        DB::raw('(SELECT firstname FROM customers WHERE customers.id = proj.partner_id AND proj.partner_id IS NOT NULL) as partner_pic_firstname'),
                        DB::raw('(SELECT lastname FROM customers WHERE customers.id = proj.partner_id AND proj.partner_id IS NOT NULL) as partner_pic_lastname'),
                    ])
                    ->where('id',$projectId)
                    ->where('pm_id',$userId)
                    ->where('deleted_at',null)
                    ->first();
                }else{

                    $data['project'] = DB::table('projects as proj')
                    ->select([
                        'proj.*',
                        //task
                        DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = '.$taskId.') as task_name'),
                        //partner & customer company
                        DB::raw('(SELECT name FROM clients WHERE clients.id = proj.customer_id AND proj.customer_id IS NOT NULL) as customer_name'),
                        DB::raw('(SELECT logo FROM clients WHERE clients.id = proj.customer_id AND proj.customer_id IS NOT NULL) as customer_logo'),
                        DB::raw('(SELECT name FROM clients WHERE clients.id = proj.partner_id AND proj.partner_id IS NOT NULL) as partner_name'),
                        DB::raw('(SELECT logo FROM clients WHERE clients.id = proj.partner_id AND proj.partner_id IS NOT NULL) as partner_logo'),
                        //partner & customer contact person
                        DB::raw('(SELECT firstname FROM customers WHERE customers.id = proj.customer_id AND proj.customer_id IS NOT NULL) as customer_pic_firstname'),
                        DB::raw('(SELECT lastname FROM customers WHERE customers.id = proj.customer_id AND proj.customer_id IS NOT NULL) as customer_pic_lastname'),
                        DB::raw('(SELECT firstname FROM customers WHERE customers.id = proj.partner_id AND proj.partner_id IS NOT NULL) as partner_pic_firstname'),
                        DB::raw('(SELECT lastname FROM customers WHERE customers.id = proj.partner_id AND proj.partner_id IS NOT NULL) as partner_pic_lastname'),
                    ])
                    ->where('id',$projectId)
                    ->where('pm_id',$infoProjectTask->pm_id)
                    ->where('deleted_at',null)
                    ->first();

                }

                //check privilege
                $projectCheck = $data['project'];
                if ($projectCheck == null) {
                    return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
                }

                //project category used for image folder placement
                $data['dataProjectCategory'] = DB::table('projects_category')->where('id',$data['projectTemplate']->procat_id)->first();

                //project pictures - subcat_id
                $data['dataProjectPictures'] = DB::table('project_report_images as pri')
                ->select([
                    'pri.*',
                    DB::raw('(SELECT name FROM project_report_subcategory WHERE project_report_subcategory.id = pri.subcat_id) as subcat_name')
                ])
                ->where('project_id',$projectId)
                ->where('task_id',$taskId)
                ->where('template_id',$templateId)
                ->where('subcat_id','!=',null)
                ->where('selected_image',1)
                ->get();

                //project pictures - subcatcustom_id
                $data['dataProjectPicturesCustom'] = DB::table('project_report_images as pri')
                ->select([
                    'pri.*',
                    DB::raw('(SELECT name FROM project_report_subcategory_customized WHERE project_report_subcategory_customized.id = pri.subcatcustom_id) as subcat_name')
                ])
                ->where('project_id',$projectId)
                ->where('task_id',$taskId)
                ->where('template_id',$templateId)
                ->where('subcatcustom_id','!=',null)
                ->where('selected_image',1)
                ->get();

                $dataSubCount = count($data['dataProjectPictures']);

                if ($dataSubCount > 0) {
                    $data['subcatName'] = 'subcat_id';
                }else{
                    $data['subcatName'] = 'subcatcustom_id';
                }

                $data['dataProjectPicturesStatus'] = DB::table('project_report_images as pri')
                ->select([
                    'pri.*',
                    DB::raw('COUNT(pri.approved_at) as countApproved'),
                    DB::raw('COUNT(pri.approved_by_pm_at) as countPMApproved'),
                    //comments
                    DB::raw('(SELECT COUNT(*) FROM project_report_images_comments WHERE project_report_images_comments.pri_id = pri.id) as commentsCount')
                ])
                ->where('task_id',$taskId)
                ->where('template_id',$templateId)
                ->orderBy('status','DESC')
                ->first();
            //////////////// Project image end ///////////////
            //////////////// Approver start ///////////////
                //data admin doc
                $data['dataApprover'] = DB::table('project_report_images as pri')
                ->select([
                    'pri.*',
                    //admin doc
                    DB::raw('(SELECT user_level FROM users WHERE users.id = pri.approver_id) as user_level'),
                    DB::raw('(SELECT name FROM users_level WHERE users_level.id = user_level) as title'),
                    DB::raw('(SELECT firstname FROM users WHERE users.id = pri.approver_id) as firstname'),
                    DB::raw('(SELECT lastname FROM users WHERE users.id = pri.approver_id) as lastname'),
                ])
                ->where('task_id',$taskId)
                ->where('approved_at','!=', null)
                ->first();

                //data pm
                $data['dataProjectManager'] = DB::table('project_report_images as pri')
                ->select([
                    'pri.*',
                    //project manager
                    DB::raw('(SELECT user_level FROM users WHERE users.id = '.$infoProjectTask->pm_id.') as user_level'),
                    DB::raw('(SELECT name FROM users_level WHERE users_level.id = user_level) as title'),
                    DB::raw('(SELECT firstname FROM users WHERE users.id = '.$infoProjectTask->pm_id.') as firstname'),
                    DB::raw('(SELECT lastname FROM users WHERE users.id = '.$infoProjectTask->pm_id.') as lastname'),
                ])
                ->where('task_id',$taskId)
                ->where('approved_by_pm_at','!=', null)
                ->first();

            //////////////// Approver end ///////////////
            
            //////////////// Report comments start ///////////////
            
                //internal comunications
                $data['dataComments'] = DB::table('project_report_images_comments')->where('status',$commentStatus)->where('task_id',$taskId)->orderBy('date', 'DESC')->get();

                //external communications
                $data['dataExternalComments'] = DB::table('projects_report_comments')->where('status',$externalCommentStatus)->where('task_id',$taskId)->orderBy('date', 'DESC')->get();
                $data['dataProjectReportCommentsCount'] = DB::table('projects_report_comments')->where('task_id',$taskId)->count();

                //commentators
                $data['users'] = DB::table('users')
                ->select([
                    'users.*',
                    DB::raw('(SELECT name FROM users_level WHERE users_level.id = users.user_level) as title')
                ])
                ->get();
                $data['customers'] = DB::table('customers')->get();

            //////////////// Report comments end ///////////////
            
            return view('user.project.report.report-images-selected',$data);
        }
    }
}
