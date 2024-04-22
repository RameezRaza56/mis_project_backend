<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseRequest;
use App\Http\Requests\CourseDataRequest;
use App\Http\Requests\AssignCoordinatorRequest;
use App\Models\Course;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    public function getCoursesBySession(CourseRequest $request)
    {
        $session = $request->input('session');
        $sessionYear = $request->input('session_year');

        $courses = Course::where('session', $session)
                         ->where('session_year', $sessionYear)
                         ->get();
        
        Log::info('Courses retrieved: ' . $courses);

        return response()->json($courses);
    }

    public function getProfessorsOfCourse(CourseDataRequest $courseDataRequest)
    {
        $courseDataRequest->validate([
            'session' => 'required|string',
            'session_year' => 'required|string',
            'sub_code' => 'required|string',
        ]);
    
        try {
            // Retrieve all professors for the given session, session_year, and sub_code
            $professors = Course::where('session', $courseDataRequest->input('session'))
                                ->where('session_year', $courseDataRequest->input('session_year'))
                                ->where('sub_code', $courseDataRequest->input('sub_code'))
                                ->pluck('offered_to_name')
                                ->toArray();
    
            // Remove duplicates and convert the result to array
            $uniqueProfessors = array_unique($professors);
    
            return response()->json(['professors' => $uniqueProfessors]);
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json(['error' => 'Failed to fetch professors'], 500);
        }        
    }

    public function markCoordinator(AssignCoordinatorRequest $request)
    {
        // Validate the request data
        $request->validate([
            'session' => 'required|string',
            'session_year' => 'required|string',
            'sub_code' => 'required|string',
            'co_emp_id' => 'required|string',
        ]);

        try {
            // Find the course matching the provided criteria
            $course = Course::where('session', $request->input('session'))
                            ->where('session_year', $request->input('session_year'))
                            ->where('sub_code', $request->input('sub_code'))
                            ->where('co_emp_id', $request->input('co_emp_id'))
                            ->first();

            if ($course) {
                // Mark the is_coordinator field as true
                $course->is_coordinator = true;
                // Save the changes to the database
                $course->save();

                return response()->json(['message' => 'Coordinator marked successfully']);
            } else {
                return response()->json(['error' => 'Course not found'], 404);
            }
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json(['error' => 'Failed to mark coordinator'], 500);
        }
    }


    
}
