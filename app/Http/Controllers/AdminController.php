<?php

namespace App\Http\Controllers;

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
        // Get the logged-in admin
        $admin = Auth::guard('admin')->user(); // Use the admin guard here

        // Get the organization of the logged-in admin
        $organization = $admin->username;

        // Pass the organization to the view
        return view('admin.dashboard', compact('organization'));
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

       public function showMembers(Request $request)
       {
           $admin = Auth::guard('admin')->user();
           $organization = $admin->username;
   
           $section = $request->input('section');
   
           $students = Student::where('organization', $organization)
               ->when($section, function ($query, $section) {
                   $query->where('section', $section);
               })
               ->get();
   
           // Group sections by year
           $availableSections = Student::where('organization', $organization)
               ->pluck('section')
               ->unique()
               ->sort()
               ->values();
   
           $groupedSections = [];
   
           foreach ($availableSections as $sec) {
               $year = (int) substr($sec, 2, 1); // Cast to integer to avoid key issues
               $groupedSections[$year][] = $sec;
           }
           $allOrganizations = Admin::where('role', 'admin')
           ->where('username', '!=', $organization)
           ->pluck('username') // each username is an organization
           ->unique()
           ->sort()
           ->values();
   
           return view('admin.members', compact('students', 'organization', 'section', 'groupedSections', 'allOrganizations'));
       }




    // Super Admin dashboard
    public function superAdminDashboard()
    {
        // Fetch all admins (excluding the super admin)
        $admins = Admin::where('role', 'admin')->get();

        // Pass the admins data to the view
        return view('superadmin.dashboard', compact('admins'));
    }

    // User management for super admin
    public function userManagementSuperAdmin()
    {
        // Fetch all admins (excluding the super admin)
        $admins = Admin::where('role', 'admin')->get();

        // Pass the admins data to the view
        return view('superadmin.usermanagement', compact('admins'));
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

    // Combine academic year fields into one string
    $academicYear = $request->academic_year_from . '-' . $request->academic_year_to;

    // Create semester
    $semester = Semester::create([
        'admin_id' => auth()->id(), // or however you're assigning admin
        'semester' => $request->semester,
        'academic_year' => $academicYear,
    ]);

    // Attach all students with default "Unpaid" payment_status
    $students = Student::all();
    foreach ($students as $student) {
        $semester->students()->attach($student->id, ['payment_status' => 'Unpaid']);
    }

    return redirect()->back()->with('success', 'Semester created and students initialized with unpaid status.');
}



    // Set semester - this is where the semester is SELECTED AND REDIRECTED to the semester record page
    public function setSemester($id)
    {
        // Store the selected semester ID in the session
        session(['current_semester_id' => $id]);

        // Redirect to the semester record page
        return redirect()->route('admin.semesterrecord');
    }

   

    // Show semester record----blade SEMESTERRECORD.BLADE
  public function semesterRecord(Request $request)
{
    $admin = Auth::guard('admin')->user(); // Get the logged-in admin
    $organization = $admin->username; // Get the organization name

    // Get the selected section (if any) from the request
    $section = $request->input('section');

    // Fetch the selected semester ID from the session
    $semesterId = session('current_semester_id');

    // Fetch the specific semester based on the session ID
    $currentSemester = Semester::where('id', $semesterId)
        ->where('admin_id', $admin->id)
        ->first();

    // 🆕 Get students via the relationship on the current semester, with payment_status from pivot
    $studentsQuery = $currentSemester
        ? $currentSemester->students()->withPivot('payment_status')->where('organization', $organization)
        : Student::where('organization', $organization); // fallback

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
    
        $student->semesters()->updateExistingPivot($semester_id, [
            'payment_status' => $request->payment_status
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
        $semesterId = session('current_semester_id');
    
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