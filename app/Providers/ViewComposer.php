<?php

namespace App\Providers;

use Illuminate\Contracts\View\View;
use App\User;
use App\Admin;
use Auth;
use DB;

class ViewComposer {

    protected $customerId, $userDatas, $adminDatas, $techDatas, $custDatas, $taskCount, $notifCount, $notificationDatas, $dashboardLink, $changePasswordLink, $profileLink, $formNotifEditAction, $formNotifUpdateAction, $linkClearNotif, $department, $navServices, $dataTechCount;

    /**
     * Create a new ViewComposer instance.
     */
    public function __construct()
    {
        //notification datas
        $notifStatus = 0;
        $taskStatus = 3; //3 done
        
        if(Auth::user() !== null){
            $userType = Auth::user()->user_type;
            $userDepartment = Auth::user()->department_id;
            $customerId = Auth::user()->id;
        }else{
            $userType = 'guest';
            $userDepartment = null;
            $customerId = null;
        }

        //check if user type is technician
        if($userType == 'tech'){
            $dataTechCount = DB::table('proc_tech_personal_data')->where('tech_id',$customerId)->count();
        }else{
            $dataTechCount = 0;
        }

        $companyInfo = DB::table('company_info')->first();
        $department = DB::table('department')->get();
        $navServices = DB::table('services')->get();

        $taskCount = DB::table('tasks')
            ->where('task_receiver_id',$customerId)
            ->where('receiver_type',$userType)
            ->where('receiver_department',$userDepartment)
            ->where('task_status','<',$taskStatus)
            ->count();

        $notificationCount = DB::table('notifications')
            ->where('receiver_id','=',$customerId)
            ->where('receiver_type','=',$userType)
            //->where('receiver_department',$userDepartment)
            //->whereOr('receiver_department',NULL)
            ->where('status','=',$notifStatus)
            ->count();

        $notificationDatas = DB::table('notifications')
            ->where('receiver_id','=',$customerId)
            ->where('receiver_type','=',$userType)
            //->where('receiver_department',$userDepartment)
            //->whereOr('receiver_department',NULL)
            ->where('status','=',$notifStatus)
            ->orderBy('date','DESC')
            ->get();
        
        $userDatas = DB::table('users')->get();
        $adminDatas = DB::table('admins')->get();
        $techDatas = DB::table('techs')->get();
        $custDatas = DB::table('customers')->get();

        if ($userType == 'admin') {
            #$userDatas = $adminDatas;
            $profileLink = 'profil-admin.index';
            $changePasswordLink = 'admin.edit.password';
            $formNotifEditAction = 'notifikasi-admin.edit';
            $formNotifUpdateAction = 'notifikasi-admin.update';
            $linkClearNotif = 'notifikasi-admin.update';
            $dashboardLink  = 'admin.dashboard';
        }elseif ($userType == 'tech') {
            #$userDatas = $techDatas;
            $profileLink = 'profil-tech.index';
            $changePasswordLink = 'tech.edit.password';
            $formNotifEditAction = 'notifikasi-tech.edit';
            $formNotifUpdateAction = 'notifikasi-tech.update';
            $linkClearNotif = 'notifikasi-tech.update';
            $dashboardLink  = 'tech.dashboard';
        }elseif ($userType == 'cust') {
            #$userDatas = $techDatas;
            $profileLink = 'profil-cust.index';
            $changePasswordLink = 'cust.edit.password';
            $formNotifEditAction = 'notifikasi-cust.edit';
            $formNotifUpdateAction = 'notifikasi-cust.update';
            $linkClearNotif = 'notifikasi-cust.update';
            $dashboardLink  = 'cust.dashboard';
        }else{
            #$userDatas = $userDatas;
            $profileLink = 'profil-user.index';
            $changePasswordLink = 'user.edit.password';
            $formNotifEditAction = 'notifikasi-user.edit';
            $formNotifUpdateAction = 'notifikasi-user.update';
            $linkClearNotif = 'notifikasi-user.update';
            $dashboardLink  = 'user.index';
        }
        
        $this->companyInfo = $companyInfo;
        $this->department = $department;
        $this->navServices = $navServices;
        $this->customerId = $customerId;
        $this->userDatas = $userDatas;
        $this->adminDatas = $adminDatas;
        $this->techDatas = $techDatas;
        $this->custDatas = $custDatas;
        $this->taskCount = $taskCount;
        $this->notifCount = $notificationCount;
        $this->notifDatas = $notificationDatas;
        $this->changePasswordLink = $changePasswordLink;
        $this->profileLink = $profileLink;
        $this->formNotifEditAction = $formNotifEditAction;
        $this->formNotifUpdateAction = $formNotifUpdateAction;
        $this->linkClearNotif = $linkClearNotif;
        $this->dashboardLink = $dashboardLink;
        $this->dataTechCount = $dataTechCount;
    }

    /**
     * Compose the view.
     *
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('companyInfo', $this->companyInfo);
        $view->with('department', $this->department);
        $view->with('navServices', $this->navServices);
        $view->with('customerId', $this->customerId);
        $view->with('userDatas', $this->userDatas);
        $view->with('adminDatas', $this->adminDatas);
        $view->with('techDatas', $this->techDatas);
        $view->with('custDatas', $this->custDatas);
        $view->with('taskCount', $this->taskCount);
        $view->with('notifCount', $this->notifCount);
        $view->with('notifDatas', $this->notifDatas);
        $view->with('profileLink', $this->profileLink);
        $view->with('changePasswordLink', $this->changePasswordLink);
        $view->with('formNotifEditAction', $this->formNotifEditAction);
        $view->with('formNotifUpdateAction', $this->formNotifUpdateAction);
        $view->with('linkClearNotif', $this->linkClearNotif);
        $view->with('dashboardLink', $this->dashboardLink);
        $view->with('dataTechCount', $this->dataTechCount);
    }

}
