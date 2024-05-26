<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\MpesaStk;
use App\Models\MpesaPayment;
use App\Models\Course;
use App\Models\User;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class MpesaPaymentConfirmationTest extends TestCase
{
    use RefreshDatabase, MockeryPHPUnitIntegration;

    /** @test */
    public function it_confirms_payment_correctly()
    {
        // Create test data
        $course = Course::factory()->create();
        $user = User::factory()->create();
        $mpesaStk = MpesaStk::factory()->create([
            'merchant_request_id' => '123456',
            'checkout_request_id' => '654321',
            'course_id' => $course->id,
            'user_id' => $user->id,
            'status' => '0'
        ]);

        // Mock the NotificationService and MicrosoftDynamicsService
        $notificationServiceMock = Mockery::mock('App\Services\NotificationService');
        $this->app->instance('App\Services\NotificationService', $notificationServiceMock);
        $notificationServiceMock->shouldReceive('sendPaymentNotification')
                                ->with(Mockery::on(function ($data) use ($course, $user) {
                                    return $data['amount'] == 100 && $data['course_id'] == $course->id && $data['user_id'] == $user->id;
                                }));

        $microsoftDynamicsServiceMock = Mockery::mock('App\Services\MicrosoftDynamicsService');
        $this->app->instance('App\Services\MicrosoftDynamicsService', $microsoftDynamicsServiceMock);
        $microsoftDynamicsServiceMock->shouldReceive('postPaymentData')
                                     ->with(Mockery::on(function ($data) use ($course, $user) {
                                         return $data['amount'] == 100 && $data['course_id'] == $course->id && $data['user_id'] == $user->id;
                                     }));

        // Simulate the callback request
        $callbackPayload = [
            'Body' => [
                'stkCallback' => [
                    'MerchantRequestID' => '123456',
                    'CheckoutRequestID' => '654321',
                    'ResultCode' => '0',
                    'ResultDesc' => 'The service request is processed successfully.',
                    'CallbackMetadata' => [
                        'Item' => [
                            ['Name' => 'Amount', 'Value' => 100],
                            ['Name' => 'MpesaReceiptNumber', 'Value' => 'ABC123'],
                            ['Name' => 'Balance', 'Value' => 0],
                            ['Name' => 'TransactionDate', 'Value' => '20230101120000'],
                            ['Name' => 'PhoneNumber', 'Value' => '254712345678'],
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->postJson(route('courses.stk.callback.43054384'), $callbackPayload);

        // Verify that the payment details are saved in the database
        $this->assertDatabaseHas('mpesa_payments', [
            'amount' => 100,
            'mpesa_receipt_number' => 'ABC123',
            'phone_number' => '254712345678',
            'course_id' => $course->id, // Ensure course_id is checked
            'user_id' => $user->id, // Ensure user_id is checked
        ]);

        // Verify that the STK status is updated
        $this->assertDatabaseHas('mpesa_stks', [
            'merchant_request_id' => '123456',
            'status' => '1'
        ]);

        // Ensure the response status is 200 OK
        $response->assertStatus(200);
    }

}
