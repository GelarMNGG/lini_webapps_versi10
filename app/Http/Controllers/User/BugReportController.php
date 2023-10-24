<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class BugReportController extends Controller
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
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        $data['dataReports'] = DB::table('bug_report')->where('publisher_id',$userId)->where('publisher_type',$userType)->paginate(10);

        return view('user.bug-report.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.bug-report.create');
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
        $userDepartment = Auth::user()->department_id;
        
        $request->validate([
            'name' => 'required',
            'reproduce' => 'required',
            'description' => 'required',
            'image' => 'required',
        ]);
            
        //file handler
        $fileName = null;
        $destinationPath = public_path().'/img/bug-report/user/';
        
        // Retrieving An Uploaded File
        $file = $request->file('image');
        if (!empty($file)) {
            $extension = $file->getClientOriginalExtension();
            $fileName = time().'_'.$file->getClientOriginalName();
    
            // Moving An Uploaded File
            $request->file('image')->move($destinationPath, $fileName);
        }
        
        //getting the data
        $data = $request->except(['_token','submit']);
        $data['publisher_id'] = $userId;
        $data['publisher_type'] = $userType;
        $data['publisher_department'] = $userDepartment;
        $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        //data image
        if (!empty($fileName)) {
            $data['image'] = $fileName;
        }

        DB::table('bug_report')->insert($data);

        return redirect()->route('user-bug-report.index')->with('alert-success','Data berhasil disimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Bug report.');
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
        $userDepartment = Auth::user()->department_id;

        $firstcheck = DB::table('bug_report')->where('id',$id)->where('publisher_id',$userId)->where('publisher_type',$userType)->first();

        if (isset($firstcheck)) {
            $data['dataReport'] = $firstcheck;

            return view('user.bug-report.edit',$data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Bug report.');
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
        $userDepartment = Auth::user()->department_id;

        $request->validate([
            'name' => 'required',
            'reproduce' => 'required',
            'description' => 'required',
            'image' => 'mimes:jpeg,jpg,png,pdf|max:2048',
        ]);

        //file handler
        $fileName = null;
        $destinationPath = public_path().'/img/bug-report/user/';
        
        // Retrieving An Uploaded File
        $file = $request->file('image');
        if (!empty($file)) {
            $extension = $file->getClientOriginalExtension();
            $fileName = time().'_'.$file->getClientOriginalName();
    
            // Moving An Uploaded File
            $request->file('image')->move($destinationPath, $fileName);

            //delete previous image
            $oldImage = $dataImage->image;

            if($oldImage !== 'default.png'){
                $image_path = $destinationPath.$oldImage;
                if(File::exists($image_path)) {
                    File::delete($image_path);
                }
            }
        }
        
        //getting the data
        $data = $request->except(['_token','_method','submit']);
        $data['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
        //data image
        if (!empty($fileName)) {
            $data['image'] = $fileName;
        }

        DB::table('bug_report')->where('id',$id)->update($data);
    
        return redirect()->route('user-bug-report.index')->with('alert-success','Data berhasil disimpan.');
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
        $userDepartment = Auth::user()->department_id;

        $firstcheck = DB::table('bug_report')->where('id',$id)->where('publisher_id',$userId)->where('publisher_type',$userType)->count();

        if ($userDepartment == 5 && $firstcheck > 0) {
            //delete from database
            DB::table('bug_report')->delete($id);
            return redirect()->route('user-bug-report.index')->with('alert-success','Data berhasil dihapus.');
        }
   
        
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Bug report.');
    }
}
