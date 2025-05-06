<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\alert;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login'); // Show the login form
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Try Admin login first
        $admin = Admin::where('username', $validated['username'])->first();
        if ($admin && Hash::check($validated['password'], $admin->password)) {
            Auth::guard('admin')->loginUsingId($admin->id);

            if ($admin->role == 'super_admin') {
                return redirect()->route('superadmin.dashboard');
            } else {
                return redirect()->route('admin.dashboard');
            }
        }

        // Try Student login if Admin login fails
        $student = Student::where('id_number', $validated['username'])->first();
        if ($student){
            if ($student->status !== 'active') {
                return back()->withErrors(['login' => 'Oops! Your account is not active. Please contact the ' . $student->organization . ' Treasurer for assistance.']);

            }
        }
        if ($student && strtolower(trim($student->last_name)) === strtolower(trim($validated['password']))) {
            Auth::guard('student')->login($student);

            return redirect()->route('student.dashboard')->with('success', 'Login successful! Welcome, ' . $student->first_name . '!');
        }

        // If neither Admin nor Student matched
        return back()->withErrors(['login' => 'Invalid Username or Password']);
    }


    public function logout()
    {
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }

        if (Auth::guard('student')->check()) {
            Auth::guard('student')->logout();
        }
        // Redirect to the login page after logout
        return redirect()->route('login')->with('success', 'Logged out successfully');


    }


}
