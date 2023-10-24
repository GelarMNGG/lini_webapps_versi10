<?php

namespace App\Http\Controllers\Admin\Proc;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class QuestionController extends Controller
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
        $userType = Auth::user()->user_type;
        $userRole = Auth::user()->role;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if($userCompany == 1 && $userDepartment == 9 || $userRole == 1){
            $data['dataQuestions'] = DB::table('proc_questions as pq')
            ->select([
                'pq.*',
                DB::raw('(SELECT name FROM proc_questions_category WHERE proc_questions_category.id = pq.cat_id) as category_name')
            ])
            ->paginate(10);
    
            return view('admin.proc.proc-question.index',$data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Talent Pool');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userRole = Auth::user()->role;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if($userCompany == 1 && $userDepartment == 9 || $userRole == 1){
            $data['questionCats'] = DB::table('proc_questions_category')->get();
            return view('admin.proc.proc-question.create',$data);
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Talent Pool');
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
        $userRole = Auth::user()->role;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if($userCompany == 1 && $userDepartment == 9 || $userRole == 1){
                $request->validate([
                    'question' => 'required',
                    'answer' => 'required',
                    ]);
               $data = $request->except(['_token','submit']);
               DB::table('proc_questions')->insert($data);

            return redirect()->route('admin-proc-question.index')->with('alert-success','Data berhasil disimpan');
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Talent Pool');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Talent Pool');
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
        $userRole = Auth::user()->role;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if($userCompany == 1 && $userDepartment == 9 || $userRole == 1){
            $data['questionCats'] = DB::table('proc_questions_category')->get();
            $data['dataQuestion'] = DB::table('proc_questions')->where('id',$id)->first();
            return view('admin.proc.proc-question.edit',$data);
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Talent Pool');
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
        $userRole = Auth::user()->role;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if($userCompany == 1 && $userDepartment == 9 || $userRole == 1){
            $request->validate([
                'question' => 'required',
                'answer' => 'required',
                'cat_id' => 'required'
                ]);
            
            $data = $request->except(['_token','_method','submit']);
            DB::table('proc_questions')->where('id',$id)->update($data);
        
            return redirect()->route('admin-proc-question.index')->with('alert-success','Data berhasil disimpan');
        }
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Talent Pool');
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
        $userRole = Auth::user()->role;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        if($userCompany == 1 && $userDepartment == 9 || $userRole == 1){
               //delete from database
               DB::table('proc_questions')->delete($id);
               return redirect()->route('admin-proc-question.index')->with('alert-success','Data berhasil dihapus.');
        }   

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Talent Pool');
    }
}
