<?php

namespace Database\Seeders;

use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CoursesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $courses = [
            [
                'id' => 1,
                'name' => 'Introduction to Programming With PHP',
                'description' => 'Dive into the world of web development with our "Introduction to Programming with PHP" course. Designed for beginners, this course provides a comprehensive foundation in PHP, a powerful server-side scripting language widely used for creating dynamic and interactive websites.
                 You will learn the basics of PHP syntax, variables, control structures, functions, and form handling, as well as how to interact with databases using MySQL. By the end of the course, you will be able to build and deploy your own web applications, equipped with the skills to further explore advanced PHP programming and web development concepts.',
                'price' => '5000',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Introduction to Systems Integration Architecture',
                'description' => 'Explore the critical field of Systems Integration Architecture in this introductory course designed for aspiring IT professionals and system architects. This course delves into the principles and practices of integrating diverse computing systems and software applications, enabling them to function as a coordinated whole.
                 You will learn about various integration methods, middleware technologies, architectural patterns, and best practices for designing robust and scalable integrated systems. By the end of the course, you will have a strong foundation in systems integration and be equipped to tackle real-world challenges in creating cohesive IT environments.',
                'price' => '7000',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'Introduction to Machine Learning and AI',
                'description' => "Unlock the power of data and intelligent systems with our Introduction to Machine Learning and AI course. Designed for beginners and enthusiasts, this course provides a solid foundation in the principles and applications of machine learning and artificial intelligence. You will explore key concepts, algorithms, and tools used to build intelligent systems capable of learning and making decisions. Through hands-on exercises and real-world examples, you'll gain practical experience in applying machine learning techniques to various domains. By the end of the course, you'll be well-equipped to advance your studies in AI and contribute to this rapidly evolving field.",
                'price' => '6000',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        Course::insert($courses);
    }
}
