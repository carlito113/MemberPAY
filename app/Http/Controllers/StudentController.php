<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    // Store a new student
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_number' => ['required', 'unique:students,id_number', 'regex:/^\d{7}$/'],
            'first_name' => ['required', 'regex:/^[A-Za-z\s\-]+$/'],
            'last_name' => ['required', 'regex:/^[A-Za-z\s\-]+$/'],
            'contact_number' => ['required', 'regex:/^09\d{9}$/'],
            'year_level' => 'required|integer|between:1,4',
            'section' => 'required|string',
            'organization' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('showAddModal', true);
        }

        Student::create($request->all());

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

        $student->organization = $request->organization;
        $student->save();

        return back()->with('success', 'Student transferred to ' . $request->organization . ' successfully.');
    }

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
