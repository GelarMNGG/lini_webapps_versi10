<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use DB;

class ServiceController extends Controller
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
        $data['services'] = DB::table('services')->orderBy('id','DESC')->get();
        $data['serviceImages'] = DB::table('services_image')->get();

        return view('admin.service.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.service.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'icon' => 'required',
        ]);

        //file handler
        $fileName = null;
        $destinationPath = public_path().'/img/services/icon/';
        
        // Retrieving An Uploaded File
        $file = $request->file('icon');
        if (!empty($file)) {
            $extension = $file->getClientOriginalExtension();
            $fileName = time().'_'.$file->getClientOriginalName();
    
            // Moving An Uploaded File
            $request->file('icon')->move($destinationPath, $fileName);
        }

        //custom setting to support file upload
        $data = $request->except(['_token','_method','submit']);
        $data['slug'] = Str::slug($request->name);

        #$data = $request->all();
        if (!empty($fileName)) {
            $data['icon'] = $fileName;
        }

        DB::table('services')->insert($data);

        return redirect()->route('service.index')->with('alert-success','Data berhasil disimpan.');
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
        $data['service'] = DB::table('services')->where('id',$id)->first();
        $data['serviceImages'] = DB::table('services_image')->where('service_id',$id)->get();

        return view('admin.service.edit', $data);
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
        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        //file handler
        $fileName = null;
        $destinationPath = public_path().'/img/services/icon/';
        
        // Retrieving An Uploaded File
        $file = $request->file('icon');
        if (!empty($file)) {
            $extension = $file->getClientOriginalExtension();
            $fileName = time().'_'.$file->getClientOriginalName();
    
            // Moving An Uploaded File
            $request->file('icon')->move($destinationPath, $fileName);

            //delete previous image
            $dataImage = DB::table('services')->select('icon as image')->where('id', $id)->first();
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
        $data['slug'] = Str::slug($request->name);

        #$data = $request->all();
        if (!empty($fileName)) {
            $data['icon'] = $fileName;
        }

        DB::table('services')->update($data);

        return redirect()->route('service.index')->with('alert-success','Data berhasil diubah.');
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
        $destinationPath = public_path().'/img/services/';
        $dataImages = DB::table('services_image')->select('image as image')->where('service_id', $id)->get();

        foreach($dataImages as $dataImage){

            $oldImage = $dataImage->image;

            if($oldImage !== 'default.png'){
                $image_path = $destinationPath.$oldImage;
                if(File::exists($image_path)) {
                    File::delete($image_path);
                }
            }
        }
        //delete image
        DB::table('services_image')->where('service_id', $id)->delete();

        //delete category
        DB::table('services')->delete($id);

        return redirect()->route('service.index')->with('alert-success', 'Data berhasil dihapus.');
    }
}
