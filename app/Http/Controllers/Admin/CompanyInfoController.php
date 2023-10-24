<?php

namespace App\Http\Controllers\Admin;

use App\Models\CompanyInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class CompanyInfoController extends Controller
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
        $data['companyData'] = CompanyInfo::first();

        return view('admin.company-info.edit', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CompanyInfo  $companyInfo
     * @return \Illuminate\Http\Response
     */
    public function show(CompanyInfo $companyInfo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CompanyInfo  $companyInfo
     * @return \Illuminate\Http\Response
     */
    public function edit(CompanyInfo $companyInfo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CompanyInfo  $companyInfo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'mobile' => 'required',
            'email' => 'required|email',
            'url' => 'required',
            'brief' => 'required|min:20|max:255',
            'keywords' => 'required',
            'slogan' => 'required|min:10',
            'address' => 'required:min:20',
            'map' => 'required',
            'logo' => 'mimes:jpeg,jpg,png|max:2048',
            'year' => 'required',
        ]);

        //file handler
        $fileName = null;
        $destinationPath = public_path().'/img/';
        
        // Retrieving An Uploaded File
        $file = $request->file('logo');
        if (!empty($file)) {
            $extension = $file->getClientOriginalExtension();
            $fileName = time().'_'.$file->getClientOriginalName();
    
            // Moving An Uploaded File
            $request->file('logo')->move($destinationPath, $fileName);

            //delete previous image
            $dataImage = CompanyInfo::select('logo as image')->where('id', $id)->first();
            $oldImage = $dataImage->image;

            if($oldImage !== 'logo.png'){
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
            $data['logo'] = $fileName;
        }
        CompanyInfo::where('id',$id)->update($data);

        return redirect()->route('company-info.index')->with('success','Data berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CompanyInfo  $companyInfo
     * @return \Illuminate\Http\Response
     */
    public function destroy(CompanyInfo $companyInfo)
    {
        //
    }
}
