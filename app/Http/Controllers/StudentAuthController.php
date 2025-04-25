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
}
