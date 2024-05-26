<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseUser;
use App\Services\MpesaService;
use App\Services\SalesforceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function create()
    {
        return view('courses.create');
    }

    public function index()
    {
        if (session('info')) {
            Alert::info('Course Enrolled', session('info'));
        }
        $courses = Course::all();
        return view('courses.index', compact('courses'));
    }

    public function show($id)
    {
        if (session('success')) {
            Alert::success('Success', session('success'));
        }

        if (session('info')) {
            Alert::info('Course Enrolled', session('info'));
        }
        if (session('error')) {
            Alert::info('Error', session('error'));
        }
        $course = Course::findOrFail($id);

        return view('courses.show', compact('course'));
    }

    public function enroll(Request $request)
    {
        $userID = Auth::id();
        $courseID = $request->course_id;
        $course = Course::findOrFail($courseID);
        //Check If Already enrolled Avoid duplicate enrollment
        $userCourse = CourseUser::where('user_id', $userID)->where('course_id', $courseID)->first();
        if ($userCourse) {
            return redirect()->route('courses.show', $course->id)->with('info', 'You already enrolled to ' . $course->name);
        }
        //Save Enrollment Data
        try {
            // Start the transaction
            DB::beginTransaction();

            // Save to Student Portal
            $courseUser = CourseUser::create([
                'user_id' => $userID,
                'course_id' => $courseID,
            ]);
            $data = [
                'user_id' => $userID,
                'course_id' => $courseID,
                'amount' => $course->price,
            ];

            //Save student Data to SalesForce
            $salesforceService = new SalesforceService();
            $salesforceService->postCourseData($data);
            // Commit the transaction
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            //Log Errors In saving Data
            info("Error Saving Course Data: ".$th->getMessage());
        }


        return redirect()->route('courses.show', $courseUser->course_id)->with('success', 'You have Successfully enrolled to ' . $course->name);
    }

    public function payment(Request $request, $id)
    {
        $request->validate([
            'phone' => 'required|digits_between:10,12'
        ]);
        $course = Course::findOrFail($id);

        $phone = $request->phone;
        $user = Auth::user();

        $mpesaService = new MpesaService();
        //Initiate Mpesa Push through Service
        $mpesaService->stkPush($phone, $user, $course);

        return back();
    }
}
