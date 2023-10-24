<?php

namespace App\Http\Controllers\Admin\IT;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class PerformanceSummaryController extends Controller
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
        $userDepartment = Auth::user()->department_id;
        
        $published = 1;
        $staffType = 'user';

        if ($userDepartment == 5) {
            $data['dailyActivity'] = DB::table('minutes as min')
            ->select([
                'min.*',
                DB::raw('(SELECT firstname FROM users WHERE users.id = min.publisher_id) as firstname'),
                DB::raw('(SELECT lastname FROM users WHERE users.id = min.publisher_id) as lastname'),
                //category
                DB::raw('(SELECT name FROM minutes_category WHERE minutes_category.id = min.minute_cat AND minutes_category.department_id = '.$userDepartment.') as category_name'),
            ])
            ->where('publisher_type',$staffType)
            ->where('publisher_department',$userDepartment)
            ->where('published',$published)
            ->orderBy('status','ASC')
            ->orderBy(DB::raw('HOUR(event_start)'),'DESC')
            ->get();

            ###minutes by its status
                $one_week_ago = Carbon::now()->subDays(5)->format('Y-m-d');
                $now = Carbon::now()->format('Y-m-d');
                $minuteStatus = DB::table('minutes')
                ->where('date', '>=', $one_week_ago)
                ->where('department_id',$userDepartment)
                ->groupBy('date')
                ->orderBy('date', 'DESC')
                ->get(array(
                    DB::raw('Date(date) as date'),
                    DB::raw('COUNT(status = 0) as "onprogress"'),
                    DB::raw('COUNT(status = 1) as "done"'),
                ));
                //->pluck('date','onprogress','done');

                $data['minuteStatus'] = $minuteStatus;
                //$data['statusNameDatas'] = array_values($minuteStatus->pluck('date')->toArray());
                //dd($minuteStatus,$data['statusNameDatas']);
            ###minutes by its status end

            ###minutes by its category
                $minuteCats = DB::table('minutes_category as mincat')
                ->select([
                    'mincat.name',
                    DB::raw('(SELECT COUNT(*) FROM minutes WHERE minutes.minute_cat = mincat.id AND minutes.publisher_department = mincat.department_id) as cat_count'),
                ])
                ->where('department_id',$userDepartment)
                ->get()->pluck('name','cat_count');

                //$catNameDatas = json_encode($minuteCats);
                //$catNameDatas = array_key_exists($minuteCats,'cat_count');
                $data['catNameDatas'] = array_values($minuteCats->toArray());
                $data['catCountDatas'] = array_keys($minuteCats->toArray());
                //$data['datacatCountDatas'] = count($data['catCountDatas']);
                //$dataCount = array_count_values($data['catCountDatas']);
                $data['datacatCountDatas'] = array_sum($data['catCountDatas']);
            ###minutes by its category end

            ###minutes by its department
                $minuteDepts = DB::table('department as dept')
                ->select([
                    'dept.name',
                    DB::raw('(SELECT COUNT(*) FROM minutes WHERE minutes.department_id = dept.id) as dept_count'),
                ])
                ->get()->pluck('name','dept_count');

                $data['deptNameDatas'] = array_values($minuteDepts->toArray());
                $data['deptCountDatas'] = array_keys($minuteDepts->toArray());
                
                //$dataCount = array_count_values($data['deptCountDatas']);
                $data['dataDeptCountDatas'] = array_sum($data['deptCountDatas']);
            ###minutes by its department end
            
            ###coding & programming by programmer name
                $appsProgrammers = DB::table('users as usr')
                ->select([
                    'usr.firstname',
                    'usr.lastname',
                    DB::raw('(SELECT COUNT(*) FROM apps_development_logs WHERE apps_development_logs.programmer_id = usr.id) as prog_count'),
                ])
                ->where('department_id',$userDepartment)
                ->orderBy('prog_count','DESC')
                ->limit(3)->get()->pluck('firstname','prog_count');

                $data['progNameDatas'] = array_values($appsProgrammers->toArray());
                $data['progCountDatas'] = array_keys($appsProgrammers->toArray());
                
                $data['dataProgCountDatas'] = array_sum($data['progCountDatas']);
            ###coding & programming by programmer name end

            return view('admin.performance-summary', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Performance Summary.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Performance Summary.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Performance Summary.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Performance Summary.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Performance Summary.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Performance Summary.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Performance Summary.');
    }
}
