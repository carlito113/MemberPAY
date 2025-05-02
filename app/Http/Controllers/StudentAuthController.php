<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentAuthController extends Controller
{
    public function dashboard()
    {
        $student = Auth::guard('student')->user(); // Get logged-in student
        return view('student.dashboard', compact('student'));
    }
    public function viewCardOne()
    {
        $student = auth()->user();
    
        // Get only valid semester IDs where the admin matches between pivot and semester
        $validSemesterIds = \DB::table('semester_student')
            ->join('semesters', 'semester_student.semester_id', '=', 'semesters.id')
            ->where('semester_student.student_id', $student->id)
            ->whereColumn('semester_student.admin_id', 'semesters.admin_id')
            ->pluck('semester_student.semester_id')
            ->toArray(); // make sure it's a pure array for whereIn
    
        // Now load the actual semesters the student belongs to AND admin matches
        $semesters = $student->semesters()
            ->whereIn('semester_id', $validSemesterIds)
            ->wherePivot('student_id', $student->id) // Explicit filter
            ->with('admin')
            ->get();

    
        // Dynamically get the org name from the first valid semester
        $organizationName = $semesters->first()?->admin?->username ?? 'Unknown Organization';
    
        // Get all admins for the 'Updated By' map
        $adminMap = \App\Models\Admin::pluck('name', 'id');
        
    
        return view('student.organizationcard', compact('student', 'semesters', 'organizationName', 'adminMap'));
    }
    
    
    

    public function viewCardTwo()
    {
        $student = auth()->user(); // assuming student is logged in
        return view('student.yearorganizationcard', compact('student'));
    }

    public function studentProfile()
    {
        $student = Auth::guard('student')->user(); // Get logged-in student
        return view('student.profile', compact('student'));
    }

    public function showPayments()
{
    $student = Auth::user(); // or fetch by param
    
    $semesters = $student->semesters()
        ->withPivot('payment_status', 'admin_id')
        ->with('students') // just to ensure relationships are available
        ->get();

    return view('student.payment', compact('student', 'semesters'));
}


}

