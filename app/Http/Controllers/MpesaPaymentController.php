<?php

namespace App\Http\Controllers;

use App\Models\MpesaPayment;
use App\Models\MpesaStk;
use Illuminate\Http\Request;

class MpesaPaymentController extends Controller
{
    public function stkPush(){
        $response = Mpesa::stkpush($phoneno, $amount, $account_number);
        $result = json_decode((string) $response, true);
        Log::info('ready');
        $mpesa_stk = MpesaStk::create([
            "merchant_request_id" => $result["MerchantRequestID"],
            "checkout_request_id" => $result["CheckoutRequestID"],
            "order_id" => $orderID,
            "user_id" => $user->id,
            "status" => 0,
        ]);
    }
    public function confirm(Request $request)
    {
        info('were here');
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
                $order_details = DB::table('mpesa_stks')
                    ->where('merchant_request_id', $merchant_request_id)
                    ->first();
                $orderID = $order_details->order_id;
                $userID = $order_details->user_id;
                $data = [
                    'amount' => $amount,
                    'mpesa_receipt_number' => $mpesa_receipt_number,
                    'transaction_date' => $transaction_date,
                    'phonenumber' => $phonenumber,
                    'order_id' => $orderID,
                    'user_id' => $userID,
                ];
                // Check if payment already exists for this request
                $existingPayment = MpesaPayment::where('order_id', $orderID)->exists();
                if (!$existingPayment && MpesaPayment::create($data)) {
                    $mpesa_stk = MpesaSTK::find($order_details->id);
                    $mpesa_stk->update(['status' => 1]);

                    $order = Order::where('id', $orderID)->first();
                    $users = [];
                    $notifications = new NotificationService();
                    $customers = User::where('id', $userID)->get();
                    foreach ($customers as $user) {
                        $users[] = $user;
                    }
                    $notification_type = "ORDER_CREATED";
                    $notifications->createOrderNotification($order, $users, $notification_type);


                    $order_items = OrderDetail::where('order_id', $orderID)
                        ->where('design_id', '!=', 0)
                        ->get();
                    if ($order_items) {
                        Log::debug('found');
                        foreach ($order_items as $item) {
                            $designs = Design::where('id', $item->design_id)->first();
                            $all_designs = Design::where('canvas_id', $designs->canvas_id)->get();
                            foreach ($all_designs as $value) {
                                $design_name = $value->design;
                                $this->processCommision($design_name, $value->id);
                                info('dfdf');
                            }
                        }
                    } else {
                        Log::debug('none');
                    }
                }
            }
        } else {
            $this->failed = true;
        }
        return $this;
    }
}
