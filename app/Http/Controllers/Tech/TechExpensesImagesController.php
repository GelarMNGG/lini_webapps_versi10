<?php

namespace App\Http\Controllers\Tech;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Auth;
use DB;

class TechExpensesImagesController extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Upload Image Expenses.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Upload Image Expenses.');
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

        //check priviledge
        $firstCheck = DB::table('projects_task')->where('id',$taskId)->where('tech_id',$userId)->first();

        if (isset($firstCheck)) {

            $request->validate([
                'image' => 'required|mimes:jpeg,jpg,png|max:9216',
            ]);
    
            //file handler
            $fileName = null;
            $destinationPath = public_path().'/img/expenses/tech/';
            
            // Retrieving An Uploaded File
            $file = $request->file('image');
            if (!empty($file)) {
                $extension = $file->getClientOriginalExtension();
                $fileName = time().'_'.$file->getClientOriginalName();
        
                // Moving An Uploaded File
                $request->file('image')->move($destinationPath, $fileName);
            }
    
            //custom setting to support file upload
            $data = $request->except(['_token','submit','project_id']);
            
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
            $data['tech_id'] = $userId;

            //reset
                $dataExpense['status'] = 1;
                $dataExpense['submitted_at'] = NULL;
                $dataExpense['rejected_at'] = NULL;
                DB::table('project_expenses')->where('id',$request->expense_id)->update($dataExpense);
    
            if (!empty($fileName)) {
                $data['image'] = $fileName;
            }
    
            DB::table('project_expenses_files')->insert($data);

            return redirect()->route('expenses-tech.index', 'project_id='.$projectId.'&task_id='.$taskId)->with('alert-success','Data berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Upload Image Expenses.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Upload Image Expenses.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Upload Image Expenses.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Upload Image Expenses.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //delete previous image
            $destinationPath = public_path().'/img/expenses/tech/';
            $filecheck = DB::table('project_expenses_files')->select('image')->where('id',$id)->first();
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

        //delete from database
            DB::table('project_expenses_files')->delete($id);

        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }
}
