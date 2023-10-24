<?php

namespace App\Http\Controllers\Api\Tech;

use App\Http\Controllers\Api\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;

class ProjectsCashAdvancesController extends Controller
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
        $cashAdvanceStatus = 3; //3 approved ca
        $expenseStatus = 4; //2 approved expense

        //first check
        if ($taskId == null) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }

        //second check
        $checkPriviledge = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('tech_id',$techId)->where('deleted_at',null)->count();

        if ($checkPriviledge > 0) {
            //getting project data
            $data['projectTask'] = DB::table('projects_task as pt')
            ->select([
                'pt.*',
                DB::raw('(SELECT name FROM projects WHERE projects.id = pt.project_id) as project_name'),
            ])
            ->where('id',$taskId)->where('tech_id',$techId)->first();

            //data cash advance
            $data['dataCashAdvance'] = DB::table('project_cash_advance as pca1')
            ->select([
                'pca1.*',
                DB::raw('(SELECT name FROM project_cash_advance_status WHERE project_cash_advance_status.id = pca1.status) as status_name'),
            ])
            ->where('task_id',$taskId)->where('publisher_id',$techId)->get();

        }
        return response()->json($data);
    }
    public function store(Request $request)
    {
        $user = $this->authUser();
        $data = $request->only(['task_id','name','amount']);

        if (!$data) {
            return response()->json(['error' => 'Anda tidak diijinkan mengakses halaman ini.']);
        }
        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }
        if (!isset($request->project_id)) {
            return response()->json(['error' => 'Maaf, project id tidak tersedia.']);
        } 
        if (!isset($data['task_id'])) {
            return response()->json(['error' => 'Maaf, task id tidak tersedia.']);
        }
        if (!isset($data['name'])) {
            return response()->json(['error' => 'Maaf, Anda harus mengisi judul.']);
        }
        if (!isset($data['amount'])) {
            return response()->json(['error' => 'Maaf, Anda harus mengisi Amount.']);
        }

        $taskId = $data['task_id'];
        $techId = $user->id;
        $userType = $user->user_type;
        $projectId = $request->project_id;

        $request->validate([
            'name' => 'required',
            'amount' => 'required',
        ]);

        //check priviledge
        $checkPriviledge = DB::table('projects_task')->where('project_id',$projectId)->where('id',$taskId)->where('tech_id',$techId)->where('deleted_at',null)->count();

        if ($checkPriviledge > 0) {
            //getting data
            $data = $request->except(['_token','submit']);
            $data['publisher_id'] = $techId;
            $data['publisher_type'] = $userType;
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');

            //insert data
            DB::table('project_cash_advance')->insert($data);

            return response()->json(['message' => 'Data berhasil disimpan.']);
        }
        return response()->json(['error' => 'Terjadi kesalahan koneksi, data tidak berhasil disimpan.']);
    }
    public function update(Request $request, $id)
    {
        $user = $this->authUser();

        $data = $request->only(['task_id','name','amount','code']);

        if (!$data) {
            return response()->json(['error' => 'Anda tidak diijinkan mengakses halaman ini.']);
        }
        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }
        if (!isset($request->project_id)) {
            return response()->json(['error' => 'Maaf, project id tidak tersedia.']);
        } 
        if (!isset($data['task_id'])) {
            return response()->json(['error' => 'Maaf, task id tidak tersedia.']);
        }
        if (!isset($data['name'])) {
            return response()->json(['error' => 'Maaf, Anda harus mengisi nama pemngeluaran.']);
        }
        if (!isset($data['amount'])) {
            return response()->json(['error' => 'Maaf, Anda harus mengisi jumlah pengeluaran.']);
        }

        $techId = $user->id;
        $userType = $user->user_type;
        $projectId = $request->project_id;
        $taskId = $request->task_id;

        //check priviledge
        $checkPriviledge = DB::table('project_cash_advance')->where('id',$id)->where('publisher_id',$techId)->count();

        if ($checkPriviledge > 0) {
            //update status
            if (isset($request->status)) {
                //getting data
                $data = $request->except(['_token','_method','submit']);
                $data['status'] = $request->status;
                $data['reject_status'] = 0;
                $data['rejected_at'] = null;
                $data['submitted_at'] = Carbon::now()->format('Y-m-d H:i:s');

                DB::table('project_cash_advance')->where('id',$id)->update($data);

                return response()->json(['message' => 'Data berhasil disimpan.']);
            }

            //validate
            $request->validate([
                'name' => 'required',
                'amount' => 'required',
            ]);

            //getting data
            $data = $request->except(['_token','_method','submit']);
            $data['publisher_id'] = $techId;
            $data['publisher_type'] = $userType;
            $data['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

            //insert data
            DB::table('project_cash_advance')->where('id',$id)->update($data);

            return response()->json(['message' => 'Data berhasil disimpan.']);
        }

        return response()->json(['error' => 'Terjadi kesalahan koneksi, data tidak berhasil disimpan.']);
    }
    public function destroy(Request $request, $id)
    {
        $user = $this->authUser();
        $techId = $user->id;
        $projectId = $request->project_id;

        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }

        //check priviledge
        $checkPriviledge = DB::table('project_cash_advance')->where('id',$id)->where('publisher_id',$techId)->count();

        if ($checkPriviledge > 0) {

            //delete from database
            DB::table('project_cash_advance')->delete($id);

            return response()->json(['message' => 'Data berhasil dihapus.']);
        }

        return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
    }
}