<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Auth;
use DB;

class SliderController extends Controller
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
        //data
        $data['dataSlidersStatus'] = DB::table('sliders_status')->get();
        $data['sliders'] = DB::table('sliders as sls')
            ->select([
                'sls.*',
                DB::raw('(SELECT name FROM sliders_status WHERE sliders_status.id = sls.status) as status_name'),
            ])
            ->get();

        return view('admin.slider.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userId = Auth::user()->id;
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        if ($userDepartment == 5 || $userDepartment == 4 ) {
            $data['dataSlidersStatus'] = DB::table('sliders_status')->get();
            $data['sliders'] = DB::table('sliders as sls')
                ->select([
                    'sls.*',
                    DB::raw('(SELECT name FROM sliders_status WHERE sliders_status.id = sls.status) as status_name'),
                ])
                ->get();
            return view('admin.slider.create', $data);
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Slider.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = $request->validate([
            'title' => 'required',
            'image' => 'required|mimes:jpeg,jpg,png,pdf|max:2048',
            'status' => 'required',
        ]);

        //file handler
        $fileName = null;
        $destinationPath = public_path().'/img/sliders/';
        
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

        #$data = $request->all();
        if (!empty($fileName)) {
            $data['image'] = $fileName;
        }

        DB::table('sliders')->insert($data);

        return redirect()->route('slider.index')->with('success','Data berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //data
        $data['dataSlidersStatus'] = DB::table('sliders_status')->get();
        $data['sliders'] = DB::table('sliders as sls')
            ->select([
                'sls.*',
                DB::raw('(SELECT name FROM sliders_status WHERE sliders_status.id = sls.status) as status_name'),
            ])
            ->where('id', $id)
            ->first();

        return view('admin.slider.edit', $data);
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
        $validation = $request->validate([
            'title' => 'required',
            'image' => 'mimes:jpeg,jpg,png,pdf|max:2048',
            'status' => 'required',
        ]);

        //file handler
        $fileName = null;
        $destinationPath = public_path().'/img/sliders/';
        
        // Retrieving An Uploaded File
        $file = $request->file('image');
        if (!empty($file)) {
            $extension = $file->getClientOriginalExtension();
            $fileName = time().'_'.$file->getClientOriginalName();
    
            // Moving An Uploaded File
            $request->file('image')->move($destinationPath, $fileName);

            //delete previous image
            $dataImage = DB::table('sliders')->select('image as image')->where('id', $id)->first();
            $oldImage = $dataImage->image;
            
            if($oldImage !== 'default.png'){
                $image_path = $destinationPath.$oldImage;
                if(File::exists($image_path)) {
                    File::delete($image_path);
                }
            }
        }

        //custom setting to support file upload
        $data = $request->except(['_token','_method','submit']);

        #$data = $request->all();
        if (!empty($fileName)) {
            $data['image'] = $fileName;
        }

        DB::table('sliders')->where('id', $id)->update($data);

        return redirect()->route('slider.index')->with('success','Data berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $destinationPath = public_path().'/img/sliders/';
        //delete previous image
        $dataImage = DB::table('sliders')->select('image as image')->where('id', $id)->first();
        $oldImage = $dataImage->image;
        
        if($oldImage !== 'default.png'){
            $image_path = $destinationPath.$oldImage;
            if(File::exists($image_path)) {
                File::delete($image_path);
            }
        }

        DB::table('sliders')->delete($id);

        return redirect()->route('slider.index')->with('success', 'Data berhasil dihapus.');
    }
}
