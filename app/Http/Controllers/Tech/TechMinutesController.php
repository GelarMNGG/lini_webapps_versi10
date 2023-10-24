<?php

namespace App\Http\Controllers\Tech;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Auth;
use DB;

class TechMinutesController extends Controller
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
    public function index(Request $request)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;

        $taskId = $request->task_id;

        //check priviledge
        $projectTask = DB::table('projects_task as pt')
        ->select([
            'pt.*',
            DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
        ])
        ->where('id',$taskId)->where('tech_id',$userId)->first();

        if (isset($projectTask)) {

            $data['projectTask'] = $projectTask;
            //minutes datas
                $data['techMinutes'] = DB::table('project_minutes as pm')
                ->select([
                    'pm.*',
                    DB::raw('(SELECT number FROM projects_task WHERE projects_task.id = pm.task_id) as task_number'),
                    DB::raw('(SELECT COUNT(image) FROM project_minutes_images WHERE project_minutes_images.projmin_id = pm.id) as images_count'),
                ])
                ->where('pm.task_id',$taskId)
                ->paginate(10);
            //minutes images datas
                $data['techMinutesImages'] = DB::table('project_minutes_images')
                ->where('task_id',$taskId)
                ->where('publisher_id',$userId)
                ->where('publisher_type',$userType)
                ->orderBy('id','DESC')
                //->limit(3)
                ->get();

            return view('tech.minutes.index',$data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Aktivitas.');
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

        $taskId = $request->task_id;

        //check priviledge
        $projectTask = DB::table('projects_task as pt')
        ->select([
            'pt.*',
            DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
        ])
        ->where('id',$taskId)->where('tech_id',$userId)->first();

        if (isset($projectTask)) {

            $data['projectTask'] = $projectTask;
            $data['dataTasks'] = DB::table('projects_task')->where('tech_id',$userId)->get();

            return view('tech.minutes.create', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Aktivitas.');
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

        $taskId = $request->task_id;

        //check priviledge
            $projectTask = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
            ])
            ->where('id',$taskId)->where('tech_id',$userId)->first();

        $projectId = $projectTask->project_id;

        if (isset($projectTask)) {

            $request->validate([
                'name' => 'required',
                'task_id' => 'required',
                'event_start' => 'required',
                'event_end' => 'required|after:event_start',
            ]);

            //custom setting to support file upload
                $data = $request->except(['_token','submit','image']);
            
            //insert text data to database
                $data['publisher_id'] = $userId;
                $data['publisher_type'] = $userType;
                $data['event_start'] = date('H:i:s', strtotime($request->event_start));
                $data['event_end'] = date('H:i:s', strtotime($request->event_end));
                //database
                DB::table('project_minutes')->insert($data);

            //upload image to database
                $fileName = null;
                $destinationPath = public_path().'/img/minutes/tech/';
            
            // Retrieving An Uploaded File - multiple image upload
                $files = $request->file('image');
                if (!empty($files)) {
                    foreach ($files as $file) {
                        $extension = $file->getClientOriginalExtension();
                        $fileName = time().'_'.$file->getClientOriginalName();
                        // Moving An Uploaded File
                            $file->move($destinationPath, $fileName);
                        //setting the datas
                            $image['image'] = $fileName;
                            $latestPostData = DB::table('project_minutes')->select('id')->latest('id')->first();
                            $image['projmin_id'] = $latestPostData->id;
                            $image['task_id'] = $taskId;
                            $image['publisher_id'] = $userId;
                            $image['publisher_type'] = $userType;
                        //insert to the database
                            DB::table('project_minutes_images')->insert($image);
                    }
                }

            return redirect()->route('minutes-tech.index','project_id='.$projectId.'&task_id='.$taskId)->with('alert-success','Data berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Aktivitas.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Aktivitas.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;

        //check privilege
            $projectMinute = DB::table('project_minutes as pm')
            ->select([
                'pm.*',
                DB::raw('(SELECT COUNT(image) FROM project_minutes_images WHERE project_minutes_images.projmin_id = pm.id) as images_count'),
            ])
            ->where('id',$id)->where('publisher_id',$userId)->first();

            if (!isset($projectMinute)) {
                return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Aktivitas.');
            }

        $taskId = $projectMinute->task_id;
        //tech minutes image data
            $data['techMinutesImages'] = DB::table('project_minutes_images')
            ->where('task_id',$taskId)
            ->where('publisher_id',$userId)
            ->where('publisher_type',$userType)
            ->orderBy('id','DESC')
            ->get();

        //check priviledge
            $projectTask = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
            ])
            ->where('id',$taskId)->where('tech_id',$userId)->first();

            if (isset($projectTask)) {

                $data['projectTask'] = $projectTask;
                $data['techMinute'] = $projectMinute;

                return view('tech.minutes.edit', $data);
            }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Aktivitas.');
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

        $projectId = $request->project_id;
        $taskId = $request->task_id;

        //check priviledge
        $projectTask = DB::table('projects_task as pt')
        ->select([
            'pt.*',
            DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
        ])
        ->where('id',$taskId)->where('tech_id',$userId)->first();

        if (isset($projectTask)) {

            $request->validate([
                'name' => 'required',
                'event_start' => 'required',
                'event_end' => 'required|after:event_start',
            ]);

            //file handler
            $fileName = null;
            $destinationPath = public_path().'/img/minutes/tech/';
            
            // Retrieving An Uploaded File
                $files = $request->file('image');
                if (!empty($files)) {
                    foreach ($files as $file) {
                        $extension = $file->getClientOriginalExtension();
                        $fileName = time().'_'.$file->getClientOriginalName();
                        // Moving An Uploaded File
                            $file->move($destinationPath, $fileName);
                        //setting the datas
                            $image['image'] = $fileName;
                            $latestPostData = DB::table('project_minutes')->select('id')->where('id',$id)->first();
                            $image['projmin_id'] = $latestPostData->id;
                            $image['task_id'] = $taskId;
                            $image['publisher_id'] = $userId;
                            $image['publisher_type'] = $userType;
                        //insert to the database
                            DB::table('project_minutes_images')->insert($image);
                    }
                }
            /** first version */
            /*
            $file = $request->file('image');
            if (!empty($file)) {
                $extension = $file->getClientOriginalExtension();
                $fileName = time().'_'.$file->getClientOriginalName();
        
                // Moving An Uploaded File
                $request->file('image')->move($destinationPath, $fileName);

                //delete previous image
                $dataImage = DB::table('project_minutes')->select('image as image')->where('id', $id)->first();
                $oldImage = $dataImage->image;

                if($oldImage !== 'default.png'){
                    $image_path = $destinationPath.$oldImage;
                    if(File::exists($image_path)) {
                        File::delete($image_path);
                    }
                }
            }
            */

            //custom setting to support file upload
            $data = $request->except(['_token','_method','submit','project_id','image']);
            
            $data['publisher_id'] = $userId;
            $data['event_start'] = date('H:i:s', strtotime($request->event_start));
            $data['event_end'] = date('H:i:s', strtotime($request->event_end));

            /**first version */
            /*
            if (!empty($fileName)) {
                $data['image'] = $fileName;
            }
            */

            DB::table('project_minutes')->where('id',$id)->update($data);

            return redirect()->route('minutes-tech.index','project_id='.$projectId.'&task_id='.$taskId)->with('alert-success','Data berhasil diubah.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Aktivitas.');
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
        $userType = Auth::user()->user_type;

        //check privilege
            $projectMinute = DB::table('project_minutes')->where('id',$id)->where('publisher_id',$userId)->first();
            if (!isset($projectMinute)) {
                return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Aktivitas.');
            }
        $taskId = $projectMinute->task_id;
        //check priviledge
            $projectTask = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
            ])
            ->where('id',$taskId)->where('tech_id',$userId)->first();

        if (isset($projectTask)) {
            $projectMinuteImages = DB::table('project_minutes_images')->where('projmin_id',$id)->where('task_id',$taskId)->where('publisher_id',$userId)->get();
            //delete previous image
                foreach ($projectMinuteImages as $projectMinuteImage) {
                    $destinationPath = public_path().'/img/minutes/tech/';
                    $dataImage = $projectMinuteImage->image;

                    if($dataImage !== 'default.png'){
                        $image_path = $destinationPath.$dataImage;
                        if(File::exists($image_path)) {
                            File::delete($image_path);
                        }
                    }
                    DB::table('project_minutes_images')->delete($projectMinuteImage->id);
                }
            //delete from database
                DB::table('project_minutes')->delete($id);

            return redirect()->back()->with('success', 'Data berhasil dihapus.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Aktivitas.');
    }

}
