<?php

namespace App\Services;

use App\Models\Course;
use App\Models\MpesaStk;

/**
 * Class MpesaService.
 */
class MpesaService
{
    public function stkPush($phone, $user, $course)
    {
        //Get Course Prize
        $amount = Course::where('id', $course->id)->first()->value('price');
        $amountPay = number_format($amount, 0);
        $phone = '254' . substr($phone, -9, 9);
        try {
            $environment = "sandbox";
            if (env("MPESA_ENVIRONMENT") == 'live') {
                $environment = 'api';
            }
            if ($environment == "sandbox") {
                $amountPay = 1;
            }
            $auth_url = 'https://' . $environment . '.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'; //replace sandbox with api for live
            $stk_push_url = 'https://' . $environment . '.safaricom.co.ke/mpesa/stkpush/v1/processrequest'; //replace sandbox with api for live
            $consumer_key = env("MPESA_CONSUMER_KEY");
            $consumer_secret = env("MPESA_CONSUMER_SECRET");
            $credentials = base64_encode($consumer_key . ':' . $consumer_secret);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $auth_url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $curl_response = curl_exec($ch);
            $access_token = json_decode($curl_response)->access_token;
            curl_setopt($ch, CURLOPT_URL, $stk_push_url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $access_token));
            $timestamp = date('YmdHis');
            $passkey = env("MPESA_PASSKEY");
            $shortCode = env("MPESA_BUSINESS_SHORTCODE");
            $password = base64_encode($shortCode . $passkey . $timestamp);

            $curl_post_data = [
                'BusinessShortCode'      => $shortCode,
                'Password'               => $password,
                'Timestamp'              => $timestamp,
                'TransactionType'        => 'CustomerPayBillOnline',
                'Amount'                 => $amountPay,
                'PartyA'                 => $phone,
                'PartyB'                 => $shortCode,
                'PhoneNumber'            => $phone,
                'CallBackURL'            => route('courses.stk.callback.43054384'),
                //'CallBackURL'            => "https://takemyitclass.com",
                'AccountReference'       => 'Moringa School Course Enrollment',
                'TransactionDesc'        => 'Payment For Enrollment: ' . $course->name

            ];
            $data_string = json_encode($curl_post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_HEADER, 0);

            $curl_res = curl_exec($ch);
            $result = json_decode((string) $curl_res, true);
            info("End Point Hit STK");
            info($result);
            $mpesa_stk = MpesaStk::create([
                "merchant_request_id" => $result["MerchantRequestID"],
                "checkout_request_id" => $result["CheckoutRequestID"],
                "course_id" => $course->id,
                "user_id" => $user->id,
                "status" => '0',
            ]);

            return back()->with('success', 'Please Complete Transaction In the Phone Number Provided');
        } catch (\Throwable $th) {
            //Log Error
            info($th->getMessage());
            //Send error Notification to Admin
            $notificationService = new NotificationService();
            $subject = "Mpesa Payment Failure";
            $message = "Stk Push Error : " . $th->getMessage();
            $notificationService->sendAdminErrorLog($subject, $message);
            return back()->with('error', 'Failed! Try Again After 5 minutes or Contact the School for help. An Automated Email is sent to the Admin');
        }
    }
}
