<?php

namespace App\Http\Controllers\Api\Tech;

use App\Http\Controllers\Api\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;

class TroubleshootingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:tech-api', ['except' => ['login']]);
    }
    public function all(Request $request)
    {
        $user = $this->authUser();

        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }

        $techId = $user->id;
        $userType = $user->user_type;

        //get data
        $data['troubleshootings'] = DB::table('troubleshooting')->where('publisher_id',$techId)->where('publisher_type',$userType)
        ->orderBy('status','ASC')
        ->orderBy('date','DESC')
        ->get();

        
        return response()->json($data);
    }
    public function store(Request $request)
    {
        $user = $this->authUser();
        $data = $request->only(['title','problem','solution', 'event_end','image']);

        if (!isset($data['title'])) {
            return response()->json(['error' => 'Maaf, Anda harus mengisi judul kasus.']);
        }
        if (!isset($data['problem'])) {
            return response()->json(['error' => 'Maaf, Anda harus mengisi Deskripsi Masalah.']);
        }
        if (!isset($data['solution'])) {
            return response()->json(['error' => 'Maaf, Anda harus mengisi Langkah-langkah penyelesaian.']);
        }

        $techId = $user->id;
        $userType = $user->user_type;

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
        
        $data['publisher_id'] = $techId;
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
                $dataNotif['publisher_id'] = $techId;
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

        return response()->json(['message' => 'Data berhasil disimpan.']);
    }
    public function show($id)
    {
        
        $user = $this->authUser();
        $techId = $user->id;
        $userType = $user->user_type;
        
        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }

        $firstCheck = DB::table('troubleshooting')->where('id',$id)->where('publisher_id',$techId)->where('publisher_type',$userType)->count();

        if ($firstCheck < 0) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }

        //get data
        $data['troubleshootingData'] = DB::table('troubleshooting')->where('id',$id)->first();

        $publisherId = $data['troubleshootingData']->publisher_id;

        //add view
        if ($techId != $publisherId) {
            DB::table('troubleshooting')->where('id',$id)->increment('view',1);
        }

        return response()->json($data);
    }
    public function update(Request $request, $id)
    {
        $user = $this->authUser();
        $techId = $user->id;
        $userType = $user->user_type;
        
        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }

        //firstcheck
        $firstCheck = DB::table('troubleshooting')->where('id',$id)->where('publisher_id',$techId)->where('publisher_type',$userType)->count();

        if ($firstCheck < 0) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
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
        
        $data['publisher_id'] = $techId;
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
                $dataNotif['publisher_id'] = $techId;
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

        return response()->json(['message' => 'Data berhasil diubah.']);
    }
    public function destroy($id)
    {
        $user = $this->authUser();
        $techId = $user->id;
        $userType = $user->user_type;

        //firstcheck
        $firstCheck = DB::table('troubleshooting')->where('id',$id)->where('publisher_id',$techId)->where('publisher_type',$userType)->count();

        if ($firstCheck < 0) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
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

        return response()->json(['message' => 'Data berhasil dihapus.']);
    }
}
