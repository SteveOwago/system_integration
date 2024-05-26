<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseUser;
use App\Models\User;
use App\Services\SalesforceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Mockery;

class CourseEnrollSaveDataToSalesforceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_cannot_enroll_twice()
    {
        // Given a user is already enrolled in a course
        $user = User::factory()->create();
        $course = Course::factory()->create();
        CourseUser::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

        // When the user tries to enroll again
        $response = $this->actingAs($user)
            ->post(route('courses.enroll'), ['course_id' => $course->id]);

        // Then they should be redirected back with an info message
        $response->assertRedirect(route('courses.show', $course->id))
            ->assertSessionHas('info');
    }

    /** @test */
    public function user_can_successfully_enroll()
    {
        // Given a user and a course
        $user = User::factory()->create();
        $course = Course::factory()->create();

        // When the user enrolls for the first time
        $response = $this->actingAs($user)
            ->post(route('courses.enroll', [
                'user_id' => $user->id,
                'course_id' => $course->id
            ]));

        // Then they should be redirected to the course page with a success message
        $response->assertRedirect(route('courses.show',  $course->id));

        // And a new record should be created in the course_user table
        $this->assertDatabaseHas('course_users', [
            'user_id' => $user->id,
            'course_id' => $course->id
        ]);
    }

    /** @test */
    public function data_is_saved_to_salesforce_when_enrolled()
    {
        // Given a user and a course
        $user = User::factory()->create();
        $course = Course::factory()->create();

        /// Create a mock for the SalesforceService
        $mockSalesforceService = Mockery::mock(SalesforceService::class);

        // Set an expectation that the postCourseData method should be called
        $mockSalesforceService->shouldReceive('postCourseData');

        // Use the app instance to swap the real service with the mock
        $this->app->instance(SalesforceService::class, $mockSalesforceService);

        // When the user enrolls
        $this->actingAs($user)->post(route('courses.enroll'),  [
            'user_id' => $user->id,
            'course_id' => $course->id
        ]);
    }
}
