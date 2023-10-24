<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Arr;
use DB;

class TeamController extends Controller
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
        $data['departments'] = DB::table('department')->get();
        $data['teams'] = Admin::get();

        return view('admin.team.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['departments'] = DB::table('department')->get();

        return view('admin.team.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $errorMessage = [
            'firstname.required' => 'Field ini tidak boleh kosong.',
            'mobile.required' => 'Field ini tidak boleh kosong.',
            'department_id.required' => 'Field ini tidak boleh kosong.',
            'address.min:20' => 'Minimum 20 karakter.',
            'image.required' => 'Field ini tidak boleh kosong. File yang diijinkan: jpeg, jpg, png, pdf.',
            'email.required' => 'Field ini tidak boleh kosong dan unik.',
        ];
        $validation = Validator::make($request->all(),[
            'firstname' => 'required',
            'title' => 'required|min:2',
            'mobile' => 'required|min:9',
            'department_id' => 'required',
            'address' => 'required|min:20',
            'image' => 'mimes:jpeg,jpg,png,pdf|max:2048',
            'email' => 'required|email|string|max:255|unique:admins,email',
            'password' => 'required|confirmed|min:6',
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
            $request->file('image')->move($destinationPath, $fileName);
        }

        //custom setting to support file upload
        $data = $request->except(['_token','_method','submit']);
        
        $data['password'] = Hash::make($request->password);
        $data['active'] = 1;
        $data['is_verified'] = 1;
        $data['email_verified_at'] = Carbon::now()->format('Y-m-d H:i:s');

        #$data = $request->all();
        if (!empty($fileName)) {
            $data['image'] = $fileName;
        }

        #dd($data);

        Admin::create($data);

        return redirect()->route('team.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function show(Admin $admin)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Team.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['departments'] = DB::table('department')->get();
        $data['userProfile'] = Admin::find($id);

        return view('admin.team.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $errorMessage = [
            'firstname.required' => 'Field ini tidak boleh kosong.',
            'mobile.required' => 'Field ini tidak boleh kosong.',
            'department_id.required' => 'Field ini tidak boleh kosong.',
            'address.min:20' => 'Minimum 20 karakter.',
            'image.required' => 'Field ini tidak boleh kosong. File yang diijinkan: jpeg, jpg, png, pdf.',
            'email.required' => 'Field ini tidak boleh kosong dan unik.',
        ];
        $validation = Validator::make($request->all(),[
            'firstname' => 'required',
            'title' => 'required|min:2',
            'mobile' => 'required',
            'department_id' => 'required',
            'address' => 'required|min:20',
            'image' => 'mimes:jpeg,jpg,png,pdf|max:2048',
            'email' => 'required|email|string|max:255|unique:admins,email,'.$id,
            'password' => 'required|min:6',
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
            $request->file('image')->move($destinationPath, $fileName);

            //delete previous image
            $dataImage = Admin::select('image as image')->where('id', $id)->first();
            $oldImage = $dataImage->image;

            if($oldImage !== 'default.png'){
                $image_path = $destinationPath.$oldImage;
                if(File::exists($image_path)) {
                    File::delete($image_path);
                }
            }
        }

        //old password
        $oldPassword = Admin::select('password as password')->where('id', $id)->first();

        if(!empty($request['password']) && ($request['password'] != $oldPassword->password)){ 
            $request['password'] = Hash::make($request['password']);
        }else{
            $request = Arr::except($request,array('password'));    
        }

        //custom setting to support file upload
        $data = $request->except(['_token','_method','submit']);
        #$data = $request->all();
        if (!empty($fileName)) {
            $data['image'] = $fileName;
        }
        Admin::where('id',$id)->update($data);

        return redirect()->route('team.index')->with('success','Data profile berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //delete previous image
        $destinationPath = public_path().'/admintheme/images/users/';
        $dataImage = Admin::select('image as image')->where('id', $id)->first();
        $oldImage = $dataImage->image;

        if($oldImage !== 'default.png'){
            $image_path = $destinationPath.$oldImage;
            if(File::exists($image_path)) {
                File::delete($image_path);
            }
        }
        
        Admin::destroy($id);
        return redirect()->route('team.index')->with('success', 'Data berhasil dihapus.');
    }
}
