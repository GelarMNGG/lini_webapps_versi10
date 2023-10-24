<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class IndexController extends Controller
{
    public function draft()
    {
        //data
        $theStatus = 1;
        $data['sliders'] = DB::table('sliders')->where('status',$theStatus)->get();
        $data['slidersCount'] = DB::table('sliders')->where('status',$theStatus)->count();
        $data['clients'] = DB::table('clients')->get();
        $data['services'] = DB::table('services')->get();

        return view('pages.index', $data);
    }

    public function index()
    {
        /*
        //data
        $theStatus = 1;
        $data['sliders'] = DB::table('sliders')->where('status',$theStatus)->get();
        $data['slidersCount'] = DB::table('sliders')->where('status',$theStatus)->count();
        $data['clients'] = DB::table('clients')->get();
        $data['services'] = DB::table('services')->get();

        return view('pages.index', $data);
        */

        /** underconstruction v2 */
        return view('pages.home-2');
    }

    public function ourServices($slug)
    {
        $data['service'] = DB::table('services')->where('slug',$slug)->first();
        $serviceId = $data['service']->id;

        $data['serviceImages'] = DB::table('services_image')->where('service_id',$serviceId)->get();

        return view('pages.our-services', $data);
    }

    public function about()
    {
        return view('pages.about');
    }

    public function corporateCulture()
    {
        return view('pages.corporate-culture');
    }

    public function companyHistory()
    {
        return view('pages.sejarah-perusahaan');
    }

    public function blog()
    {
        $blogStatus = 1;
        $data['blogs'] = DB::table('blogs')->where('status', $blogStatus)->paginate(5);

        return view('pages.blog', $data);
    }

    public function blogDetail($slug)
    {
        $data['blog'] = DB::table('blogs')->where('slug', $slug)->first();

        return view('pages.blog-detail', $data);
    }

    public function contact()
    {
        return view('pages.kontak-kami')->with(compact('companyInfos'));
    }

    public function home()
    {
        return view('pages.home');
    }

    // test
    public function sendEmail()
    {
        return view('pages.send-email');
    }
    // test end
}
