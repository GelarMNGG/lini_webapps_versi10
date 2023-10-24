<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Auth;
use DB;

class UserMinutesController extends Controller
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
    public function index(Request $request)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userDepartment = Auth::user()->department_id;
        
        $skin = $request->skin;
        $date = $request->date;
        $data['date'] = $date;

        if ($date != NULL) {
            if ($skin == 1) {
                $data['skin'] = 0;
                $data['skinBack'] = 1;
    
                $data['userMinutes'] = DB::table('minutes as min')
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
    
                $data['userMinutes'] = DB::table('minutes as min')
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
    
                return view('user.minutes.index',$data);
            }
        }else{
            if ($skin == 1) {
                $data['skin'] = 0;
                $data['skinBack'] = 1;
    
                $data['userMinutes'] = DB::table('minutes as min')
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
    
                return view('user.minutes.index-table',$data);
            }else{
                $data['skin'] = 1;
                $data['skinBack'] = 0;
    
                $data['userMinutes'] = DB::table('minutes as min')
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
    
                return view('user.minutes.index',$data);
            }
        }

        /*
        if ($skin == 1) {
            $data['skin'] = 0;
            $data['skinBack'] = 1;

            $data['userMinutes'] = DB::table('minutes as min')
            ->select([
                'min.*',
                DB::raw('(SELECT name FROM department WHERE department.id = min.department_id) as department_name'),
                //category
                DB::raw('(SELECT name FROM minutes_category WHERE minutes_category.id = min.minute_cat AND minutes_category.department_id = '.$userDepartment.') as category_name'),
                //DB::raw('HOUR(even_start)'),
            ])
            ->where('publisher_department',$userDepartment)
            ->where('publisher_id',$userId)
            ->orderBy('date','DESC')
            ->orderBy('status','ASC')
            ->orderBy(DB::raw('HOUR(event_start)'),'DESC')
            ->get();

            return view('user.minutes.index-table',$data);
        }else{
            $data['skin'] = 1;
            $data['skinBack'] = 0;

            $data['userMinutes'] = DB::table('minutes as min')
            ->select([
                'min.*',
                DB::raw('(SELECT name FROM department WHERE department.id = min.department_id) as department_name'),
                //category
                DB::raw('(SELECT name FROM minutes_category WHERE minutes_category.id = min.minute_cat AND minutes_category.department_id = '.$userDepartment.') as category_name'),
            ])
            ->where('publisher_department',$userDepartment)
            ->where('publisher_id',$userId)
            ->orderBy('date','DESC')
            ->orderBy('status','ASC')
            ->orderBy(DB::raw('HOUR(event_start)'),'DESC')
            ->paginate(10);

            return view('user.minutes.index',$data);
        }
        */
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $data['skin'] = $request->skin;

        if ($userCompany == 2) {
            $lintaslogDepartment = 10; //lintaslog
            $data['departmentDatas'] = DB::table('department_lintaslog')->get();
            $data['minutesCats'] = DB::table('minutes_category')->where('department_id',$lintaslogDepartment)->get();
        }else{
            $data['departmentDatas'] = DB::table('department')->get();
            $data['minutesCats'] = DB::table('minutes_category')->where('department_id',$userDepartment)->get();
        }

        return view('user.minutes.create',$data);
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

        $request->validate([
            'name' => 'required',
            'event_start' => 'required',
            'event_end' => 'required|after:event_start',
            'image' => 'mimes:jpeg,jpg,png|max:9216',
        ]);

        //file handler
        $fileName = null;
        $destinationPath = public_path().'/img/minutes/user/';
        
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

        if (isset($request->date)) {
            $data['date'] = date('Y-m-d H:i:s', strtotime($request->date));
        }else{
            $data['date'] = Carbon::now()->format('Y-m-d H:i:s');
        }
        if ($request->status == 1 && $request->done_date == null) {
            $data['done_date'] = Carbon::now()->format('Y-m-d H:i:s');
            $data['percentage'] = 100;
        }
        
        $data['department_id'] = $userDepartment;
        $data['publisher_id'] = $userId;
        $data['publisher_type'] = $userType;
        $data['publisher_department'] = $userDepartment;
        $data['publisher_company'] = $userCompany;
        $data['event_start'] = date('H:i:s', strtotime($request->event_start));
        $data['event_end'] = date('H:i:s', strtotime($request->event_end));

        if (!empty($fileName)) {
            $data['image'] = $fileName;
        }

        DB::table('minutes')->insert($data);

        return redirect()->route('user-minutes.index')->with('alert-success','Data berhasil disimpan.');
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
    public function edit(Request $request, $id)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        //custom param
        $data['skin'] = substr($id,-1);
        $id = explode('&', $id)[0];

        if ($userCompany == 2) {
            $lintaslogDepartment = 10; //lintaslog
            $data['departmentDatas'] = DB::table('department_lintaslog')->get();
            $data['minutesCats'] = DB::table('minutes_category')->where('department_id',$lintaslogDepartment)->get();
        }else{
            $data['departmentDatas'] = DB::table('department')->get();
            $data['minutesCats'] = DB::table('minutes_category')->where('department_id',$userDepartment)->get();
        }

        $data['userMinute'] = DB::table('minutes')->where('id',$id)->where('publisher_id',$userId)->where('publisher_type',$userType)->where('publisher_department',$userDepartment)->first();

        if (isset($data['userMinute'])) {
            return view('user.minutes.edit', $data);
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
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $request->validate([
            'name' => 'required',
            'event_start' => 'required',
            'event_end' => 'required|after:event_start',
            'image' => 'mimes:jpeg,jpg,png|max:9216',
        ]);

        $dataImage = DB::table('minutes')->where('id', $id)->where('publisher_id',$userId)->where('publisher_type',$userType)->where('publisher_department',$userDepartment)->first();

        if (!isset($dataImage)) {
            return redirect()->back()->with('alert-danger','Gagal input. Cobalah beberapa saat lagi.');
        }

        //file handler
        $fileName = null;
        $destinationPath = public_path().'/img/minutes/user/';
        
        // Retrieving An Uploaded File
        $file = $request->file('image');
        if (!empty($file)) {
            $extension = $file->getClientOriginalExtension();
            $fileName = time().'_'.$file->getClientOriginalName();
    
            // Moving An Uploaded File
            $request->file('image')->move($destinationPath, $fileName);

            //delete previous image
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

        $data['date'] = date('Y-m-d H:i:s', strtotime($request->date));
        if ($request->status == 1 && $request->done_date == null) {
            $data['done_date'] = Carbon::now()->format('Y-m-d H:i:s');
            $data['percentage'] = 100;
        }
        
        $data['publisher_id'] = $userId;
        $data['publisher_department'] = $userDepartment;
        $data['publisher_company'] = $userCompany;
        $data['event_start'] = date('H:i:s', strtotime($request->event_start));
        $data['event_end'] = date('H:i:s', strtotime($request->event_end));

        if (!empty($fileName)) {
            $data['image'] = $fileName;
        }

        DB::table('minutes')->where('id',$id)->update($data);

        return redirect()->route('user-minutes.index')->with('alert-success','Data berhasil diubah.');
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
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $dataCheck = DB::table('minutes')->where('id', $id)->where('publisher_id',$userId)->where('publisher_type',$userType)->where('publisher_department',$userDepartment)->first();

        if (!isset($dataCheck)) {
            return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
        }

        //delete previous image
        $destinationPath = public_path().'/img/minutes/user/';
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

        return redirect()->route('user-minutes.index')->with('alert-success', 'Data berhasil dihapus.');
    }

    public function customReport(Request $request)
    {
        $userId = Auth::user()->id;
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;
        $published = 1;
        
        $skin = $request->skin;
        $date = $request->date;
        $data['date'] = $date;

        if ($date != NULL) {
            if ($skin == 1) {
                $data['skin'] = 0;
                $data['skinBack'] = 1;
    
                $data['userMinutes'] = DB::table('minutes as min')
                ->select([
                    'min.*',
                    DB::raw('(SELECT name FROM department WHERE department.id = min.department_id) as department_name'),
                    //category
                    DB::raw('(SELECT name FROM minutes_category WHERE minutes_category.id = min.minute_cat AND minutes_category.department_id = '.$userDepartment.') as category_name'),
                ])
                ->whereRaw( "(date >= ? AND date <= ?)", [$date." 00:00:00", $date." 23:59:59"])
                ->where('publisher_department',$userDepartment)
                ->where('publisher_id',$userId)
                ->orderBy('date','DESC')
                ->orderBy('status','ASC')
                ->orderBy(DB::raw('HOUR(event_start)'),'DESC')
                ->get();
    
                return view('user.minutes.index-table',$data);
            }else{
                $data['skin'] = 1;
                $data['skinBack'] = 0;
    
                $data['userMinutes'] = DB::table('minutes as min')
                ->select([
                    'min.*',
                    DB::raw('(SELECT name FROM department WHERE department.id = min.department_id) as department_name'),
                    //category
                    DB::raw('(SELECT name FROM minutes_category WHERE minutes_category.id = min.minute_cat AND minutes_category.department_id = '.$userDepartment.') as category_name'),
                ])
                ->whereRaw( "(date >= ? AND date <= ?)", [$date." 00:00:00", $date." 23:59:59"])
                ->where('publisher_department',$userDepartment)
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
    
                $data['userMinutes'] = DB::table('minutes as min')
                ->select([
                    'min.*',
                    DB::raw('(SELECT name FROM department WHERE department.id = min.department_id) as department_name'),
                    //category
                    DB::raw('(SELECT name FROM minutes_category WHERE minutes_category.id = min.minute_cat AND minutes_category.department_id = '.$userDepartment.') as category_name'),
                    //DB::raw('HOUR(even_start)'),
                ])
                ->where('publisher_department',$userDepartment)
                ->where('publisher_id',$userId)
                ->orderBy('date','DESC')
                ->orderBy('status','ASC')
                ->orderBy(DB::raw('HOUR(event_start)'),'DESC')
                ->get();
    
                return view('user.minutes.index-table',$data);
            }else{
                $data['skin'] = 1;
                $data['skinBack'] = 0;
    
                $data['userMinutes'] = DB::table('minutes as min')
                ->select([
                    'min.*',
                    DB::raw('(SELECT name FROM department WHERE department.id = min.department_id) as department_name'),
                    //category
                    DB::raw('(SELECT name FROM minutes_category WHERE minutes_category.id = min.minute_cat AND minutes_category.department_id = '.$userDepartment.') as category_name'),
                ])
                ->where('publisher_department',$userDepartment)
                ->where('publisher_id',$userId)
                ->orderBy('date','DESC')
                ->orderBy('status','ASC')
                ->orderBy(DB::raw('HOUR(event_start)'),'DESC')
                ->paginate(10);
    
                return view('user.minutes.index-custom',$data);
            }
        }

        return view('user.minutes.index-custom',$data);
    }
}
