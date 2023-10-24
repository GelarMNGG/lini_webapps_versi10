<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Auth;
use DB;

class TaskCommentFileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Comments Upload File.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Comments Upload File.');
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

        $commentId = $request->comment_id;

        $firstCheck = DB::table('tasks_comments')->where('tc_id',$commentId)->where('tc_publisher_id',$userId)->count();

        if ($firstCheck > 0) {
            $request->validate([
                'image' => 'required|mimes:jpeg,jpg,png,pdf|max:4096',
            ]);

            //file handler
            $fileName = null;
            $destinationPath = public_path().'/img/comment-file/task/';
            
            // Retrieving An Uploaded File
            $file = $request->file('image');
            if (!empty($file)) {
                $extension = $file->getClientOriginalExtension();
                $fileName = time().'_'.$file->getClientOriginalName();

                // Moving An Uploaded File
                $request->file('image')->move($destinationPath, $fileName);
            }

            //custom setting to support file upload
            $data = $request->except(['_token','_method','submit']);
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
            
            if (!empty($fileName)) {
                $data['image'] = $fileName;
            }

            DB::table('tasks_comments_files')->insert($data);

            return redirect()->back()->with('alert-success','File berhasil diupload.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Comments Upload File.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Comments Upload File.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Comments Upload File.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Comments Upload File.');
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
        $userLevel = Auth::user()->user_level;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if ($userCompany == 1) {

            //check priviledge
            $dataCheck = DB::table('tasks_comments_files')->where('id',$id)->count();

            if ($dataCheck > 0) {
                //delete previous image
                $destinationPath = public_path().'/img/comment-file/task/';
                $dataImages = DB::table('tasks_comments_files')->select('image as image')->where('id', $id)->get();

                if (count($dataImages) > 0) {
                    foreach($dataImages as $dataImage){
                        $oldImage = $dataImage->image;
        
                        if($oldImage !== 'default.png'){
                            $image_path = $destinationPath.$oldImage;
                            if(File::exists($image_path)) {
                                File::delete($image_path);
                            }
                        }
                    }
                }

                //delete from database
                DB::table('tasks_comments_files')->delete($id);

                return redirect()->back()->with('alert-success','File berhasil dihapus.');
            }

            return redirect()->back()->with('alert-danger','File tidak ditemukan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Comments Upload File.');
    }
}
