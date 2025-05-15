<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Organization;
use App\Models\Semester;

class StudentController extends Controller
{
    // Store a new student
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
           'id_number' => ['required', 'unique:students,id_number', 'regex:/^\d{7}$/'],
            'first_name' => ['required', 'regex:/^[A-Za-z\s\-]+$/'],
            'last_name' => ['required', 'regex:/^[A-Za-z\s\-]+$/'],
            'contact_number' => ['required', 'regex:/^09\d{9}$/'],
            'year_level' => 'required|integer|between:1,4',
            'section' => 'required|string',
            'organization' => 'required|string',
        ]);

    // Create the student
    $student = Student::create($request->all());

    // Now, get the organization and link it to the correct year-level organization
    $courseOrg = $request->organization;

    // Define the year-level organizations
    $yearOrgs = [
        1 => 'FCO', // 1st year -> FCO
        2 => 'SCO', // 2nd year -> SCO
        3 => 'JCO', // 3rd year -> JCO
        4 => 'SENCO', // 4th year -> SENCO
    ];

    // Find the corresponding year-level organization based on year level
    $yearLevelOrg = $yearOrgs[$request->year_level] ?? null;

    if ($yearLevelOrg) {
        // Attach the student to the corresponding year-level organization
        $yearOrganization = Organization::where('code', $yearLevelOrg)->first();
        if ($yearOrganization) {
            $student->organizations()->attach($yearOrganization->id);
        }
    }

    // Attach student to the course organization (if it doesn't exist yet)
    $courseOrganization = Organization::where('code', $courseOrg)->first();
    if ($courseOrganization) {
        $student->organizations()->attach($courseOrganization->id);
    }


    
    return back()->with('success', 'Student added successfully.');
}


        public function update(Request $request, Student $student)
        {
            $validator = Validator::make($request->all(), [
                'first_name' => ['required', 'regex:/^[A-Za-z\s\-]+$/'],
                'last_name' => ['required', 'regex:/^[A-Za-z\s\-]+$/'],
                'contact_number' => ['required', 'regex:/^09\d{9}$/'],
                'year_level' => 'required|integer|between:1,4',
                'section' => 'required|string',
                'id_number' => [
                    'required',
                    Rule::unique('students', 'id_number')->ignore($student->id),
                ],
            ]);

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('editing_student_id', $student->id);
            }

            $student->update($request->all());

            return back()->with('success', 'Student updated successfully.');
        }

    // Transfer student to another organization
    public function transfer(Request $request, Student $student)
    {
        $request->validate([
            'organization' => 'required|string'
        ]);

        // Find the new organization
        $newOrg = Organization::where('code', $request->organization)->first();

        if (!$newOrg) {
            return back()->with('error', 'Organization not found.');
        }

        // Detach only course organization (not year-level ones like FCO/SCO/etc.)
        $courseOrgs = ['APSS', 'AVED', 'BACOMMUNITY', 'BPED MOVERS', 'COFED', 'DIGITS',
            'EC', 'EA', 'HRC', 'JSWAP', 'KMF', 'LNU MSS', 'INTERSOC',
            'TC', 'TLEG', 'SQU', 'ECEO']; // <- adjust this list based on your actual course org codes
        $orgIdsToDetach = $student->organizations()
            ->whereIn('code', $courseOrgs)
            ->pluck('organizations.id');

        $student->organizations()->detach($orgIdsToDetach);

        // Attach the new organization
        $student->organizations()->attach($newOrg->id);
        // Update student's current organization field
        $student->current_organization_id = $newOrg->id;
        $student->save();
        

        return back()->with('success', 'Student transferred to ' . $request->organization . ' successfully.');
    }

    // public function transfer(Request $request, Student $student)
    // {
    //     $request->validate([
    //         'organization' => 'required|string'
    //     ]);

    //     $student->organization = $request->organization;
    //     $student->save();

    //     return back()->with('success', 'Student transferred to ' . $request->organization . ' successfully.');
    // }

    public function getByYearAndOrg(Request $request)
    {
        $sections = Student::where('year_level', $request->year_level)
            ->where('organization', $request->organization)
            ->select('section')
            ->distinct()
            ->orderBy('section')
            ->get();

        return response()->json($sections);
    }



}
