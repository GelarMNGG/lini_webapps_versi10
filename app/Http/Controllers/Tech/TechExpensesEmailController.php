<?php

namespace App\Http\Controllers\Tech;

use App\Mail\TechExpensesEmail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class TechExpensesEmailController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:tech');
    }

    public function index()
    {
        Mail::to("anto.indonesia1@gmail.com")->send(new TechExpensesEmail());
 
		return "Email telah dikirim";
    }
}
