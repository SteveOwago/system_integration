<?php

namespace App\Http\Controllers;

use App\Models\MpesaPayment;
use App\Models\MpesaStk;
use Illuminate\Http\Request;

class MpesaPaymentController extends Controller
{

    public function confirm(Request $request)
    {
        info('Stk Payment Endpoint Reached');
        $payload = json_decode($request->getContent());
        if (property_exists($payload, 'Body') && $payload->Body->stkCallback->ResultCode == '0') {
            $merchant_request_id = $payload->Body->stkCallback->MerchantRequestID;
            $checkout_request_id = $payload->Body->stkCallback->CheckoutRequestID;
            $result_desc = $payload->Body->stkCallback->ResultDesc;
            $result_code = $payload->Body->stkCallback->ResultCode;
            $amount = $payload->Body->stkCallback->CallbackMetadata->Item[0]->Value;
            $mpesa_receipt_number = $payload->Body->stkCallback->CallbackMetadata->Item[1]->Value;
            $transaction_date = $payload->Body->stkCallback->CallbackMetadata->Item[3]->Value;
            $phonenumber = $payload->Body->stkCallback->CallbackMetadata->Item[4]->Value;
            if ($amount > 0) {
                $mpesa_stk =MpesaStk::where('merchant_request_id', $merchant_request_id)->where('checkout_request_id',$checkout_request_id)->first();
                $courseID = $mpesa_stk->order_id;
                $userID = $mpesa_stk->user_id;
                $data = [
                    'amount' => $amount,
                    'mpesa_receipt_number' => $mpesa_receipt_number,
                    'transaction_date' => $transaction_date,
                    'phone_number' => $phonenumber,
                    'course_id' =>$courseID ,
                    'user_id' => $userID,
                ];
                // Avoid Duplicate Payment || Check if payment already exists for this request
                $existingPayment = MpesaPayment::where('course_id', $courseID)->where('user_id',$userID)->exists();
                if (!$existingPayment && MpesaPayment::create($data)) {
                    $mpesa_stk = MpesaStk::find($mpesa_stk->id);
                    $mpesa_stk->update(['status' => '1']);
                    //Send Payment Received SMS or Email to Student

                    //Send Payment Details to Dynamics
                }
            }
        } else {
            info("Failed Transaction!")
        }

    }
}
