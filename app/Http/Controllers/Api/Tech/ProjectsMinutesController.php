<?php

namespace App\Http\Controllers\Api\Tech;

use App\Http\Controllers\Api\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class ProjectsMinutesController extends Controller
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

        $projectId = $request->project_id;
        $taskId = $request->task_id;
        $techId = $user->id;

        // tech image
        $data['images'] = DB::table('project_minutes_images')
        ->select([
            'projmin_id',
            'image'
            ])
        ->where('publisher_id',$techId)
        ->where('task_id',$taskId)
        ->get();

        // tech minutes
        $data['minutes'] = DB::table('project_minutes as pm')
        ->select([
            'id',
            'task_id',
            DB::raw('(SELECT project_id FROM projects_task WHERE projects_task.id = pm.task_id) as project_id'),
            DB::raw('(SELECT name FROM projects WHERE projects.id = project_id) as project_name'),
            DB::raw('(SELECT name FROM projects_task WHERE projects_task.id = pm.task_id) as task_name'),
            DB::raw('(SELECT number FROM projects_task WHERE projects_task.id = pm.task_id) as task_number'),
            'name',
            'date',
            'event_start',
            'event_end',
            // DB::raw('(SELECT image FROM project_minutes_images WHERE project_minutes_images.projmin_id = image) as id'),
        ])
        ->where('publisher_id',$techId)
        ->where('task_id',$taskId)
        ->orderBy('id','DESC')
        ->get();

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $user = $this->authUser();
        $data = $request->only(['task_id','name','description','event_start','event_end','image']);

        if (!$data) {
            return response()->json(['error' => 'Anda tidak diijinkan mengakses halaman ini.']);
        }

        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }

        if (!isset($data['task_id'])) {
            return response()->json(['error' => 'Maaf, task id tidak tersedia.']);
        }
        if (!isset($data['name'])) {
            return response()->json(['error' => 'Maaf, Anda harus mengisi judul aktifitas.']);
        }
        if (!isset($data['event_start'])) {
            return response()->json(['error' => 'Maaf, Anda harus mengisi jam mulai aktifitas.']);
        }
        if (!isset($data['event_end'])) {
            return response()->json(['error' => 'Maaf, Anda harus mengisi jam selesai aktifitas.']);
        }

        $taskId = $data['task_id'];
        $techId = $user->id;
        $userType = $user->user_type;
        $projectId = $request->project_id;
        
        //check priviledge
            $projectTask = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name')
            ])
            ->where('id',$taskId)->where('tech_id',$techId)->first();

        $projectId = $projectTask->project_id;

        if (isset($projectTask)) {
            //insert text data to database
                $data['publisher_id'] = $techId;
                $data['publisher_type'] = $userType;
                $data['event_start'] = date('H:i:s', strtotime($data['event_start']));
                $data['event_end'] = date('H:i:s', strtotime($data['event_end']));
                $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
                $data['image'] = 'default.png';
                //database
                    DB::table('project_minutes')->insert($data);
            //upload image to database
                $fileName = null;
                $destinationPath = public_path().'/img/minutes/tech/';
            
            // Retrieving An Uploaded File - multiple image upload
                if ($request->hasFile('image')) {
                    $files = $request->file('image');
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
                            $image['publisher_id'] = $techId;
                            $image['publisher_type'] = $userType;
                            $image['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
                        //insert to the database
                            DB::table('project_minutes_images')->insert($image);
                    }
                }
            return response()->json(['message' => 'Data berhasil disimpan.']);
        }
        return response()->json(['error' => 'Terjadi kesalahan koneksi, data tidak berhasil disimpan.']);
    }

    public function show($id)
    {
        $user = $this->authUser();
        $techId = $user->id;
        $userType = $user->user_type;

        $latestPostData = DB::table('project_minutes')->select(['id','task_id','name','event_start','event_end'])->where('id',$id)->where('publisher_id',$techId)->first();
        if (!$latestPostData) {
            return response()->json(['error' => 'Anda tidak diijinkan mengakses halaman ini.']);
        }

        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }

        $taskId = $latestPostData->task_id;

        //check priviledge
        $projectTask = DB::table('projects_task as pt')->where('id',$taskId)->where('tech_id',$techId)->first();

        if (isset($projectTask)) {
            $data['minutes'] = DB::table('project_minutes')->where('id',$id)->first();
            $data['minutes_images'] = DB::table('project_minutes_images')->select([
                'projmin_id',
                'image',
            ])->where('projmin_id',$id)->where('publisher_id',$techId)->get();
            
            return response()->json($data);
        }
        return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
    }

    public function update(Request $request, $id)
    {
        $user = $this->authUser();
        $data = $request->only(['task_id','name','description','event_start','event_end','image']);

        $taskId = $data['task_id'];
        $techId = $user->id;
        $userType = $user->user_type;

        $latestPostData = DB::table('project_minutes')->select(['id','name','event_start','event_end'])->where('id',$id)->where('publisher_id',$techId)->first();
        if (!$latestPostData) {
            return response()->json(['error' => 'Anda tidak diijinkan mengakses halaman ini.']);
        }

        if (!$data) {
            return response()->json(['error' => 'Anda tidak diijinkan mengakses halaman ini.']);
        }

        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }

        if (!isset($data['task_id'])) {
            return response()->json(['error' => 'Maaf, task id tidak tersedia.']);
        }
        if (!isset($data['name'])) {
            $data['name'] = $latestPostData->name;
        }
        if (!isset($data['event_start'])) {
            $data['event_start'] = $latestPostData->event_start;
        }
        if (!isset($data['event_end'])) {
            $data['event_end'] = $latestPostData->event_end;
        }

        //check priviledge
        $projectTask = DB::table('projects_task as pt')->where('id',$taskId)->where('tech_id',$techId)->first();

        if (isset($projectTask)) {
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
                            $image['projmin_id'] = $latestPostData->id;
                            $image['task_id'] = $taskId;
                            $image['publisher_id'] = $techId;
                            $image['publisher_type'] = $userType;
                            $image['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
                        //insert to the database
                            DB::table('project_minutes_images')->insert($image);
                    }
                }

            //custom setting to support file upload
            $data['publisher_id'] = $techId;
            $data['event_start'] = date('H:i:s', strtotime($data['event_start']));
            $data['event_end'] = date('H:i:s', strtotime($data['event_end']));
            $data['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
            $data['image'] = 'default.png';

            DB::table('project_minutes')->where('id',$id)->update($data);

            return response()->json(['message' =>'Data berhasil diubah.']);
        }
        return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
    }

    public function destroy($id)
    {
        $user = $this->authUser();
        $techId = $user->id;
        $userType = $user->user_type;

        $projectMinute = DB::table('project_minutes')->where('id',$id)->where('publisher_id',$techId)->first();

        if (!$projectMinute) {
            return response()->json(['error' => 'Terjadi kesalahan koneksi, data tidak berhasil dihapus.']);
        }

        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }
        //check privilege
            $taskId = $projectMinute->task_id;
        //check priviledge
            $projectTask = DB::table('projects_task as pt')->where('id',$taskId)->where('tech_id',$techId)->first();
        if (isset($projectTask)) {
            $projectMinuteImages = DB::table('project_minutes_images')->where('projmin_id',$id)->where('task_id',$taskId)->where('publisher_id',$techId)->get();
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

            return response()->json(['message' => 'Data berhasil dihapus.']);
        }
        return response()->json(['error' => 'Terjadi kesalahan koneksi, data tidak berhasil dihapus.']);
    }
}
