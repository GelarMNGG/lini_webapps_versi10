<?php

namespace App\Http\Controllers\Tech;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Auth;
use DB;

class TechInputDocController extends Controller
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
        $userType = Auth::user()->user_type;
        $userDepartment = 1; //project department

        $data['documentDatas'] = DB::table('proc_tech_documents as ptd')
        ->select([
            'ptd.*',
            DB::raw('(SELECT name FROM proc_tech_documents_type WHERE proc_tech_documents_type.id = ptd.doc_type) as doc_name')
        ])
        ->where('tech_id',$userId)->paginate(10);

        return view('tech.data-diri.upload-doc.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userId = Auth::user()->id;

        //supporting data
        $data['docDatas'] = DB::table('proc_tech_documents')->select('doc_type')->where('tech_id',$userId)->get()->pluck('doc_type')->toArray();
        
        $data['docTypes'] = DB::table('proc_tech_documents_type')->get();

        return view('tech.data-diri.upload-doc.create',$data);
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

        $request->validate([
            'doc_type' => 'required',
            'image' => 'required|mimes:jpeg,jpg,png,pdf|max:2048',
        ]);

        //file handler
        $fileName = null;
        $destinationPath = public_path().'/img/upload-doc/tech/';
        
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
        $data['tech_id'] = $userId;
        $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        
        if (!empty($fileName)) {
            $data['image'] = $fileName;
        }
        DB::table('proc_tech_documents')->insert($data);

        return redirect()->route('tech-input-data-diri.index')->with('alert-success','Data berhasil disimpan.');
        //return redirect()->route('tech-input-doc.index')->with('alert-success','Data berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Edit dokumen.');
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

        $firstCheck = DB::table('proc_tech_documents')->where('id',$id)->where('tech_id',$userId)->first();
        
        if (!isset($firstCheck)) {
            return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
        }

        $data['docData'] = $firstCheck;

        //supporting data
        $data['docDatas'] = DB::table('proc_tech_documents')->select('doc_type')->where('tech_id',$userId)->get()->pluck('doc_type')->toArray();
        $data['docTypes'] = DB::table('proc_tech_documents_type')->get();

        return view('tech.data-diri.upload-doc.edit',$data);
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

        $firstCheck = DB::table('proc_tech_documents')->where('id',$id)->where('tech_id',$userId)->first();
        
        if (!isset($firstCheck)) {
            return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
        }

        if (isset($request->image)) {
            $request->validate([
                'doc_type' => 'required',
                'image' => 'required|mimes:jpeg,jpg,png,pdf|max:2048',
            ]);
        }else{
            $request->validate([
                'doc_type' => 'required',
            ]);
        }

        //file handler
        $fileName = null;
        $destinationPath = public_path().'/img/upload-doc/tech/';
        
        // Retrieving An Uploaded File
        $file = $request->file('image');
        if (!empty($file)) {
            $extension = $file->getClientOriginalExtension();
            $fileName = time().'_'.$file->getClientOriginalName();

            // Moving An Uploaded File
            $request->file('image')->move($destinationPath, $fileName);

            //removing previous image
            $dataImage = DB::table('proc_tech_documents')->select('image')->where('id', $id)->first();
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
        $data['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
        
        if (!empty($fileName)) {
            $data['image'] = $fileName;
        }
        DB::table('proc_tech_documents')->where('id',$id)->update($data);

        return redirect()->route('tech-input-data-diri.index')->with('alert-success','Data berhasil diubah.');
        //return redirect()->route('tech-input-doc.index')->with('alert-success','Data berhasil diubah.');
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

        $firstCheck = DB::table('proc_tech_documents')->where('tech_id',$userId)->where('id',$id)->first();
        
        if (!isset($firstCheck)) {
            return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
        }

        //update kwitansi
        $fileName = null;
        $destinationPath = public_path().'/img/upload-doc/tech/';
        
        //removing previous image
        $dataImage = DB::table('proc_tech_documents')->where('id', $id)->first();
        $oldImage = $dataImage->image;

        if($oldImage !== 'default.png'){
            $image_path = $destinationPath.$oldImage;
            if(File::exists($image_path)) {
                File::delete($image_path);
            }
        }

        DB::table('proc_tech_documents')->delete($id);

        return redirect()->back()->with('alert-success','Data berhasil dihapus.');
    }
}
