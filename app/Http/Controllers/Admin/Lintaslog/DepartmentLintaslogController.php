<?php

namespace App\Http\Controllers\Admin\Lintaslog;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;

class DepartmentLintaslogController extends Controller
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
        $data['departments'] = DB::table('department_lintaslog')->get();

        return view('admin.lintaslog.department-lintaslog.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.lintaslog.department-lintaslog.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $data = $request->except(['_token','_method','submit']);
        $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('department_lintaslog')->insert($data);

        return redirect()->route('department-lintaslog.index')->with('success','Data berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['departmentById'] = DB::table('department_lintaslog')->where('id',$id)->first();

        #dd($id, $data);

        return view('admin.lintaslog.department-lintaslog.edit', $data);
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
        $request->validate([
            'name' => 'required',
        ]);

        $data = $request->except(['_token','_method','submit']);

        DB::table('department_lintaslog')->where('id',$id)->update($data);

        return redirect()->route('department-lintaslog.index')->with('success','Data berhasil diubah.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('department_lintaslog')->where('id',$id)->delete($id);

        return redirect()->route('department-lintaslog.index')->with('success','Data berhasil dihapus.');
    }
}
