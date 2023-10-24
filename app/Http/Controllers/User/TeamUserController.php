<?php

namespace App\Http\Controllers\User;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Arr;
use Auth;
use DB;

class TeamUserController extends Controller
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
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;
        $data['departments'] = DB::table('department')->get();
        
        if ($userLevel == 22) {
            $data['teamusers'] = DB::table('users')
            ->select([
                'users.*',
                DB::raw('(SELECT name FROM users_level WHERE users_level.department_id = users.department_id AND users_level.id = users.user_level) as level_name')
            ])
            ->where('department_id', $userDepartment)
            ->orderBy('department_id','ASC')->paginate(10);

            return view('user.teamuser.index', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Team User.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;
        $data['departments'] = DB::table('department')->get();

        if ($userLevel == 22) {
            $data['userLevels'] = DB::table('users_level')->where('deleted_at',NULL)->where('department_id',$userDepartment)->OrWhere('department_id','0')->get();

            return view('user.teamuser.create', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Team User.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;
        $data['departments'] = DB::table('department')->get();

        if ($userLevel == 22) {
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

            $data['password'] = Hash::make($request['password']);
            $data['active'] = 1;
            $data['is_verified'] = 1;
            $data['email_verified_at'] = Carbon::now()->format('Y-m-d H:i:s');

            #$data = $request->all();
            if (!empty($fileName)) {
                $data['image'] = $fileName;
            }

            User::create($data);

            return redirect()->route('user-teamuser.index')->with('alert-success','Data berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Team User.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Team User.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        if ($userLevel == 22) {

            $data['userProfile'] = User::find($id);
            $data['departments'] = DB::table('department')->get();
            $data['userLevels'] = DB::table('users_level')->where('deleted_at',null)->where('department_id',$userDepartment)->OrWhere('department_id','0')->get();

            if (!isset($data['userProfile'])) {
                return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
            }

            return view('user.teamuser.edit', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Team User.');
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
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        if ($userLevel == 22) {
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
                'mobile' => 'required',
                'department_id' => 'required',
                'address' => 'required|min:20',
                'image' => 'mimes:jpeg,jpg,png,pdf|max:2048',
                'email' => 'required|email|string|max:255|unique:users,email,'.$id,
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
                $dataImage = User::select('image as image')->where('id', $id)->first();
                $oldImage = $dataImage->image;

                if($oldImage !== 'default.png'){
                    $image_path = $destinationPath.$oldImage;
                    if(File::exists($image_path)) {
                        File::delete($image_path);
                    }
                }
            }

            //old password
            $oldPassword = User::select('password as password')->where('id', $id)->first();

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

            User::where('id',$id)->update($data);

            return redirect()->route('user-teamuser.index')->with('alert-success','Data profile berhasil diperbarui.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Team User.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Team User.');
    }
}
