<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Auth;
use DB;

class ClientController extends Controller
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
        $userId = Auth::user()->id;
        $userRole = Auth::user()->role;
        $userType = Auth::user()->user_type;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $theStatus = 1; //shared
        $publisherTable = $userType.'s';

        $data['clients'] = DB::table('clients')
        ->select([
            'clients.*',
            DB::raw('(SELECT COUNT(*) FROM customers WHERE customers.company_id = clients.id AND customers.status = 1) as cp_count'),
            DB::raw('(SELECT COUNT(*) FROM customers WHERE customers.company_id = clients.id) as cp_count_admin'),
        ])
        ->orderBy('id','DESC')
        ->get();
        
        if ($userRole == 1 && $userCompany == 1 && $userDepartment == 5) {
            $data['clientsContactPersons'] = DB::table('customers')->get();
            $data['publisher_count'] = 1;
        }else{
            $data['clientsContactPersons'] = DB::table('customers')->where('status',$theStatus)->get();
            $data['publisher_count'] = 0;
        }

        $data['userId'] = $userId;
        $data['userRole'] = $userRole;
        $data['userType'] = $userType;
        $data['userCompany'] = $userCompany;
        $data['userDepartment'] = $userDepartment;

        return view('admin.client.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.client.create');
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
            'name' => 'required',
            'logo' => 'required|mimes:jpeg,jpg,png,pdf|max:2048',
        ]);

        //file handler
        $fileName = null;
        $destinationPath = public_path().'/img/clients/';
        
        // Retrieving An Uploaded File
        $file = $request->file('logo');
        if (!empty($file)) {
            $extension = $file->getClientOriginalExtension();
            $fileName = time().'_'.$file->getClientOriginalName();
    
            // Moving An Uploaded File
            $request->file('logo')->move($destinationPath, $fileName);
        }

        //custom setting to support file upload
        $data = $request->except(['_token','_method','submit']);

        #$data = $request->all();
        if (!empty($fileName)) {
            $data['logo'] = $fileName;
        }

        DB::table('clients')->insert($data);

        return redirect()->route('client.index')->with('success','Data berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Clients.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['client'] = DB::table('clients')->where('id',$id)->first();

        return view('admin.client.edit', $data);
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
            'name' => 'required',
            'logo' => 'mimes:jpeg,jpg,png,pdf|max:2048',
        ]);

        //file handler
        $fileName = null;
        $destinationPath = public_path().'/img/clients/';
        
        // Retrieving An Uploaded File
        $file = $request->file('logo');
        if (!empty($file)) {
            $extension = $file->getClientOriginalExtension();
            $fileName = time().'_'.$file->getClientOriginalName();
    
            // Moving An Uploaded File
            $request->file('logo')->move($destinationPath, $fileName);

            //delete previous image
            $dataImage = DB::table('clients')->select('logo as image')->where('id', $id)->first();
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
            $data['logo'] = $fileName;
        }

        DB::table('clients')->where('id', $id)->update($data);

        return redirect()->route('client.index')->with('success','Data berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $destinationPath = public_path().'/img/clients/';
        //delete previous image
        $dataImage = DB::table('clients')->select('logo as image')->where('id', $id)->first();
        $oldImage = $dataImage->image;
        
        if($oldImage !== 'default.png'){
            $image_path = $destinationPath.$oldImage;
            if(File::exists($image_path)) {
                File::delete($image_path);
            }
        }

        DB::table('clients')->delete($id);

        return redirect()->route('client.index')->with('success', 'Data berhasil dihapus.');
    }
}
