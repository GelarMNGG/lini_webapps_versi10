<?php

namespace App\Http\Controllers;

use App\Mail\SignupEmail;
use App\Mail\TechExpensesEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use DB;

class MailController extends Controller
{
    public static function sendSignupEmail($name, $email, $type, $verification_code, $verify)
    {
        $data = [
            'name' => $name,
            'type' => $type,
            'link' => $verify,
            'verification_code' => $verification_code
        ];
        Mail::to($email)->send(new SignupEmail($data));
    }

    //expenses
    public static function sendExpenseEmail($name, $email, $code, $amount, $sitename, $ccEmail,$expenseId)
    {
        $files = DB::table('project_expenses_files')->where('expense_id',$expenseId)->get();

        $data = [
            'name' => $name,
            'code' => $code,
            'amount' => $amount,
            'sitename' => $sitename,
            'images' => $files,
            'email_cc' => $ccEmail,
        ];
        Mail::to($email)->send(new TechExpensesEmail($data));
    }
}
