<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Admin;
use App\Models\Semester;
use App\Models\YearSection;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


use function Laravel\Prompts\alert;

class AdminController extends Controller
{
    // Admin dashboard
    public function dashboard()
{
    $admin = Auth::guard('admin')->user();
    $organization = $admin->username;

    // Fetch the latest semester
    $currentSemester = Semester::where('admin_id', $admin->id)
        ->orderBy('created_at', 'desc')
        ->first();

    if (!$currentSemester) {
        return view('admin.dashboard', [
            'organization' => $organization,
            'totalMembers' => 0,
            'totalPaid' => 0,
            'totalUnpaid' => 0,
            'recentTransactions' => [],
        ]);
    }

    // Fetch total members for the current semester
    $totalMembers = Student::where('organization', $organization)
        ->join('semester_student', function ($join) use ($currentSemester) {
            $join->on('students.id', '=', 'semester_student.student_id')
                 ->where('semester_student.semester_id', '=', $currentSemester->id);
        })
        ->count();

    // Fetch total paid members for the current semester
    $totalPaid = Student::where('organization', $organization)
        ->join('semester_student', function ($join) use ($currentSemester) {
            $join->on('students.id', '=', 'semester_student.student_id')
                 ->where('semester_student.semester_id', '=', $currentSemester->id)
                 ->where('semester_student.payment_status', '=', 'Paid');
        })
        ->count();

    // Fetch total unpaid members for the current semester
    $totalUnpaid = $totalMembers - $totalPaid;

    // Fetch recent payment transactions for the current semester
    $recentTransactions = Student::where('organization', $organization)
        ->join('semester_student', function ($join) use ($currentSemester) {
            $join->on('students.id', '=', 'semester_student.student_id')
                 ->where('semester_student.semester_id', '=', $currentSemester->id);
        })
        ->select('students.id_number as student_id', 'students.first_name', 'students.last_name', 'students.section')
        ->orderBy('semester_student.updated_at', 'desc')
        ->limit(5)
        ->get();

    return view('admin.dashboard', [
        'organization' => $organization,
        'totalMembers' => $totalMembers,
        'totalPaid' => $totalPaid,
        'totalUnpaid' => $totalUnpaid,
        'recentTransactions' => $recentTransactions,
    ]);
}


    //-------NEW ADDED-------
       // Show add payment form
       public function showAddPaymentForm()
       {
           // Get the logged-in admin
           $admin = Auth::guard('admin')->user(); // Use the admin guard here

           // Get the organization of the logged-in admin
           $organization = $admin->username;

           // Pass the organization to the view
           return view('admin.addpayment', compact('organization'));
       }

    //    public function showMembers(Request $request)
    //    {
    //        $admin = Auth::guard('admin')->user();
    //        $organization = $admin->username;

    //        $section = $request->input('section');

    //        $students = Student::where('organization', $organization)
    //            ->when($section, function ($query, $section) {
    //                $query->where('section', $section);
    //            })
    //            ->get();

    //        // Group sections by year
    //        $availableSections = Student::where('organization', $organization)
    //            ->pluck('section')
    //            ->unique()
    //            ->sort()
    //            ->values();

    //        $groupedSections = [];

    //        foreach ($availableSections as $sec) {
    //            $year = (int) substr($sec, 2, 1); // Cast to integer to avoid key issues
    //            $groupedSections[$year][] = $sec;
    //        }
    //        $allOrganizations = Admin::where('role', 'admin')
    //        ->where('username', '!=', $organization)
    //        ->pluck('username') // each username is an organization
    //        ->unique()
    //        ->sort()
    //        ->values();

    //        return view('admin.members', compact('students', 'organization', 'section', 'groupedSections', 'allOrganizations'));
    //    }
        public function showMembers(Request $request)
        {
            $admin = Auth::guard('admin')->user();
            $organization = $admin->username;

            $filter = $request->input('filter');
            $studentsQuery = Student::where('organization', $organization);

            if ($filter) {
                if (str_starts_with($filter, 'year_')) {
                    $year = substr($filter, 5);
                    $studentsQuery->whereRaw('SUBSTRING(section, 3, 1) = ?', [$year]);
                } elseif (str_starts_with($filter, 'section_')) {
                    $section = substr($filter, 8);
                    $studentsQuery->where('section', $section);
                }
            }

            $students = $studentsQuery->get();

            // Group sections
            $availableSections = Student::where('organization', $organization)
                ->pluck('section')
                ->unique()
                ->sort()
                ->values();

            $groupedSections = [];
            foreach ($availableSections as $sec) {
                $year = (int) substr($sec, 2, 1);
                $groupedSections[$year][] = $sec;
            }

            $allOrganizations = Admin::where('role', 'admin')
                ->where('username', '!=', $organization)
                ->pluck('username')
                ->unique()
                ->sort()
                ->values();

            return view('admin.members', compact('students', 'organization', 'filter', 'groupedSections', 'allOrganizations'));
        }

        public function toggleStatus($id)
        {
            $student = Student::findOrFail($id);
            $student->status = $student->status === 'active' ? 'inactive' : 'active';
            $student->save();

            return back()->with('success', 'Student status updated successfully.');
        }




    // Super Admin dashboard
    public function superAdminDashboard()
    {
        // Fetch all admins with their total members (students) via semesters
        $organizations = Admin::withCount(['semesters as students_count' => function ($query) {
            $query->join('semester_student', 'semesters.id', '=', 'semester_student.semester_id')
                  ->join('students', 'semester_student.student_id', '=', 'students.id')
                  ->where('semester_student.semester_id', function ($subQuery) {
                      $subQuery->select('id')
                               ->from('semesters')
                               ->orderBy('created_at', 'desc')
                               ->limit(1); // Get the latest semester
                  });
        }])->get();

        return view('superadmin.dashboard', compact('organizations'));
    }

    // User management for super admin
    public function userManagementSuperAdmin()
    {
        // Fetch all admins (excluding the super admin)
        $admins = Admin::where('role', 'admin')->get();

        // Pass the admins data to the view
        return view('superadmin.usermanagement', compact('admins'));
    }

    public function updateAdminSuperadmin(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ]);

        // Find the admin by ID
        $admin = Admin::findOrFail($id);

        // Update the admin's details
        $admin->name = $request->input('name');
        $admin->password = bcrypt($request->input('password')); // Hash the password
        $admin->save();

        // Update the admin ID for new semesters only
        $semesters = Semester::where('admin_id', $id)->get();

        foreach ($semesters as $semester) {
            // Update only future semesters, not past ones
            if ($semester->created_at > now()) {
                $semester->admin_id = $admin->id; // Update admin to new treasurer for future semesters
                $semester->save();
            }
        }

        // Redirect back with a success message
        return redirect()->route('usermanagement.dashboard')->with('success', 'Admin updated successfully.');
    }


    // Admin payment
    public function adminPayment()
    {
        $admin = Auth::guard('admin')->user(); // Get the logged-in admin
        $organization = $admin->username; // Get the organization of the logged-in admin

        // Fetch only semesters that belong to the logged-in admin
        $semesters = Semester::where('admin_id', $admin->id)->get();

        return view('admin.addpayment', compact('organization', 'semesters'));
    }



    // Store semester - this is the MODAL currently at ADDPAYMENT.BLADE
    public function semStore(Request $request)
    {
        $request->validate([
            'semester' => 'required|string',
            'academic_year_from' => 'required|numeric|min:2000|max:2100',
            'academic_year_to' => 'required|numeric|min:2000|max:2100',
        ]);

        $academicYear = $request->academic_year_from . '-' . $request->academic_year_to;

        // Check for duplicate within the same admin only
        $exists = Semester::where('admin_id', auth()->id())
            ->where('semester', $request->semester)
            ->where('academic_year', $academicYear)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withErrors(['duplicate' => 'This semester and academic year already exist for your organization.'])
                ->withInput();
        }

        // Create the semester
        $semester = Semester::create([
            'admin_id' => auth()->id(),
            'semester' => $request->semester,
            'academic_year' => $academicYear,
        ]);

        // Attach all students with unpaid status and the admin_id recorded in pivot
      // ✅ Filter students based on organization (case-insensitive match)
    $adminOrg = trim(strtolower(auth()->user()->username));
    $students = Student::whereRaw('TRIM(LOWER(organization)) = ?', [$adminOrg])->get();

    // Attach only matching students
    foreach ($students as $student) {
        $semester->students()->attach($student->id, [
            'payment_status' => 'Unpaid',
            'admin_id' => auth()->id(),
            'admin_name' => auth()->user()->name, // Optional snapshot
        ]);
}



        return redirect()->back()->with('success', 'Semester created and students initialized with unpaid status.');
    }



    // Set semester - this is where the semester is SELECTED AND REDIRECTED to the semester record page
    public function setSemester($id)
    {
        session(['current_semester_id' => $id]); // Optional fallback
    return redirect()->route('admin.semesterrecord', ['semester_id' => $id]);
    }




    // Show semester record----blade SEMESTERRECORD.BLADE
    public function semesterRecord(Request $request)
    {
        $admin = Auth::guard('admin')->user(); // Get the logged-in admin
        $organization = $admin->username; // Get the organization name

        $section = $request->input('section');


        // Try to get from request first, then session
    $semesterId = $request->input('semester_id') ?? session('current_semester_id');

    // If found in request, update the session (keep it fresh)
    if ($request->has('semester_id')) {
        session(['current_semester_id' => $semesterId]);
    }

    $currentSemester = Semester::where('id', $semesterId)
        ->where('admin_id', $admin->id)
        ->first();

    if (!$currentSemester) {
        return redirect()->route('admin.addpayment')->withErrors(['error' => 'No semester selected or found.']);
    }


        // Get students via the relationship on the current semester, with payment_status from pivot
        $studentsQuery = $currentSemester->students()->withPivot('payment_status')->where('organization', $organization);

        // Apply section filter if selected
        if ($section) {
            $studentsQuery->where('section', $section);
        }

        $students = $studentsQuery->get();

        // Get all semesters for the admin
        $semesters = Semester::where('admin_id', $admin->id)->get();

        // Group sections by year for dropdown filtering
        $availableSections = Student::where('organization', $organization)
            ->pluck('section')
            ->unique()
            ->sort()
            ->values();

        $groupedSections = [];
        foreach ($availableSections as $sec) {
            $year = (int) substr($sec, 2, 1);
            $groupedSections[$year][] = $sec;
        }

        // Get yearSections for dropdown
        $yearSections = YearSection::where('admin_id', $admin->id)
            ->orderBy('year')
            ->orderBy('section')
            ->get()
            ->groupBy('year');

        return view('admin.semesterrecord', compact(
            'organization',
            'semesters',
            'currentSemester',
            'yearSections',
            'students',
            'groupedSections',
            'section'
        ));
    }



    public function updatePaymentStatus(Request $request)
    {
        $student = Student::findOrFail($request->student_id);
        $semester_id = $request->semester_id;

        // Save payment status, admin_id, and admin_name snapshot
        $student->semesters()->updateExistingPivot($semester_id, [
            'payment_status' => $request->payment_status,
            'admin_id' => auth()->id(),
            'admin_name' => auth()->user()->name, // Snapshot of treasurer at time of update
        ]);

        return back()->with('success', 'Payment status updated.');
    }




    // Show the payment history (GET)
    public function paymentHistory()
    {

        $admin = Auth::guard('admin')->user(); // Get the logged-in admin
        $organization = $admin->username; // Get the organization name
         // Fetch only semesters that belong to the logged-in admin
         $semesters = Semester::where('admin_id', $admin->id)->get();

        return view('admin.paymenthistory', compact('semesters', 'organization'));
    }


    public function paymentHistoryList(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $organization = $admin->username;
        $section = $request->input('section');
        $semesterId = $request->input('semester_id'); // Get semester_id from the request

        // Fetch current semester
        $currentSemester = Semester::where('id', $semesterId)
            ->where('admin_id', $admin->id)
            ->first();

        // If no current semester, return early or show empty list
        if (!$currentSemester) {
            return view('admin.paymenthistorylist', [
                'organization' => $organization,
                'semesters' => Semester::where('admin_id', $admin->id)->get(),
                'currentSemester' => null,
                'yearSections' => [],
                'students' => collect(),
                'groupedSections' => [],
                'section' => $section
            ]);
        }

        // Query students joined with semester_student pivot
        $studentsQuery = Student::where('students.organization', $organization)
            ->join('semester_student', function ($join) use ($semesterId) {
                $join->on('students.id', '=', 'semester_student.student_id')
                     ->where('semester_student.semester_id', '=', $semesterId);
            })
            ->select('students.*', 'semester_student.payment_status');

        if ($section) {
            $studentsQuery->where('students.section', $section);
        }

        $students = $studentsQuery->get();

        // Group sections by year
        $availableSections = Student::where('organization', $organization)
            ->pluck('section')->unique()->sort()->values();

        $groupedSections = [];
        foreach ($availableSections as $sec) {
            $year = (int) substr($sec, 2, 1);
            $groupedSections[$year][] = $sec;
        }

        // YearSections
        $yearSections = YearSection::where('admin_id', $admin->id)
            ->orderBy('year')->orderBy('section')->get()->groupBy('year');

        $semesters = Semester::where('admin_id', $admin->id)->get();

        return view('admin.paymenthistorylist', compact(
            'organization',
            'semesters',
            'currentSemester',
            'yearSections',
            'students',
            'groupedSections',
            'section'
        ));
    }





    public function downloadPaymentHistoryPDF(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $organization = $admin->username;
        $semesterId = $request->input('semester_id'); // Get semester_id from the request
        $section = $request->input('section'); // Get section filter from the request

        // Fetch current semester
        $currentSemester = Semester::where('id', $semesterId)
            ->where('admin_id', $admin->id)
            ->first();

        if (!$currentSemester) {
            return back()->withErrors(['error' => 'Semester not found.']);
        }

        // Query students joined with semester_student pivot
        $studentsQuery = Student::where('students.organization', $organization)
            ->join('semester_student', function ($join) use ($semesterId) {
                $join->on('students.id', '=', 'semester_student.student_id')
                     ->where('semester_student.semester_id', '=', $semesterId);
            })
            ->select('students.*', 'semester_student.payment_status');

        // Apply section filter if provided
        if ($section) {
            $studentsQuery->where('students.section', $section);
        }

        $students = $studentsQuery->get();

        // Pass data to the PDF view
        $pdf = Pdf::loadView('admin.paymenthistorypdf', [
            'organization' => $organization,
            'currentSemester' => $currentSemester,
            'students' => $students,
            'section' => $section, // Pass the section for context
        ]);

        // Generate the filename dynamically
        $filename = 'payment_history_' . $currentSemester->semester;
        if ($section) {
            $filename .= '_section_' . $section;
        }
        $filename .= '.pdf';

        // Download the PDF
        return $pdf->download($filename);
    }


    public function removeSemester(Request $request)
{
    $semester = Semester::findOrFail($request->semester_id);

    $semester->delete(); // Permanently deletes

    return redirect()->back()->with('success', 'Semester permanently deleted.');
}






    // Update admin
    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        // Only update name (always)
        $admin->name = $request->name;

        // Check if password has changed
        if ($request->filled('password') && $request->password !== $admin->plain_password) {
            $admin->plain_password = $request->password; // Store plain
            $admin->password = Hash::make($request->password); // Store hashed
        }

        $admin->save();

        return redirect()->back()->with('success', 'Treasurer updated successfully!');
    }
}
