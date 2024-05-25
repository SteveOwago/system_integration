<?php

namespace App\Services;

use App\Mail\SendPaymentSuccessfulMail;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

/**
 * Class NotificationService.
 */
class NotificationService
{
    public function sendPaymentNotification($data){
        //Send Via Email
        $course = Course::where('id',$data['course_id'])->first()->value('name');
        $user = User::where('id',$data['user_id'])->first();;
        $mpesaRef = $data['mpesa_reference_number'];
        $amount = $data['amount'];
        $message = "Hello $user->name
                    Payment Successful! Your Payment of $amount to Enroll for $course have been received successfully.
                    Your Payment has been received under reference number: $mpesaRef";
        $email = $user->email;
        Mail::to($email)->cc("stevenowago@gmail.com")->send(new SendPaymentSuccessfulMail($message));
        //Send Via SMS
    }
}
