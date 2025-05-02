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
        $student = auth()->user(); // assuming student is logged in
        return view('student.organizationcard', compact('student'));
    }

    public function viewCardTwo()
    {
        $student = auth()->user(); // assuming student is logged in
        return view('student.yearorganizationcard', compact('student'));
    }

}

