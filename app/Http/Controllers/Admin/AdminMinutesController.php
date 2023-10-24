<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Auth;
use DB;

class AdminMinutesController extends Controller
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
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;

        // new version
        $skin = $request->skin;
        $date = $request->date;
        $data['date'] = $date;

        if ($date != NULL) {
            if ($skin == 1) {
                $data['skin'] = 0;
                $data['skinBack'] = 1;
    
                $data['adminMinutes'] = DB::table('minutes as min')
                ->select([
                    'min.*',
                    DB::raw('(SELECT name FROM department WHERE department.id = min.department_id) as department_name'),
                    //category
                    DB::raw('(SELECT name FROM minutes_category WHERE minutes_category.id = min.minute_cat AND minutes_category.department_id = '.$userDepartment.') as category_name'),
                ])
                ->whereRaw( "(date >= ? AND date <= ?)", [$date." 00:00:00", $date." 23:59:59"])
                ->where('publisher_department',$userDepartment)
                ->where('publisher_type',$userType)
                ->where('publisher_id',$userId)
                ->orderBy('date','DESC')
                ->orderBy('status','ASC')
                ->orderBy(DB::raw('HOUR(event_start)'),'DESC')
                ->get();
    
                return view('admin.minutes.index-table',$data);
            }else{
                $data['skin'] = 1;
                $data['skinBack'] = 0;
    
                $data['adminMinutes'] = DB::table('minutes as min')
                ->select([
                    'min.*',
                    DB::raw('(SELECT name FROM department WHERE department.id = min.department_id) as department_name'),
                    //category
                    DB::raw('(SELECT name FROM minutes_category WHERE minutes_category.id = min.minute_cat AND minutes_category.department_id = '.$userDepartment.') as category_name'),
                ])
                ->whereRaw( "(date >= ? AND date <= ?)", [$date." 00:00:00", $date." 23:59:59"])
                ->where('publisher_department',$userDepartment)
                ->where('publisher_type',$userType)
                ->where('publisher_id',$userId)
                ->orderBy('date','DESC')
                ->orderBy('status','ASC')
                ->orderBy(DB::raw('HOUR(event_start)'),'DESC')
                ->paginate(10);
    
                return view('admin.minutes.index',$data);
            }
        }else{
            if ($skin == 1) {
                $data['skin'] = 0;
                $data['skinBack'] = 1;
    
                $data['adminMinutes'] = DB::table('minutes as min')
                ->select([
                    'min.*',
                    DB::raw('(SELECT name FROM department WHERE department.id = min.department_id) as department_name'),
                    //category
                    DB::raw('(SELECT name FROM minutes_category WHERE minutes_category.id = min.minute_cat AND minutes_category.department_id = '.$userDepartment.') as category_name'),
                    //DB::raw('HOUR(even_start)'),
                ])
                ->where('publisher_department',$userDepartment)
                ->where('publisher_type',$userType)
                ->where('publisher_id',$userId)
                ->orderBy('date','DESC')
                ->orderBy('status','ASC')
                ->orderBy(DB::raw('YEAR(date), MONTH(date), HOUR(event_start)'),'DESC')
                ->get();
    
                return view('admin.minutes.index-table',$data);
            }else{
                $data['skin'] = 1;
                $data['skinBack'] = 0;
    
                $data['adminMinutes'] = DB::table('minutes as min')
                ->select([
                    'min.*',
                    DB::raw('(SELECT name FROM department WHERE department.id = min.department_id) as department_name'),
                    //category
                    DB::raw('(SELECT name FROM minutes_category WHERE minutes_category.id = min.minute_cat AND minutes_category.department_id = '.$userDepartment.') as category_name'),
                ])
                ->where('publisher_department',$userDepartment)
                ->where('publisher_type',$userType)
                ->where('publisher_id',$userId)
                ->orderBy('date','DESC')
                ->orderBy('status','ASC')
                ->orderBy(DB::raw('YEAR(date), MONTH(date), HOUR(event_start)'),'DESC')
                ->paginate(10);
    
                return view('admin.minutes.index',$data);
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
        $userDepartment = Auth::user()->department_id;
        $data['minutesCats'] = DB::table('minutes_category')->where('department_id',$userDepartment)->get();

        return view('admin.minutes.create', $data);
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
        $userDepartment = Auth::user()->department_id;

        $request->validate([
            'name' => 'required',
            'event_start' => 'required',
            'event_end' => 'required|after:event_start',
        ]);

        //file handler
        $fileName = null;
        $destinationPath = public_path().'/img/minutes/admin/';
        
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
        $data['publisher_department'] = $userDepartment;

        if (isset($request->date)) {
            $data['date'] = date('Y-m-d H:i:s', strtotime($request->date));
        }else{
            $data['date'] = Carbon::now()->format('Y-m-d H:i:s');
        }
        $data['event_start'] = date('H:i:s', strtotime($request->event_start));
        $data['event_end'] = date('H:i:s', strtotime($request->event_end));

        if (!empty($fileName)) {
            $data['image'] = $fileName;
        }

        DB::table('minutes')->insert($data);

        return redirect()->route('admin-minutes.index')->with('alert-success','Data berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Activities.');
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
        $userDepartment = Auth::user()->department_id;

        $data['adminMinute'] = DB::table('minutes')->where('id',$id)->where('publisher_id',$userId)->where('publisher_type',$userType)->where('publisher_department',$userDepartment)->first();
        $data['minutesCats'] = DB::table('minutes_category')->where('department_id',$userDepartment)->get();

        if (isset($data['adminMinute'])) {
            return view('admin.minutes.edit', $data);
        }

        return redirect()->back()->with('alert-danger','Halaman yang Anda tuju, tidak tersedia.');
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
        $userDepartment = Auth::user()->department_id;

        $firstCheck = DB::table('minutes')->where('id',$id)->where('publisher_id',$userId)->where('publisher_type',$userType)->where('publisher_department',$userDepartment)->count();

        if ($firstCheck < 1) {
            return redirect()->back()->with('alert-danger','Gagal input. Cobalah beberapa saat lagi.');
        }

        $request->validate([
            'name' => 'required',
            'event_start' => 'required',
            'event_end' => 'required|after:event_start',
        ]);

        //file handler
        $fileName = null;
        $destinationPath = public_path().'/img/minutes/admin/';
        
        // Retrieving An Uploaded File
        $file = $request->file('image');
        if (!empty($file)) {
            $extension = $file->getClientOriginalExtension();
            $fileName = time().'_'.$file->getClientOriginalName();
    
            // Moving An Uploaded File
            $request->file('image')->move($destinationPath, $fileName);

            //delete previous image
            $dataImage = DB::table('minutes')->select('image as image')->where('id', $id)->first();
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
        if ($request->status == 1 && $request->done_date == null) {
            $data['done_date'] = Carbon::now()->format('Y-m-d H:i:s');
            $data['percentage'] = 100;
        }
        
        $data['publisher_id'] = $userId;
        $data['publisher_department'] = $userDepartment;
        $data['date'] = date('Y-m-d H:i:s', strtotime($request->date));
        $data['event_start'] = date('H:i:s', strtotime($request->event_start));
        $data['event_end'] = date('H:i:s', strtotime($request->event_end));

        if (!empty($fileName)) {
            $data['image'] = $fileName;
        }

        DB::table('minutes')->where('id',$id)->update($data);

        return redirect()->route('admin-minutes.index')->with('alert-success','Data berhasil diubah.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //delete previous image
        $destinationPath = public_path().'/img/minutes/admin/';
        $dataImage = DB::table('minutes')->select('image as image')->where('id', $id)->first();
        $oldImage = $dataImage->image;

        if($oldImage !== 'default.png'){
            $image_path = $destinationPath.$oldImage;
            if(File::exists($image_path)) {
                File::delete($image_path);
            }
        }

        //delete from database
        DB::table('minutes')->delete($id);

        return redirect()->route('admin-minutes.index')->with('alert-success', 'Data berhasil dihapus.');
    }

    //custom report
    public function customReport(Request $request)
    {
        $userId = Auth::user()->id;
        $userLevel = Auth::user()->user_level;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;
        $published = 1;
        
        $skin = $request->skin;
        $date = $request->date;
        $data['date'] = $date;

        if ($date != NULL) {
            if ($skin == 1) {
                $data['skin'] = 0;
                $data['skinBack'] = 1;
    
                $data['adminMinutes'] = DB::table('minutes as min')
                ->select([
                    'min.*',
                    DB::raw('(SELECT name FROM department WHERE department.id = min.department_id) as department_name'),
                    //category
                    DB::raw('(SELECT name FROM minutes_category WHERE minutes_category.id = min.minute_cat AND minutes_category.department_id = '.$userDepartment.') as category_name'),
                ])
                ->whereRaw( "(date >= ? AND date <= ?)", [$date." 00:00:00", $date." 23:59:59"])
                ->where('publisher_department',$userDepartment)
                ->where('publisher_type',$userType)
                ->where('publisher_id',$userId)
                ->orderBy('date','DESC')
                ->orderBy('status','ASC')
                ->orderBy(DB::raw('HOUR(event_start)'),'DESC')
                ->get();
    
                return view('user.minutes.index-table',$data);
            }else{
                $data['skin'] = 1;
                $data['skinBack'] = 0;
    
                $data['adminMinutes'] = DB::table('minutes as min')
                ->select([
                    'min.*',
                    DB::raw('(SELECT name FROM department WHERE department.id = min.department_id) as department_name'),
                    //category
                    DB::raw('(SELECT name FROM minutes_category WHERE minutes_category.id = min.minute_cat AND minutes_category.department_id = '.$userDepartment.') as category_name'),
                ])
                ->whereRaw( "(date >= ? AND date <= ?)", [$date." 00:00:00", $date." 23:59:59"])
                ->where('publisher_department',$userDepartment)
                ->where('publisher_type',$userType)
                ->where('publisher_id',$userId)
                ->orderBy('date','DESC')
                ->orderBy('status','ASC')
                ->orderBy(DB::raw('HOUR(event_start)'),'DESC')
                ->paginate(10);
    
                return view('user.minutes.index-custom',$data);
            }
        }else{
            if ($skin == 1) {
                $data['skin'] = 0;
                $data['skinBack'] = 1;
    
                $data['adminMinutes'] = DB::table('minutes as min')
                ->select([
                    'min.*',
                    DB::raw('(SELECT name FROM department WHERE department.id = min.department_id) as department_name'),
                    //category
                    DB::raw('(SELECT name FROM minutes_category WHERE minutes_category.id = min.minute_cat AND minutes_category.department_id = '.$userDepartment.') as category_name'),
                    //DB::raw('HOUR(even_start)'),
                ])
                ->where('publisher_department',$userDepartment)
                ->where('publisher_type',$userType)
                ->where('publisher_id',$userId)
                ->orderBy('date','DESC')
                ->orderBy('status','ASC')
                ->orderBy(DB::raw('HOUR(event_start)'),'DESC')
                ->get();
    
                return view('admin.minutes.index-table',$data);
            }else{
                $data['skin'] = 1;
                $data['skinBack'] = 0;
    
                $data['adminMinutes'] = DB::table('minutes as min')
                ->select([
                    'min.*',
                    DB::raw('(SELECT name FROM department WHERE department.id = min.department_id) as department_name'),
                    //category
                    DB::raw('(SELECT name FROM minutes_category WHERE minutes_category.id = min.minute_cat AND minutes_category.department_id = '.$userDepartment.') as category_name'),
                ])
                ->where('publisher_department',$userDepartment)
                ->where('publisher_type',$userType)
                ->where('publisher_id',$userId)
                ->orderBy('date','DESC')
                ->orderBy('status','ASC')
                ->orderBy(DB::raw('HOUR(event_start)'),'DESC')
                ->paginate(10);
    
                return view('admin.minutes.index-custom',$data);
            }
        }

        return view('admin.minutes.index-custom',$data);
    }
}
