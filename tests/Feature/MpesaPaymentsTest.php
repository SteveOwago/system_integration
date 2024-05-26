<?php

namespace Tests\Feature;


use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class MpesaPaymentsTest extends TestCase
{
    use RefreshDatabase, MockeryPHPUnitIntegration;

    /** @test */
    public function it_initiates_payment_correctly()
    {
        // Create a test user and course
        $user = User::factory()->create();
        $course = Course::factory()->create(['price' => 100]);

        // Log in the test user
        Auth::login($user);

        // Mock the MpesaService
        $mpesaServiceMock = Mockery::mock('App\Services\MpesaService');
        $this->app->instance('App\Services\MpesaService', $mpesaServiceMock);
        $mpesaServiceMock->shouldReceive('stkPush')
            ->with('254712345678', $user, $course);

        // Simulate a payment request
        $response = $this->post(route('courses.payment', ['course_id' => $course->id]), [
            'phone' => '0712345678'
        ]);

        // Verify that the user is redirected back
        $response->assertRedirect();
    }
}
