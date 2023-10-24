<?php

namespace App\Http\Controllers\Tech;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class TroubleshootingController extends Controller
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

        //count data
        $data['countData'] = DB::table('troubleshooting')->where('publisher_id',$userId)->where('publisher_type',$userType)->count();

        //get data
        $data['troubleshootings'] = DB::table('troubleshooting')->where('publisher_id',$userId)->where('publisher_type',$userType)
        ->orderBy('status','ASC')
        ->orderBy('date','DESC')
        ->get();

        //supporting data
        $data['statusDatas'] = DB::table('troubleshooting_status')->get();

        return view('tech.troubleshooting.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //supporting data
        $data['statusDatas'] = DB::table('troubleshooting_status')->get();

        return view('tech.troubleshooting.create', $data);
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

        $request->validate([
            'title' => 'required',
            'problem' => 'required|min:20',
            'solution' => 'required|min:20',
            'event_end' => 'after:event_start',
            'image' => 'mimes:jpeg,jpg,png|max:9216',
        ]);

        //file handler
        $fileName = null;
        $destinationPath = public_path().'/img/troubleshooting/';
        
        // Retrieving An Uploaded File
        $file = $request->file('image');
        if (!empty($file)) {
            $extension = $file->getClientOriginalExtension();
            $fileName = time().'_'.$file->getClientOriginalName();
    
            // Moving An Uploaded File
            $request->file('image')->move($destinationPath, $fileName);
        }

        //custom setting to support file upload
        $data = $request->except(['_token','submit']);
        
        $data['publisher_id'] = $userId;
        $data['publisher_type'] = $userType;
        $data['event_start'] = date('H:i:s', strtotime($request->event_start));
        $data['event_end'] = date('H:i:s', strtotime($request->event_end));

        if (!empty($fileName)) {
            $data['image'] = $fileName;
        }

        DB::table('troubleshooting')->insert($data);

        // 1 is project department
        //send notifications
            if ($request->status > 1) {
                $troubleshootingData = DB::table('troubleshooting')->orderBy('id','DESC')->first();
                $technicalSupportRole = 14;
                $projectDepartment = 1;
                ###publisher data
                $dataNotif['publisher_id'] = $userId;
                $dataNotif['publisher_type'] = $userType;
    
                $publisherName = Auth::user()->name;
                $publisherFirstname = Auth::user()->firstname;
                $publisherLastname = Auth::user()->lastname;
                if ($publisherFirstname !== null) {
                    $publisherName = ucfirst($publisherFirstname).' '.ucfirst($publisherLastname);
                }else{
                    $publisherName = ucfirst($publisherName);
                }
                ###receiver data
                $dataReceiverDepartments = DB::table('users')->where('department_id',$projectDepartment)->where('user_level',$technicalSupportRole)->get();
                
                foreach ($dataReceiverDepartments as $technicalSupportData) {
                    $dataNotif['receiver_id'] = $technicalSupportData->id;
                    $dataNotif['receiver_type'] = $technicalSupportData->user_type;
                    $dataNotif['receiver_department'] = $technicalSupportData->department_id;
                    $dataNotif['level'] = 2;
                    
                    ###notif message
                    $dataNotif['desc'] = "Membuat troubleshooting <a href='".route('troubleshooting.show',$troubleshootingData->id)."'><strong>".ucfirst($troubleshootingData->title)."</strong></a> untuk Anda review dan setujui.";
                    ###insert data to notifications table
                    DB::table('notifications')->insert($dataNotif);
                }
            }
        //send notifications end

        return redirect()->route('tech-troubleshooting.index')->with('alert-success','Data berhasil disimpan.');
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
        $userType = Auth::user()->user_type;

        //supporting data
        $data['techDatas'] = DB::table('techs')->get();
        $data['userDatas'] = DB::table('users')->get();

        $firstCheck = DB::table('troubleshooting')->where('id',$id)->where('publisher_id',$userId)->where('publisher_type',$userType)->count();

        if ($firstCheck < 1) {
            return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
        }

        //get data
        $data['troubleshootingData'] = DB::table('troubleshooting')->where('id',$id)->first();

        $publisherId = $data['troubleshootingData']->publisher_id;

        //add view
        if ($userId != $publisherId) {
            DB::table('troubleshooting')->where('id',$id)->increment('view',1);
        }
        
        //supporting data
        $data['statusDatas'] = DB::table('troubleshooting_status')->get();

        return view('tech.troubleshooting.show', $data);
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
        $userType = Auth::user()->user_type;

        //firstcheck
        $firstCheck = DB::table('troubleshooting')->where('id',$id)->where('publisher_id',$userId)->where('publisher_type',$userType)->count();

        if ($firstCheck < 1) {
            return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
        }

        //get data
        $data['troubleshootingData'] = DB::table('troubleshooting')->where('id',$id)->first();

        //supporting data
        $data['statusDatas'] = DB::table('troubleshooting_status')->get();

        return view('tech.troubleshooting.edit', $data);
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

        //firstcheck
        $firstCheck = DB::table('troubleshooting')->where('id',$id)->where('publisher_id',$userId)->where('publisher_type',$userType)->count();

        if ($firstCheck < 1) {
            return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
        }

        $request->validate([
            'title' => 'required',
            'problem' => 'required|min:20',
            'solution' => 'required|min:20',
            'event_end' => 'after:event_start',
            'image' => 'mimes:jpeg,jpg,png|max:9216',
        ]);

        //file handler
        $fileName = null;
        $destinationPath = public_path().'/img/troubleshooting/';
        
        // Retrieving An Uploaded File
        $file = $request->file('image');
        if (!empty($file)) {
            $extension = $file->getClientOriginalExtension();
            $fileName = time().'_'.$file->getClientOriginalName();
    
            // Moving An Uploaded File
            $request->file('image')->move($destinationPath, $fileName);

            //delete previous image
            $dataImage = DB::table('troubleshooting')->select('image as image')->where('id', $id)->first();
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
        
        $data['publisher_id'] = $userId;
        $data['publisher_type'] = $userType;
        $data['event_start'] = date('H:i:s', strtotime($request->event_start));
        $data['event_end'] = date('H:i:s', strtotime($request->event_end));

        if (!empty($fileName)) {
            $data['image'] = $fileName;
        }

        //update troubleshooting data
        DB::table('troubleshooting')->where('id',$id)->update($data);

        // 1 is project department
        //send notifications
            if ($request->status > 1) {
                $troubleshootingData = DB::table('troubleshooting')->orderBy('id','DESC')->first();
                $technicalSupportRole = 14;
                $projectDepartment = 1;
                ###publisher data
                $dataNotif['publisher_id'] = $userId;
                $dataNotif['publisher_type'] = $userType;

                $publisherName = Auth::user()->name;
                $publisherFirstname = Auth::user()->firstname;
                $publisherLastname = Auth::user()->lastname;
                if ($publisherFirstname !== null) {
                    $publisherName = ucfirst($publisherFirstname).' '.ucfirst($publisherLastname);
                }else{
                    $publisherName = ucfirst($publisherName);
                }
                ###receiver data
                $dataReceiverDepartments = DB::table('users')->where('department_id',$projectDepartment)->where('user_level',$technicalSupportRole)->get();
                
                foreach ($dataReceiverDepartments as $technicalSupportData) {
                    $dataNotif['receiver_id'] = $technicalSupportData->id;
                    $dataNotif['receiver_type'] = $technicalSupportData->user_type;
                    $dataNotif['receiver_department'] = $technicalSupportData->department_id;
                    $dataNotif['level'] = 2;
                    
                    ###notif message
                    $dataNotif['desc'] = "Membuat troubleshooting <a href='".route('troubleshooting.show',$troubleshootingData->id)."'><strong>".ucfirst($troubleshootingData->title)."</strong></a> untuk Anda review dan setujui.";
                    ###insert data to notifications table
                    DB::table('notifications')->insert($dataNotif);
                }
            }
        //send notifications end

        return redirect()->route('tech-troubleshooting.index')->with('alert-success','Data berhasil diubah.');
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
        $userType = Auth::user()->user_type;

        //firstcheck
        $firstCheck = DB::table('troubleshooting')->where('id',$id)->where('publisher_id',$userId)->where('publisher_type',$userType)->count();

        if ($firstCheck < 1) {
            return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
        }

        //delete previous image
        $destinationPath = public_path().'/img/troubleshooting/';
        $dataImage = DB::table('troubleshooting')->select('image as image')->where('id', $id)->first();
        $oldImage = $dataImage->image;

        if($oldImage !== 'default.png'){
            $image_path = $destinationPath.$oldImage;
            if(File::exists($image_path)) {
                File::delete($image_path);
            }
        }

        DB::table('troubleshooting')->delete($id);

        return redirect()->route('tech-troubleshooting.index')->with('alert-success','Data berhasil dihapus.');
    }
}
