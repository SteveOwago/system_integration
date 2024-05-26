<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Course;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_course()
    {
        $course = Course::factory()->create([
            'name' => 'Test Course',
            'description' => 'This is a test course description.',
            'price' => 299.99,
        ]);

        $this->assertDatabaseHas('courses', [
            'name' => 'Test Course',
            'description' => 'This is a test course description.',
            'price' => 299.99,
        ]);
    }
}
