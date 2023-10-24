<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
#Auth::routes(['register' => false]);
Auth::routes(['verify' => true]);

//tech register
Route::get('/tech-register', 'Auth\TechRegisterController@showRegisterForm')->name('tech.registerform');
Route::post('/tech-register', 'Auth\TechRegisterController@register')->name('tech.register');

Route::get('/draft', 'IndexController@draft')->name('draft');

Route::get('/', 'IndexController@index')->name('home');
Route::get('/our-service/{slug}', 'IndexController@ourServices')->name('our-services');
Route::get('/tentang-perusahaan', 'IndexController@about')->name('about');
Route::get('/sejarah-perusahaan', 'IndexController@companyHistory')->name('company-history');
Route::get('/budaya-perusahaan', 'IndexController@corporateCulture')->name('corporate-culture');
Route::get('/blog', 'IndexController@blog')->name('blog');
Route::get('/blog/{slug}', 'IndexController@blogDetail')->name('blog.detail');

Route::get('/home', 'IndexController@home')->name('unverified.dashboard');
Route::get('/verify','Auth\RegisterController@verifyUser')->name('verify.user');
Route::get('/adminverify','Auth\AdminRegisterController@verifyUser')->name('verify.admin');

Route::prefix('user')->middleware('verified')->group(function(){
    // test
    Route::get('/page-test', 'User\HomeController@pageTest')->name('test');
    // test

    //general menus
        Route::get('/', 'User\HomeController@index')->name('user.index');
        Route::get('/all-members-by-department', 'User\HomeController@allMembersByDepartment')->name('user.allmembersdepartment');
        Route::get('/ubah-password', 'User\HomeController@editPassword')->name('user.edit.password');
        Route::post('/ubah-password', 'User\HomeController@changePassword')->name('user.ubah.password');
        Route::get('/get-user-list','User\HomeController@getUserList');

        Route::resource('/attendance','User\AttendanceController')->name('*','user.attendance');
        Route::resource('/user-clockin','User\ClockinController')->name('*','user.clockin');
        Route::resource('/user-clockout','User\ClockoutController')->name('*','user.clockout');    
        Route::resource('/notifikasi-user','User\UserNotificationsController')->name('*','user.notifications');
        Route::resource('/user-bug-report','User\BugReportController')->name('*','user.bugreport');
        Route::resource('/profil-user','User\UserProfileController')->name('*','user.profile');
        Route::resource('/troubleshooting','User\TroubleshootingController')->name('*','user.troubleshooting');
        Route::resource('/troubleshooting-image','User\TroubleshootingImageController')->name('*','user.troubleshooting.image');
        Route::resource('/user-troubleshooting-comments','User\TroubleshootingCommentController')->name('*','user.troubleshooting.comment');
        Route::resource('/user-troubles-comment-file','User\TroubleshootingCommentFileController')->name('*','user.troublescommentfile');
        Route::get('/user-troubleshooting-dashboard/{id}', 'User\HomeController@troubleshootingDetail')->name('user.troubleshootingdetail');
        //minutes
            Route::post('/user-minutes/customreport', [
                'as' => 'user-minutes.customreport',
                'uses' => 'User\UserMinutesController@customReport'
            ]);
            Route::resource('/user-minutes','User\UserMinutesController')->name('*','user.minutes');
            
            Route::post('/user-minutes-report/customreport', [
                'as' => 'user-minutes-report.customreport',
                'uses' => 'User\UserMinutesReportController@customReport'
            ]);
            Route::post('/user-minutes-report/customreportlintaslog', [
                'as' => 'user-minutes-report.customreportlintaslog',
                'uses' => 'User\UserMinutesReportController@customReportLintaslog'
            ]);
            Route::resource('/user-minutes-report','User\UserMinutesReportController')->name('*','user.minutesreport');
            Route::resource('/user-minutes-category','User\UserMinutesCategoryController')->name('*','user.minutescategory');

    //project department
        Route::get('/user-projects/dashboard', [
            'as' => 'user-projects.dashboard',
            'uses' => 'User\UserProjectController@dashboard'
        ]);
        Route::get('/user-projects/progress/{id}', [
            'as' => 'user-projects.progress',
            'uses' => 'User\UserProjectController@progress'
        ]);
        Route::resource('/user-projects','User\UserProjectController')->name('*','user.projects');
        Route::resource('/user-projects-report','User\UserProjectReportController')->name('*','user.projectsreport');
        Route::resource('/user-projects-report-vt','User\Project\UserProjectReportVtController')->name('*','user.projectsreportvt'); //vt - version two
        Route::resource('/user-projects-report-format-t','User\Project\UserProjectReportFormatTitleController')->name('*','user.projectsreportformattitle');
        Route::resource('/user-projects-report-format-st','User\Project\UserProjectReportFormatSubTitleController')->name('*','user.projectsreportformatsubtitle');
        

        Route::resource('/user-projects-report-qc','User\Project\UserQcProjectReportController')->name('*','user.projectsreportqc');
        Route::resource('/user-projects-report-qc-comments','User\Project\UserQcProjectReportCommentsController')->name('*','user.projectsreportqccomments');
        Route::resource('/user-projects-report-file','User\Project\UserProjectReportFileController')->name('*','user.projectsreportfile');
        
        Route::resource('/user-projects-category','User\UserProjectCategoryController')->name('*','user.projectscategory');
        Route::resource('/user-projects-subcategory','User\UserProjectSubcategoryController')->name('*','user.projectssubcategory');
        Route::resource('/subcategory-customized','User\UserProjectSubcategoryCustomizedController')->name('*','user.subcategorycustomized');

        Route::resource('/user-projects-expense','User\UserProjectExpenseController')->name('*','user.projectsexpense');
        Route::resource('/user-projects-expense-report','User\UserProjectExpenseReportController')->name('*','user.projectsexpensereport');
        Route::resource('/user-projects-ca','User\UserProjectCashAdvanceController')->name('*','user.projectcashadvance');
        Route::resource('/user-projects-spp','User\UserProjectRequestPaymentController')->name('*','user.projectrequestpayment');

        Route::get('/user-project-tool/report', [
            'as' => 'user-project-tool.report',
            'uses' => 'User\UserProjectToolController@report'
        ]);
        Route::resource('/user-project-tool','User\UserProjectToolController')->name('*','user.projecttool');
        Route::resource('/user-project-payment-summary','User\UserProjectPaymentSummaryController')->name('*','user.paymentsummary');

        Route::resource('/user-projects-task','User\UserProjectTaskController')->name('*','user.projectstask');
        Route::resource('/user-projects-template','User\UserProjectTemplateController')->name('*','user.projectstemplate');
        Route::resource('/user-projects-template-select','User\UserProjectTemplateSelectController')->name('*','user.projectstemplateselect');

        Route::get('user-projects-image/report/{id}', [
            'as' => 'user-projects-image.report',
            'uses' => 'User\UserProjectImageController@report'
        ]);
        Route::resource('/user-projects-image','User\UserProjectImageController')->name('*','user.projectsimage');

        Route::resource('/user-projects-image-comments','User\UserProjectImageCommentsController')->name('*','user.projectsimagecomments');
        Route::resource('/user-projects-report-comments','User\UserProjectReportCommentsController')->name('*','user.projectsreportcomments');

        Route::resource('/user-pr','User\PurchaseRequisitionController')->name('*','user.pr');

    //task
        Route::resource('/task-user','User\TaskUserController')->name('*','user.tasks');
        Route::resource('/task-user-comment','User\TaskUserCommentController')->name('*','user.task.comments');
        Route::resource('/task-user-comment-file','User\TaskUserCommentFileController')->name('*','user.taskcommentfile');

    //collaboration index
        Route::resource('/user-collaboration','User\Task\Collaboration\TaskCollaborationController')->name('*','user.taskscollaboration');

    //multi department collaboration task
        Route::resource('/user-task-leaders','User\Task\Collaboration\MultiDepartment\TaskLeaderController')->name('*','user.tasksleader');
        Route::resource('/user-task-leaders-pic','User\Task\Collaboration\MultiDepartment\TaskLeaderPicController')->name('*','user.tasksleaderpic');
        Route::resource('/user-task-leaders-file','User\Task\Collaboration\MultiDepartment\TaskLeaderFileController')->name('*','user.tasksleaderfile');
        Route::resource('/user-task-leaders-comment','User\Task\Collaboration\MultiDepartment\TaskLeaderCommentController')->name('*','user.tasksleadercomments');
        Route::resource('/user-task-leaders-comment-file','User\Task\Collaboration\MultiDepartment\TaskLeaderCommentFileController')->name('*','user.tasksleadercommentsfile');
        Route::resource('/user-task-leaders-todo','User\Task\Collaboration\MultiDepartment\TaskLeaderTodoController')->name('*','user.tasksleadertodo');
        Route::resource('/user-task-leaders-todo-file','User\Task\Collaboration\MultiDepartment\TaskLeaderTodoFileController')->name('*','user.tasksleadertodofile');
        Route::resource('/user-task-leaders-todo-comment','User\Task\Collaboration\MultiDepartment\TaskLeaderTodoCommentController')->name('*','user.tasksleadertodocomment');

    //intern department collaboration task
        Route::resource('/user-task-internal','User\Task\Collaboration\Internal\TaskInternalController')->name('*','user.tasksinternal');
        Route::resource('/user-task-internal-pic','User\Task\Collaboration\Internal\TaskInternalPicController')->name('*','user.tasksinternalpic');
        Route::resource('/user-task-internal-file','User\Task\Collaboration\Internal\TaskInternalFileController')->name('*','user.tasksinternalfile');
        Route::resource('/user-task-internal-comment','User\Task\Collaboration\Internal\TaskInternalCommentController')->name('*','user.tasksinternalcomments');
        Route::resource('/user-task-internal-comment-file','User\Task\Collaboration\Internal\TaskInternalCommentFileController')->name('*','user.tasksinternalcommentsfile');
        Route::resource('/user-task-internal-todo','User\Task\Collaboration\Internal\TaskInternalTodoController')->name('*','user.tasksinternaltodo');
        Route::resource('/user-task-internal-todo-file','User\Task\Collaboration\Internal\TaskInternalTodoFileController')->name('*','user.tasksinternaltodofile');
        Route::resource('/user-task-internal-todo-comment','User\Task\Collaboration\Internal\TaskInternalTodoCommentController')->name('*','user.tasksinternaltodocomment');
    
    //GA
        Route::post('/user-covid-test/uploadImage', [
            'as' => 'user-covid-test.uploadImage',
            'uses' => 'User\UserCovidTestRequestController@uploadImage'
        ]);
        Route::resource('/user-covid-test','User\UserCovidTestRequestController')->name('*','user.covidtestuploadimage');
        Route::resource('/user-wfh-to-wfo','User\GA\Covid\WfhToWfoController')->name('*','user.wfhtowforequest');
    // Satgas
        Route::resource('/user-covid-slider','User\Satgas\Covid\UserSatgasSliderController')->name('*','user.covidslider');

    //User Procurement
        Route::resource('/user-proc-question','User\Proc\UserQuestionController')->name('*','user.proc.question');
        Route::resource('/user-proc-question-category','User\Proc\UserQuestionCategoryController')->name('*','user.proc.questioncategory');
        Route::resource('/user-proc-test-psychology','User\Proc\ProcPsychologyQuestions')->name('*','user.proc.testpsychology');
        Route::resource('/user-test-psychology-answers','User\Proc\ProcPsychologyAnswers')->name('*','user.proc.testpsychologyanswers');
        Route::resource('/user-test-psychology-analisys','User\Proc\ProcPsychologyAnalisys')->name('*','user.proc.testpsychologyanalisys');
        Route::resource('/user-proc-assesment-question','User\Proc\ProcAssesmentQuestions')->name('*','user.proc.assesmentquestion');
        Route::resource('/user-proc-assesment-answer','User\Proc\ProcAssesmentAnswers')->name('*','user.proc.assesmentanswer');
        Route::resource('/user-assesment-question-cat','User\Proc\ProcAssesmentQuestionsCategories')->name('*','user.proc.assesmentquestioncategory');
        Route::resource('/user-proc-competency-test','User\Proc\ProcCompetencyTest')->name('*','user.proc.competencytest');
        Route::resource('/user-tech','User\Proc\TechController')->name('*','user.tech');

    //IT
        Route::resource('/user-apps-dev-logs','User\IT\AppsDevelopmentLogsController')->name('*','user.appsdevlogs');

    //coadmin
    Route::resource('/user-teamuser','User\TeamUserController')->name('*','user.teamuser');
    Route::resource('/user-teamusertitle','User\TeamUserTitleController')->name('*','user.teamusertitle');
});
Route::match(array('GET','POST'),'/logout', 'Auth\LoginController@userLogout')->name('user.logout');

Route::prefix('tech')->group(function(){
    // testing
        Route::get('/kirimemail-tech','Tech\TechExpensesEmailController@index')->name('tech.emailexpense');
    // testing
    Route::get('/login', 'Auth\TechLoginController@showLoginForm')->name('tech.login');
    Route::post('/login', 'Auth\TechLoginController@login')->name('tech.login.submit');
    Route::match(array('GET','POST'),'/techverify', 'Auth\TechRegisterController@verifyUser')->name('tech.verify');
    Route::get('/tech-get-city-list','Tech\TechController@getCityList');
    Route::get('/get-user-list','Tech\TechController@getUserList');
    Route::resource('/notifikasi-tech','Tech\TechNotificationsController')->name('*','tech.notifications.index');
    //general menus
        Route::get('/', 'Tech\TechController@index')->name('tech.dashboard');
        Route::get('/payment-procedure', 'Tech\TechController@paymentProcedure')->name('tech.paymentprocedure');
        Route::get('/manual/{id}', 'Tech\TechController@manualDetail')->name('tech.manualdetail');
        Route::resource('/tech-bug-report','Tech\TechBugReportController')->name('*','tech.bugreport');
        Route::get('/ubah-password', 'Tech\TechController@editPassword')->name('tech.edit.password');
        Route::post('/ubah-password', 'Tech\TechController@changePassword')->name('tech.ubah.password');
    //procurement department
        Route::resource('/tech-input-data-diri','Tech\TechInputPersonalData')->name('*','tech.inputdatadiri');
        Route::resource('/tech-input-data-keluarga','Tech\TechFamilyController')->name('*','tech.inputdatakeluarga');
        Route::resource('/tech-input-data-pendidikan','Tech\TechEducationController')->name('*','tech.inputdatapendidikan');
        Route::resource('/tech-input-doc','Tech\TechInputDocController')->name('*','tech.inputdoc');
        Route::resource('/tech-test-training','Tech\TechTestTrainingController')->name('*','tech.testtraining');
        Route::resource('/tech-test-training-category','Tech\TechTestTrainingByCategoryController')->name('*','tech.testtrainingcat');
        Route::resource('/tech-psychology-test','Tech\TechTestPsychologyController')->name('*','tech.psychologytest');
        Route::resource('/tech-competency-test','Tech\TechTestCompetencyController')->name('*','tech.competencytest');
        Route::resource('/tech-assessment-test','Tech\TechTestAssessmentController')->name('*','tech.assessmenttest');
        Route::resource('/tech-proc-video','Tech\TechProcVideoController')->name('*','tech.procvideo');
    //project department
        Route::get('/expenses-tech/report', [
            'as' => 'expenses-tech.report',
            'uses' => 'Tech\TechExpensesController@report'
        ]);
        Route::resource('/expenses-tech','Tech\TechExpensesController')->name('*','tech.expenses');
        Route::resource('/expenses-image-upload','Tech\TechExpensesImagesController')->name('*','tech.expensesimageupload');

        Route::get('/report-tech/report', [
            'as' => 'report-tech.report',
            'uses' => 'Tech\TechReportController@report'
        ]);
        Route::resource('/report-tech','Tech\TechReportController')->name('*','tech.report');
        Route::resource('/tech-report-qc','Tech\Project\TechQcProjectReportController')->name('*','tech.projectsreportqc');
        Route::resource('/tech-report-file','Tech\Project\TechFileProjectReportController')->name('*','tech.projectsreportfile');

        Route::resource('/minutes-tech','Tech\TechMinutesController')->name('*','tech.minutes');
        Route::resource('/profil-tech','Tech\TechProfileController')->name('*','tech.profile');
        Route::resource('/tech-troubleshooting','Tech\TroubleshootingController')->name('*','tech.troubleshooting');
        Route::resource('/tech-troubleshooting-comments','Tech\TroubleshootingCommentController')->name('*','tech.troubleshooting.comment');
        Route::resource('/tech-troubles-comment-file','Tech\TroubleshootingCommentFileController')->name('*','tech.troublescommentfile');
        Route::get('/tech-troubleshooting-dashboard/{id}', 'Tech\TechController@troubleshootingDetail')->name('tech.troubleshootingdetail');

        Route::get('/project-tech/progress/{id}', [
            'as' => 'project-tech.progress',
            'uses' => 'Tech\TechProjectController@progress'
        ]);
        Route::get('/project-tech/dashboard', [
            'as' => 'project-tech.dashboard',
            'uses' => 'Tech\TechProjectController@dashboard'
        ]);
        Route::resource('/project-tech','Tech\TechProjectController')->name('*','tech.project');

        Route::get('/project-tool-tech/report', [
            'as' => 'project-tool-tech.report',
            'uses' => 'Tech\TechProjectToolController@report'
        ]);
        Route::resource('/project-tool-tech','Tech\TechProjectToolController')->name('*','tech.projecttool');
        
        Route::resource('/project-ca-tech','Tech\TechProjectCashAdvanceController')->name('*','tech.projectca');
        Route::resource('/project-image-comment-tech','Tech\TechReportImageCommentsController')->name('*','tech.reportimagecomments');
        Route::resource('/project-text-comment-tech','Tech\TechReportTextCommentsController')->name('*','tech.reporttextcomments');

        Route::resource('/payment-summary-tech','Tech\TechProjectPaymentSummaryController')->name('*','tech.paymentsummary');

        Route::match(array('GET','POST','PUT'),'/project-report-tool','Tech\TechProjectReportController@saveToolReport')->name('tech.projectreporttool');
        Route::match(array('GET','POST','PUT'),'/project-report-tool-show','Tech\TechProjectReportController@viewToolReport')->name('tech.projectreporttoolshow');
        Route::match(array('GET','POST','PUT'),'/project-report-expense','Tech\TechProjectReportController@saveExpenseReport')->name('tech.projectreportexpense');
        Route::match(array('GET','POST','PUT'),'/project-report-expense-show','Tech\TechProjectReportController@viewExpenseReport')->name('tech.projectreportexpenseshow');

    Route::match(array('GET','POST'),'/logout', 'Auth\TechLoginController@logout')->name('tech.logout');
});

Route::prefix('cust')->group(function(){
    Route::get('/login', 'Auth\CustLoginController@showLoginForm')->name('cust.login');
    Route::post('/login', 'Auth\CustLoginController@login')->name('cust.login.submit');
    Route::get('/', 'Cust\CustController@index')->name('cust.dashboard');

    Route::get('/cust-projects/dashboard', [
        'as' => 'cust-projects.dashboard',
        'uses' => 'Cust\CustProjectController@dashboard'
    ]);
    Route::resource('/cust-projects','Cust\CustProjectController')->name('*','cust.projects');

    Route::resource('/cust-projects-image-report','Cust\CustProjectImageReportController')->name('*','cust.projectsimagereport');
    Route::resource('/cust-projects-report-qc','Cust\Project\CustQcProjectReportController')->name('*','cust.projectsreportqc');
    Route::resource('/cust-projects-report-qc-comments','Cust\Project\CustQcProjectReportCommentsController')->name('*','cust.projectsreportqccomments');

    Route::get('/ubah-password', 'Cust\CustController@editPassword')->name('cust.edit.password');
    Route::post('/ubah-password', 'Cust\CustController@changePassword')->name('cust.ubah.password');
    
    Route::resource('/profil-cust','Cust\CustProfileController')->name('*','cust.profile');
    Route::resource('/notifikasi-cust','Cust\CustNotificationsController')->name('*','cust.notifications.index');
    Route::match(array('GET','POST'),'/logout', 'Auth\CustLoginController@logout')->name('cust.logout');
});

Route::prefix('admin')->group(function(){
    Route::get('/login', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
    Route::post('/login', 'Auth\AdminLoginController@login')->name('admin.login.submit');
    Route::get('/', 'Admin\AdminController@index')->name('admin.dashboard');
    Route::get('/all-members-by-department', 'Admin\AdminController@allMembersByDepartment')->name('admin.allmembersdepartment');
    Route::get('/get-user-list','Admin\AdminController@getUserList');
    Route::get('/get-user-list-all','Admin\AdminController@getUserListAll');
    Route::resource('/admin-attendance','Admin\AdminAttendanceController')->name('*','admin.attendance');
    Route::resource('/admin-covid-test','Admin\AdminCovidTestRequestController')->name('*','admin.covidtest');
    //general menus
        Route::get('/ubah-password', 'Admin\AdminController@editPassword')->name('admin.edit.password');
        Route::post('/ubah-password', 'Admin\AdminController@changePassword')->name('admin.ubah.password');
        Route::resource('/profil-admin','Admin\AdminProfileController')->name('*','admin.profile');
        Route::resource('/notifikasi-admin','Admin\AdminNotificationsController')->name('*','admin.notifications.index');
        Route::resource('/admin-troubleshooting-comments','Admin\TroubleshootingCommentController')->name('*','admin.troubleshooting.comment');
        Route::resource('/admin-troubles-comment-file','Admin\TroubleshootingCommentFileController')->name('*','admin.troublescommentfile');
        Route::get('/admin-troubleshooting-dashboard/{id}', 'Admin\AdminController@troubleshootingDetail')->name('admin.troubleshootingdetail');
        Route::resource('/client','Admin\ClientController')->name('*','admin.client');
        Route::resource('/client-contact-person','Admin\ClientContactPersonController')->name('*','admin.clientcontact');
        //minutes
            Route::post('/admin-minutes/customreport', [
                'as' => 'admin-minutes.customreport',
                'uses' => 'Admin\AdminMinutesController@customReport'
            ]);
            Route::resource('/admin-minutes','Admin\AdminMinutesController')->name('*','admin.minutes');
            Route::resource('/admin-minutes-category','Admin\AdminMinutesCategoryController')->name('*','admin.minutescategory');

            Route::post('/admin-minutes-report/customreport', [
                'as' => 'admin-minutes-report.customreport',
                'uses' => 'Admin\AdminMinutesReportController@customReport'
            ]);
            Route::post('/admin-minutes-report/customreportlintaslog', [
                'as' => 'admin-minutes-report.customreportlintaslog',
                'uses' => 'Admin\AdminMinutesReportController@customReportLintaslog'
            ]);
            Route::resource('/admin-minutes-report','Admin\AdminMinutesReportController')->name('*','admin.minutesreport');

    //project department
        Route::get('/admin-projects/progress/{id}', [
            'as' => 'admin-projects.progress',
            'uses' => 'Admin\ProjectController@progress'
        ]);
        Route::get('/admin-projects/dashboard', [
            'as' => 'admin-projects.dashboard',
            'uses' => 'Admin\ProjectController@dashboard'
        ]);
        Route::resource('/admin-projects-template','Admin\ProjectTemplateController')->name('*','admin.projectstemplate');
        Route::resource('/admin-projects-task','Admin\ProjectTaskController')->name('*','admin.projectstask');
        Route::resource('/admin-projects','Admin\ProjectController')->name('*','admin.projects');
        Route::resource('/admin-projects-category','Admin\ProjectCategoryController')->name('*','admin.projectscategory');
        Route::resource('/admin-projects-subcategory','Admin\ProjectSubcategoryController')->name('*','admin.projectssubcategory');
        Route::resource('/admin-wo','Admin\WorkOrderController')->name('*','admin.wo');
        
        
        Route::resource('/admin-projects-report-qc','Admin\Project\AdminQcProjectReportController')->name('*','admin.projectsreportqc');


    //task
        Route::resource('/task','Admin\TaskController')->name('*','admin.tasks');
        Route::resource('/task-comment','Admin\TaskCommentController')->name('*','admin.taskcomments');
        Route::resource('/task-comment-file','Admin\TaskCommentFileController')->name('*','admin.taskcommentfile');
    //collaboration index
        Route::resource('/admin-collaboration','Admin\Task\Collaboration\TaskCollaborationController')->name('*','admin.taskscollaboration');
    //multi department collaboration task
        Route::resource('/task-leaders','Admin\Task\Collaboration\MultiDepartment\TaskLeaderController')->name('*','admin.tasksleader');
        Route::resource('/task-leaders-todo','Admin\Task\Collaboration\MultiDepartment\TaskLeaderTodoController')->name('*','admin.tasksleadertodo');
        Route::resource('/task-leaders-todo-file','Admin\Task\Collaboration\MultiDepartment\TaskLeaderTodoFileController')->name('*','admin.tasksleadertodofile');
        Route::resource('/task-leaders-todo-comment','Admin\Task\Collaboration\MultiDepartment\TaskLeaderTodoCommentController')->name('*','admin.tasksleadertodocomment');
        Route::resource('/task-leaders-pic','Admin\Task\Collaboration\MultiDepartment\TaskLeaderPicController')->name('*','admin.tasksleaderpic');
        Route::resource('/task-leaders-file','Admin\Task\Collaboration\MultiDepartment\TaskLeaderFileController')->name('*','admin.tasksleaderfile');
        Route::resource('/task-leaders-comment','Admin\Task\Collaboration\MultiDepartment\TaskLeaderCommentController')->name('*','admin.tasksleadercomments');
        Route::resource('/task-leaders-comment-file','Admin\Task\Collaboration\MultiDepartment\TaskLeaderCommentFileController')->name('*','admin.tasksleadercommentsfile');

    //intern department collaboration task
        Route::resource('/task-internal','Admin\Task\Collaboration\Internal\TaskInternalController')->name('*','admin.tasksinternal');
        Route::resource('/task-internal-pic','Admin\Task\Collaboration\Internal\TaskInternalPicController')->name('*','admin.tasksinternalpic');
        Route::resource('/task-internal-todo','Admin\Task\Collaboration\Internal\TaskInternalTodoController')->name('*','admin.tasksinternaltodo');
        Route::resource('/task-internal-todo-file','Admin\Task\Collaboration\Internal\TaskInternalTodoFileController')->name('*','admin.tasksinternaltodofile');
        Route::resource('/task-internal-todo-comment','Admin\Task\Collaboration\Internal\TaskInternalTodoCommentController')->name('*','admin.tasksinternaltodocomment');
        Route::resource('/task-internal-file','Admin\Task\Collaboration\Internal\TaskInternalFileController')->name('*','admin.tasksinternalfile');
        Route::resource('/task-internal-comment','Admin\Task\Collaboration\Internal\TaskInternalCommentController')->name('*','admin.tasksinternalcomment');
        Route::resource('/task-internal-comment-file','Admin\Task\Collaboration\Internal\TaskInternalCommentFileController')->name('*','admin.tasksinternalcommentsfile');

    //Procurement
        Route::resource('/admin-proc-question','Admin\Proc\QuestionController')->name('*','admin.proc.question');
        Route::resource('/admin-proc-question-category','Admin\Proc\AdminQuestionCategoryController')->name('*','admin.proc.questioncategory');
        Route::resource('/admin-proc-assesment-question','Admin\Proc\ProcAssesmentQuestions')->name('*','admin.proc.assesmentquestion');
        Route::resource('/admin-proc-assesment-answer','Admin\Proc\ProcAssesmentAnswers')->name('*','admin.proc.assesmentanswer');
        Route::resource('/admin-assesment-question-cat','Admin\Proc\ProcAssesmentQuestionsCategories')->name('*','admin.proc.assesmentquestioncategory');
        Route::resource('/admin-proc-test-psychology','Admin\Proc\ProcPsychologyQuestions')->name('*','admin.proc.testpsychology');
        Route::resource('/admin-test-psychology-answers','Admin\Proc\ProcPsychologyAnswers')->name('*','admin.testpsychologyanswers');
        Route::resource('/admin-test-psychology-analisys','Admin\Proc\ProcPsychologyAnalisys')->name('*','admin.proc.testpsychologyanalisys');
        Route::resource('/admin-test-psychology-result','Admin\Proc\ProcPsychologyTestResultController')->name('*','admin.testpsychologyresult');
        Route::resource('/admin-proc-competency-test','Admin\Proc\ProcCompetencyTest')->name('*','admin.proc.competencytest');
        Route::resource('/admin-tech-rating','Admin\TechRatingController')->name('*','admin.techrating');
        Route::resource('/admin-tech','Admin\Proc\TechController')->name('*','admin.tech');
        //purchase requisition
            Route::resource('/admin-pr','Admin\PurchaseRequisitionController')->name('*','admin.pr');
    //GA
        Route::resource('/admin-wfh-to-wfo','Admin\WfhToWfoController')->name('*','admin.wfhtowforequest');
    // Satgas
        Route::resource('/admin-covid-slider','Admin\Satgas\Covid\AdminSatgasSliderController')->name('*','admin.covidslider');
    //Lintaslog
        Route::resource('/department-lintaslog','Admin\Lintaslog\DepartmentLintaslogController')->name('*','admin.departmentlintaslog');
    //IT
        Route::resource('/admin-cities','Admin\IT\CitiesController')->name('*','admin.cities');
        Route::resource('/flash-messages','Admin\IT\SendFlashMessageController')->name('*','admin.flashmessages');
        Route::resource('/apps-dev-logs','Admin\IT\AppsDevelopmentLogsController')->name('*','admin.appsdevlogs');
        Route::resource('/apps-update','Admin\IT\AppsUpdateController')->name('*','admin.appsupdate');
        Route::resource('/performance-summary','Admin\IT\PerformanceSummaryController')->name('*','admin.performancesummary');
        Route::resource('/admin-user-acceptance-test','Admin\UserAcceptanceTestController')->name('*','admin.useracceptancetest');
        Route::post('/apps-dev-logs-report/customreport', [
            'as' => 'apps-dev-logs-report.customreport',
            'uses' => 'Admin\AdminMinutesReportController@customReport'
        ]);
        Route::resource('/apps-dev-logs-report','Admin\IT\AppsDevelopmentLogsReportController')->name('*','admin.appsdevlogsreport');

    //superadmin only
        Route::middleware('can:accessAdminpanel')->group(function() {
            Route::resource('/team','Admin\TeamController')->name('*','admin.team');
            Route::resource('/department','Admin\DepartmentController')->name('*','admin.department');
            Route::resource('/company-info','Admin\CompanyInfoController')->name('*','admin.companyinfo');
            Route::resource('/slider','Admin\SliderController')->name('*','admin.slider');

            Route::resource('/service','Admin\ServiceController')->name('*','admin.service');
            Route::resource('/service-image','Admin\ServiceImageController')->name('*','admin.service.image');
            // Satgas
                Route::resource('/slider','Admin\SliderController')->name('*','admin.slider');
        });
        Route::resource('/admin-blog','Admin\AdminBlogController')->name('*','admin.blog');
        Route::resource('/teamuser','Admin\TeamUserController')->name('*','admin.teamuser');
        Route::resource('/teamusertitle','Admin\TeamUserTitleController')->name('*','admin.teamusertitle');
    
    Route::match(array('GET','POST'),'/logout', 'Auth\AdminLoginController@logout')->name('admin.logout');
});
?>