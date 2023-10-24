<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Auth;
use DB;

class TechExpensesEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->email_data = $data;
        //$this->middleware('auth:tech');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $userId = Auth::user()->id;
        $techEmail = DB::table('techs')->where('id',$userId)->first();

        $email = $this->from($techEmail->email,ucfirst($techEmail->firstname).' '.ucfirst($techEmail->lastname))
            ->subject($this->email_data['code'].' '.$this->email_data['name'].' Rp '.$this->email_data['amount'].' Site : '.$this->email_data['sitename'])
            //->cc(['anto@limaintisinergi.com','adi.nariswara@limaintisinergi.com'])
            ->cc($this->email_data['email_cc'])
            ->view('mail.tech-expenses',['email_data' => $this->email_data]);

        // send the data
        return $email;
    }
}