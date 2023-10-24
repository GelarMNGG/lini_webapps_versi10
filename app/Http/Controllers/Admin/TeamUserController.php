<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Auth;
use Arr;
use DB;

class TeamUserController extends Controller
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
    public function index(Request $request)
    {
        $role = Auth::user()->role;
        $userCompany = Auth::user()->company_id;
        $department = Auth::user()->department_id;

        $skin = $request->skin;
        
        if ($role == 1) {
            //skin implementation
            if ($skin == 1) {
                $data['skin'] = 0;
                $data['skinBack'] = 1;
                $data['teamusers'] = DB::table('users')
                ->select([
                    'users.*',
                    DB::raw('(SELECT name FROM users_level WHERE users_level.department_id = users.department_id AND users_level.id = users.user_level) as level_name'),
                    DB::raw('(SELECT name FROM users_level WHERE users_level.role = users.user_level) as role_name'),
                    DB::raw('(SELECT name FROM department WHERE department.id = users.department_id) as department_name'),
                ])
                ->orderBy('department_id','ASC')->latest()->get();

                return view('admin.teamuser.index-table', $data);
            }else{
                $data['skin'] = 1;
                $data['skinBack'] = 0;
                
                $data['teamusers'] = DB::table('users')
                ->select([
                    'users.*',
                    DB::raw('(SELECT name FROM users_level WHERE users_level.department_id = users.department_id AND users_level.id = users.user_level) as level_name'),
                    DB::raw('(SELECT name FROM users_level WHERE users_level.role = users.user_level) as role_name'),
                    DB::raw('(SELECT name FROM department WHERE department.id = users.department_id) as department_name'),
                ])
                ->orderBy('department_id','ASC')->latest()->paginate(10);

                return view('admin.teamuser.index', $data);
            }
        }else{
            //skin implementation
            if ($skin == 1) {
                $data['skin'] = 0;
                $data['skinBack'] = 1;

                if ($userCompany == 2) {
                    $data['teamusers'] = DB::table('users')
                    ->select([
                        'users.*',
                        DB::raw('(SELECT name FROM users_level WHERE users_level.id = users.user_level) as level_name'),
                        DB::raw('(SELECT name FROM users_level WHERE users_level.role = users.user_level) as role_name'),
                        DB::raw('(SELECT name FROM department_lintaslog WHERE department_lintaslog.id = users.department_id) as department_name'),
                    ])
                    ->where('company_id', $userCompany)
                    ->orderBy('department_id','ASC')->latest()->get();
                }else{
                    $data['teamusers'] = DB::table('users')
                    ->select([
                        'users.*',
                        DB::raw('(SELECT name FROM users_level WHERE users_level.department_id = users.department_id AND users_level.id = users.user_level) as level_name'),
                        DB::raw('(SELECT name FROM users_level WHERE users_level.role = users.user_level) as role_name'),
                        DB::raw('(SELECT name FROM department WHERE department.id = users.department_id) as department_name'),
                    ])
                    ->where('department_id', $department)
                    ->orderBy('department_id','ASC')->latest()->get();
                }

                return view('admin.teamuser.index-table', $data);
            }else{
                $data['skin'] = 1;
                $data['skinBack'] = 0;
                
                if ($userCompany == 2) {
                    $data['teamusers'] = DB::table('users')
                    ->select([
                        'users.*',
                        DB::raw('(SELECT name FROM users_level WHERE users_level.id = users.user_level) as level_name'),
                        DB::raw('(SELECT name FROM users_level WHERE users_level.role = users.user_level) as role_name'),
                        DB::raw('(SELECT name FROM department_lintaslog WHERE department_lintaslog.id = users.department_id) as department_name'),
                    ])
                    ->where('company_id', $userCompany)
                    ->orderBy('department_id','ASC')->latest()->paginate(10);
                }else{
                    $data['teamusers'] = DB::table('users')
                    ->select([
                        'users.*',
                        DB::raw('(SELECT name FROM users_level WHERE users_level.department_id = users.department_id AND users_level.id = users.user_level) as level_name'),
                        DB::raw('(SELECT name FROM users_level WHERE users_level.role = users.user_level) as role_name'),
                        DB::raw('(SELECT name FROM department WHERE department.id = users.department_id) as department_name'),
                    ])
                    ->where('department_id', $department)
                    ->orderBy('department_id','ASC')->latest()->paginate(10);
                }

                return view('admin.teamuser.index', $data);
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if ($userCompany == 2) {
            $data['departments'] = DB::table('department_lintaslog')->get();
            $data['userLevels'] = DB::table('users_level')->where('deleted_at',NULL)->where('department_id',$userDepartment)->OrWhere('department_id','0')->get();

            return view('admin.teamuser.create-lin', $data);
        }elseif($userCompany == 1){
            $data['departments'] = DB::table('department')->get();
            $data['userLevels'] = DB::table('users_level')->where('deleted_at',NULL)->where('department_id',$userDepartment)->OrWhere('department_id','0')->get();

            return view('admin.teamuser.create', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Users.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userCompany = Auth::user()->company_id;

        //firstcheck
        $emailData = $request->email;
        $firstCheck = User::select('id')->where('email',$emailData)->first();
        if (isset($firstCheck)) {
            return redirect()->back()->withInput()->with('alert-danger','Alamat email ('.$emailData.') sudah terdaftar dalam database. Coba masukkan alamat email yang lainnya atau kontak administrator.');
        }

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
        $data['company_id'] = $userCompany;
        $data['active'] = 1;
        $data['is_verified'] = 1;
        $data['email_verified_at'] = Carbon::now()->format('Y-m-d H:i:s');

        #$data = $request->all();
        if (!empty($fileName)) {
            $data['image'] = $fileName;
        }

        User::create($data);

        return redirect()->route('teamuser.index')->with('alert-success','Data berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Users.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $itDepartment = 5;

        if ($userDepartment == $itDepartment) {
            $firstCheck = DB::table('users')->where('company_id',$userCompany)->where('id',$id)->first();
        }else{
            $firstCheck = DB::table('users')->where('company_id',$userCompany)->where('department_id',$userDepartment)->where('id',$id)->first();
        }

        if (isset($firstCheck)) {
            $data['userProfile'] = $firstCheck;
            $data['userLevels'] = DB::table('users_level')->where('deleted_at',null)->where('department_id',$userDepartment)->OrWhere('department_id','0')->get();

            if ($userCompany == 1) {
                $data['departments'] = DB::table('department')->get();
                return view('admin.teamuser.edit', $data);
            }elseif($userCompany == 2){
                $data['departments'] = DB::table('department_lintaslog')->get();
                return view('admin.teamuser.edit-lin', $data);
            }else{
                return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
            }
        }
        
        return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $itDepartment = 5;

        if ($userDepartment == $itDepartment) {
            $firstCheck = DB::table('users')->where('company_id',$userCompany)->where('id',$id)->first();
        }else{
            $firstCheck = DB::table('users')->where('company_id',$userCompany)->where('department_id',$userDepartment)->where('id',$id)->first();
        }

        if (isset($firstCheck)) {
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
            $oldPassword = $firstCheck->password;

            if(!empty($request->password) && $request->password != $oldPassword){
                $request['password'] = Hash::make($request->password);
            }else{
                $request = Arr::except($request,array('password'));    
            }

            //custom setting to support file upload
            $data = $request->except(['_token','_method','submit']);
            #$data = $request->all();
            if (!empty($fileName)) {
                $data['image'] = $fileName;
            }

            //recover deleted account
            if (isset($request->deleted_at)) {
                if ($request->deleted_at == 1) {
                    $data['deleted_at'] = NULL;
                }else{
                    $data['deleted_at'] = $firstCheck->deleted_at;
                }
            }

            //User::where('id',$id)->update($data);
            DB::table('users')->where('id',$id)->update($data);

            return redirect()->route('teamuser.index')->with('alert-success','Data profile berhasil diperbarui.');
        }

        return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;
        //delete previous image
        $destinationPath = public_path().'/admintheme/images/users/';
        $dataImage = User::select('image as image')->where('id', $id)->first();
        $oldImage = $dataImage->image;

        if($oldImage !== 'default.png'){
            $image_path = $destinationPath.$oldImage;
            if(File::exists($image_path)) {
                File::delete($image_path);
            }
        }

        if ($userCompany == 1 && $userDepartment == 5) {
            DB::table('users')->delete($id);
        }else{
            User::destroy($id);
        }

        return redirect()->route('teamuser.index')->with('alert-success', 'Data berhasil dihapus.');
    }
}
