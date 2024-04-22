<?php

namespace App\Http\Controllers;

use App\Models\CbcsCourseCoordinator;
use App\Models\CbcsSubjectOfferedd;
use App\Models\CbcsDepartment;
use App\Models\UserDetailsd;
use App\Models\CbcsCourseInstructors;

use Illuminate\Http\Request;


class MyClass {
    public $courseName;
    public $courseCode;
    public $offeredToName;

    public function __construct($courseName,$offeredToName, $courseCode) {
        $this->courseName = $courseName;
        $this->offeredToName = $offeredToName;
        $this->courseCode = $courseCode;
    }
}

class InstructorDetails{
    public $instructorId;
    public $instructorName;

    public function __construct($instructorId, $instructorName) {
        $this->instructorId=$instructorId;
        $this->instructorName=$instructorName;
    }

}


class AssignCourseCoordinatorController extends Controller
{
    public function fetchCourses(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'session' => 'required',
            'session_year' => 'required'
        ]);
        // console.log($request);


        // Retrieve session and session year from the request body
        $session = $request->input('session');
        $sessionYear = $request->input('session_year');

        // Fetch data from table x based on session and session year
        $Courses = CbcsCourseCoordinator::where('session', $session)
                  ->where('session_year', $sessionYear)
                  ->get();

        // echo $Courses;

        $res = array();

        
        foreach($Courses as $Course){
            $courseCode = $Course->sub_code;
            $courseName = $Course->subject_name;
            $offeredToName=$Course->offered_to_name;
            // $departmentId=$courseDetail->dept_id;
            // $departmentName=CbcsDepartment::where('id',$departmentId)->first()->name;
            // $courseId=$Course->course_id;
            // $coordinatorId=$Course->coordinator;
            // $courseDetail=CbcsSubjectOfferedd::where('id',$courseId)->first();
            // $courseName=$courseDetail->sub_name;
            // $courseCode=$courseDetail->sub_code;
            // $departmentId=$courseDetail->dept_id;
            // $departmentName=CbcsDepartment::where('id',$departmentId)->first()->name;

            // if ($coordinatorId !== null) {
            //     $coordinatorDetail = UserDetailsd::where('id', $coordinatorId)->first();
            //     $coordinatorName = $coordinatorDetail->first_name . ' '. $coordinatorDetail->middle_name .' '.$coordinatorDetail->last_name;
            // } else {
            //     $coordinatorName = null;
            // }
        
           
            $completeCourseDeatil = new MyClass($courseName,$offeredToName,$courseCode);
            $res[] = $completeCourseDeatil;
            // return response()->json($courseDetail->sub_name);

        }

        return response()->json($res);
    }

    public function fetchInstructors(Request $request)
    {
        // Validate the incoming request data

        $request->validate([
            'cbcs_course_coordinator_id' => 'required',
        ]);
        
        // Retrieve cbcs_course_coordinator_id from the request body
        $cbcsCourseCoordinatorId = $request->input('cbcs_course_coordinator_id');
        // dd($cbcsCourseCoordinatorId);

        // Fetch instructors based on cbcs_course_coordinator_id
        $instructors = CbcsCourseInstructors::where('cbcs_course_coordinator_id', $cbcsCourseCoordinatorId)->get();

        $res = [];

        // Iterate over instructors
        foreach ($instructors as $instructor) {
            // Retrieve instructor details
            $instructorId = $instructor->professor_id;
            $instructorDetail = UserDetailsd::where('id', $instructorId)->first();
            
            // Check if instructor details are available
            if ($instructorDetail) {
                // Construct instructor name
                $instructorName = $instructorDetail->first_name . ' ' . $instructorDetail->middle_name . ' ' . $instructorDetail->last_name;
                
                // Create an instance of InstructorDetails and add it to the response array
                $completeDetail = new InstructorDetails($instructorId, $instructorName);
                $res[] = $completeDetail;
            }
        }

        // Return the response as JSON
        return response()->json($res);
    }

    public function updateCoordinator(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'id' => 'required',
            'instructorId' => 'required'
        ]);
    
        // Retrieve the id and instructorId from the request
        $id = $request->input('id');
        $instructorId = $request->input('instructorId');
    
        // Find the record in the cbsc_course_coordianator table by id
        $courseCoordinator = CbcsCourseCoordinator::findOrFail($id);
    
        // Update the coordinator field with the new instructorId
        $courseCoordinator->coordinator = $instructorId;
    
        // Save the changes to the database
        // dd($courseCoordinator);
        $courseCoordinator->save();
    
        // Optionally, you can return a response indicating success
        return response()->json(['message' => 'Coordinator updated successfully']);
    }

}
