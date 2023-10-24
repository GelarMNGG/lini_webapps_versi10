<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Arr;
use Auth;
use DB;

class ClientContactPersonController extends Controller
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Clients Contact.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $userRole = Auth::user()->role;
        $userType = Auth::user()->user_type;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $companyId = $request->cid;
        $contactStatus = 1; //shared
        $publisherTable = $userType.'s';

        $firstCheck = DB::table('clients')->where('id',$companyId)->first();

        if (isset($firstCheck)) {
            $data['clientsData'] = $firstCheck;

            return view('admin.client.contact.create', $data);
        }

        return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
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
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $errorMessage = [
            'firstname.required' => 'Field ini tidak boleh kosong.',
            'mobile.required' => 'Field ini tidak boleh kosong.',
            'address.min:20' => 'Minimum 20 karakter.',
            'email.required' => 'Field ini tidak boleh kosong dan unik.',
        ];
        $validation = Validator::make($request->all(),[
            'firstname' => 'required',
            'mobile' => 'required|min:9',
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
        $data = $request->except(['_token','_method','submit','password_confirmation']);

        $data['publisher_id'] = $userId;
        $data['publisher_type'] = $userType;
        $data['publisher_department'] = $userDepartment;
        $data['publisher_company'] = $userCompany;

        $data['password'] = Hash::make($request['password']);
        $data['active'] = 1;
        $data['is_verified'] = 1;
        $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $data['email_verified_at'] = Carbon::now()->format('Y-m-d H:i:s');

        if (!empty($fileName)) {
            $data['image'] = $fileName;
        }

        DB::table('customers')->insert($data);

        return redirect()->route('client.index')->with('alert-success','Data berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $userId = Auth::user()->id;
        $userRole = Auth::user()->role;
        $userType = Auth::user()->user_type;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $theStatus = 1; //shared

        if ($userRole == 1 && $userCompany == 1 && $userDepartment == 5) {
            $firstCheck = DB::table('customers')->where('id',$id)->first();
        }else{
            $firstCheck = DB::table('customers')->where('id',$id)->where('status',$theStatus)->first();
        }
        
        if (isset($firstCheck)) {
            if ($firstCheck->publisher_type == 'admin') {
                $data['publisherData'] = DB::table('admins')->select('firstname','lastname')->where('id',$firstCheck->publisher_id)->first();
            }else{
                $data['publisherData'] = DB::table('users')->select('firstname','lastname')->where('id',$firstCheck->publisher_id)->first();
            }

            $companyId = $firstCheck->company_id;
            $secondCheck = DB::table('clients')->where('id',$companyId)->first();

            $data['contactData'] = $firstCheck;
            $data['clientsData'] = $secondCheck;

            return view('admin.client.contact.show', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Clients Contact.');
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
        $userRole = Auth::user()->role;
        $userType = Auth::user()->user_type;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if ($userRole == 1 && $userCompany == 1 && $userDepartment == 5) {
            $firstCheck = DB::table('customers')->where('id',$id)->first();
        }else{
            $firstCheck = DB::table('customers')->where('id',$id)
            ->where('publisher_id',$userId)
            ->where('publisher_type',$userType)
            ->where('publisher_department',$userDepartment)
            ->where('publisher_company',$userCompany)
            ->first();
        }
        
        if (isset($firstCheck)) {
            $companyId = $firstCheck->company_id;
            $secondCheck = DB::table('clients')->where('id',$companyId)->first();

            $data['contactData'] = $firstCheck;
            $data['clientsData'] = $secondCheck;

            return view('admin.client.contact.edit', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Clients Contact.');
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
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $firstCheck = DB::table('customers')->where('id',$id)->first();

        if (isset($firstCheck)) {

            $errorMessage = [
                'firstname.required' => 'Field ini tidak boleh kosong.',
                'mobile.required' => 'Field ini tidak boleh kosong.',
                'address.min:20' => 'Minimum 20 karakter.',
                'email.required' => 'Field ini tidak boleh kosong dan unik.',
            ];
            $validation = Validator::make($request->all(),[
                'firstname' => 'required',
                'mobile' => 'required|min:9',
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
                $dataImage = DB::table('customers')->select('image as image')->where('id', $id)->first();
                $oldImage = $dataImage->image;

                if($oldImage !== 'default.png'){
                    $image_path = $destinationPath.$oldImage;
                    if(File::exists($image_path)) {
                        File::delete($image_path);
                    }
                }
            }

            //old password
            $oldPassword = DB::table('customers')->select('password as password')->where('id', $id)->first();

            if(!empty($request['password']) && ($request['password'] != $oldPassword->password)){ 
                $request['password'] = Hash::make($request['password']);
            }else{
                $request = Arr::except($request,array('password'));    
            }

            //custom setting to support file upload
            $data = $request->except(['_token','_method','submit']);

            $data['publisher_id'] = $userId;
            $data['publisher_type'] = $userType;
            $data['publisher_department'] = $userDepartment;
            $data['publisher_company'] = $userCompany;
            
            if (!empty($fileName)) {
                $data['image'] = $fileName;
            }
            
            DB::table('customers')->where('id',$id)->update($data);

            return redirect()->route('client.index')->with('alert-success','Data berhasil diperbarui.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Clients Contact.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Clients Contact.');
    }
}
