<?php

namespace App\Http\Controllers\Tech;

use App\Tech;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\ImageManagerStatic as Image;
use Auth;
use DB;

class TechProfileController extends Controller
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
        $userId = Auth::user()->id;

        $data['userProfile'] = Tech::find($userId);

        return view('tech.profile', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Profile.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Profile.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Profile.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Profile.');
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
        $errorMessage = [
            'firstname.required' => 'Field ini tidak boleh kosong.',
            'mobile.required' => 'Field ini tidak boleh kosong.',
            'address.min:20' => 'Minimum 20 karakter.',
            'image.required' => 'Field ini tidak boleh kosong. File yang diijinkan: jpeg, jpg, png, pdf.',
            'email.required' => 'Field ini tidak boleh kosong dan unik.',
        ];
        $validation = Validator::make($request->all(),[
            'firstname' => 'required',
            //'title' => 'required|min:2',
            'mobile' => 'required|min:9',
            'address' => 'required|min:20',
            'image' => 'mimes:jpeg,jpg,png,pdf|max:4096', //2048,4096
            'email' => 'required|email|string|max:255|unique:users,email,'.$id,
        ],$errorMessage);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }
        //file handler
        $fileName = null;
        $destinationPath = public_path().'/admintheme/images/users/';
        
        // Retrieving An Uploaded File
        $file = $request->file('image');
        if (!empty($file)) {
            $extension = $file->getClientOriginalExtension();
            $fileName = time().'_'.$file->getClientOriginalName();
    
            // Moving An Uploaded File
            $size = filesize($file)/1000;
            if ($size > 2001) {
                $x = 55;
            }elseif ($size > 1000 && $size < 2000) {
                $x = 75;
            }else{
                $x = 100;
            }
            Image::make($file)->resize(400, 400)->save($destinationPath.$fileName,$x);

            //delete previous image
            $dataImage = Tech::select('image as image')->where('id', $id)->first();
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
        Tech::where('id',$id)->update($data);

        return redirect()->route('profil-tech.index')->with('success','Data profile berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan menghapus halaman Profile.');
    }
}
