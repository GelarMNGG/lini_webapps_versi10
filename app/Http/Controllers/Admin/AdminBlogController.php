<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Auth;
use DB;

class AdminBlogController extends Controller
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
        $blogStatus = 1;
        $data['blogs'] = DB::table('blogs')
        ->select([
            'blogs.*',
            DB::raw('(SELECT name FROM blogs_type WHERE blogs_type.id = blogs.type) as type_name')
        ])
        ->paginate(10);

        $data['users'] = DB::table('users')->get();
        $data['admins'] = DB::table('admins')->get();

        return view('admin.blog.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['blogsTypes'] = DB::table('blogs_type')->get();
        return view('admin.blog.create', $data);
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
            'tags' => 'required',
            'summary' => 'required',
            'content' => 'required',
            'image' => 'mimes:jpeg,jpg,png|max:1024',
        ]);

        //file handler
        $fileName = null;
        $destinationPath = public_path().'/img/blogs/';
        
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
        $data['slug'] = Str::slug($request->title);
        $data['author_id'] = $userId;
        $data['author_type'] = $userType;
        
        #$data = $request->all();
        if (!empty($fileName)) {
            $data['image'] = $fileName;
        }

        DB::table('blogs')->insert($data);

        return redirect()->route('admin-blog.index')->with('alert-success','Data berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['blog'] = DB::table('blogs')->where('id',$id)->first();

        if (isset($data['blog'])) {

            $data['users'] = DB::table('users')->get();
            $data['admins'] = DB::table('admins')->get();
            
            return view('admin.blog.show', $data);
        }
        return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['blog'] = DB::table('blogs')->where('id',$id)->first();

        return view('admin.blog.edit', $data);
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
        $userType = Auth::user()->user_type;

        $request->validate([
            'title' => 'required',
            'tags' => 'required',
            'summary' => 'required',
            'content' => 'required',
            'image' => 'mimes:jpeg,jpg,png|max:1024',
        ]);

        //file handler
        $fileName = null;
        $destinationPath = public_path().'/img/blogs/';
        
        // Retrieving An Uploaded File
        $file = $request->file('image');
        if (!empty($file)) {
            $extension = $file->getClientOriginalExtension();
            $fileName = time().'_'.$file->getClientOriginalName();
    
            // Moving An Uploaded File
            $request->file('image')->move($destinationPath, $fileName);

            //delete previous image
            $dataImage = DB::table('blogs')->select('image as image')->where('id', $id)->first();
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
        $data['slug'] = Str::slug($request->title);
        
        #$data = $request->all();
        if (!empty($fileName)) {
            $data['image'] = $fileName;
        }

        DB::table('blogs')->where('id', $id)->update($data);

        return redirect()->route('admin-blog.index')->with('alert-success','Data berhasil diubah.');
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

        $destinationPath = public_path().'/img/blogs/';
        
        //delete image
        $dataImage = DB::table('blogs')->select('image as image')->where('id', $id)->where('author_id',$userId)->first();

        if (isset($dataImage)) {
            $oldImage = $dataImage->image;
    
            if($oldImage !== 'default.png'){
                $image_path = $destinationPath.$oldImage;
                if(File::exists($image_path)) {
                    File::delete($image_path);
                }
            }
    
            //delete category
            DB::table('blogs')->delete($id);
    
            return redirect()->route('admin-blog.index')->with('alert-success', 'Data berhasil dihapus.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan menghapus artikel ini.');
    }
}
