<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

use App\Models\Admin;
use App\Models\Semester;
use App\Models\YearSection;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str; // Make sure this is at the top
use App\Models\Organization;


use function Laravel\Prompts\alert;

class AdminController extends Controller
{
    // Admin dashboard
 
    public function dashboard()
{
    $admin = Auth::guard('admin')->user();
    $organization = $admin->username;

    $organizationAdminIds = Admin::where('username', $organization)->pluck('id');

    $currentSemester = Semester::whereIn('admin_id', $organizationAdminIds)
        ->orderBy('created_at', 'desc')
        ->first();

    $labels = collect();
    $paidData = collect();
    $totalData = collect();

    if (!$currentSemester) {
        return view('admin.dashboard', compact('organization', 'labels', 'paidData', 'totalData'))
            ->with([
                'totalMembers' => 0,
                'totalPaid' => 0,
                'totalUnpaid' => 0,
                'recentTransactions' => [],
            ]);
    }

    $totalMembers = $currentSemester->students()
        ->where('students.organization', $organization)
        ->count();

    $totalPaid = $currentSemester->students()
        ->where('students.organization', $organization)
        ->wherePivot('payment_status', 'Paid')
        ->count();

    $totalUnpaid = $currentSemester->students()
        ->where('students.organization', $organization)
        ->wherePivot('payment_status', 'Unpaid')
        ->count();

    $recentTransactions = $currentSemester->students()
        ->where('students.organization', $organization)
        ->select('students.id_number as student_id', 'students.first_name', 'students.last_name', 'students.section')
        ->orderBy('semester_student.updated_at', 'desc')
        ->limit(5)
        ->get();

    $paymentStats = DB::table('semesters')
        ->join('semester_student', 'semesters.id', '=', 'semester_student.semester_id')
        ->join('students', 'students.id', '=', 'semester_student.student_id')
        ->select(
            'semesters.created_at',
            DB::raw("CONCAT(semesters.semester, ' AY ', semesters.academic_year) AS sem_label"),
            DB::raw('COUNT(*) as total_students'),
            DB::raw('COUNT(CASE WHEN semester_student.payment_status = "Paid" THEN 1 END) as total_paid')
        )
        ->whereIn('semesters.admin_id', $organizationAdminIds)
        ->where('students.organization', $organization)
        ->groupBy('sem_label', 'semesters.created_at')
        ->orderBy('semesters.created_at', 'asc')
        ->get();

    $latestStats = $paymentStats->sortByDesc('created_at')->take(4)->sortBy('created_at')->values();

    $labels = $latestStats->pluck('sem_label');
    $paidData = $latestStats->pluck('total_paid');
    $totalData = $latestStats->pluck('total_students');

    return view('admin.dashboard', compact(
        'organization',
        'totalMembers',
        'totalPaid',
        'totalUnpaid',
        'recentTransactions',
        'labels',
        'paidData',
        'totalData'
    ));
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
    
        // Get the organization instance
        $organizationInstance = Organization::where('code', $organization)->first();
    
        if (!$organizationInstance) {
            // Handle error if the organization is not found
            abort(404, 'Organization not found');
        }
    
        $filter = $request->input('filter');
        $studentsQuery = $organizationInstance->students();
    
        if ($filter) {
            if (str_starts_with($filter, 'year_')) {
                $year = substr($filter, 5);
                $studentsQuery->whereRaw('SUBSTRING(students.section, 3, 1) = ?', [$year]);
            } elseif (str_starts_with($filter, 'section_')) {
                $section = substr($filter, 8);
                $studentsQuery->where('students.section', $section);
            }
        }
    
        $students = $studentsQuery->get();
    
        // Group sections
        $availableSections = $organizationInstance->students()->pluck('section')->unique()->sort()->values();
    
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
        $organizations = Admin::all()->map(function ($admin) {
            $latestSemester = Semester::where('admin_id', $admin->id)
                ->orderBy('created_at', 'desc')
                ->first();
    
            $studentsCount = 0;
    
            if ($latestSemester) {
                $studentsCount = \DB::table('semester_student')
                    ->join('students', 'semester_student.student_id', '=', 'students.id')
                    ->where('semester_student.semester_id', $latestSemester->id)
                    ->whereRaw('LOWER(students.organization) = LOWER(?)', [$admin->username])
                    ->count();
            }
    
            $admin->students_count = $studentsCount;
            return $admin;
        });
    
        
        return view('superadmin.dashboard', compact('organizations'));
    }
    


    // User management for super admin
public function userManagementSuperAdmin()
{
    // Fetch the latest admin (current treasurer) per organization by updated_at
    $admins = Admin::where('role', 'admin')
        ->orderBy('updated_at', 'desc')
        ->get()
        ->unique('username');

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
    $admin->plain_password = $request->input('password'); // Store plain password if used
    $admin->save();

    // Optional: Update future semesters if needed (based on created_at > now)
    // Otherwise, skip this logic if semester ownership stays tied to who is logged in

    return redirect()->route('usermanagement.dashboard')->with('success', 'Admin updated successfully.');
}


   public function assignNewTreasurer(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'password' => 'required|string|min:8',
        'organization' => 'required|string'
    ]);

    // Mark current treasurer as past
    $currentAdmin = Admin::where('username', $request->organization)
        ->where('status', 'current')
        ->first();

    if ($currentAdmin) {
        $currentAdmin->status = 'past';
        $currentAdmin->save();
    }

    // Set image path based on organization name
    $orgSlug = strtoupper(str_replace(' ', '_', $request->organization)); // e.g., DIGITS
    $imagePath = 'images/OrganizationLogo/' . $orgSlug . '.png';

    // Create new treasurer
    $newAdmin = new Admin();
    $newAdmin->name = $request->name;
    $newAdmin->username = $request->organization;
    $newAdmin->password = bcrypt($request->password);
    $newAdmin->plain_password = $request->password;
    $newAdmin->status = 'current';
    $newAdmin->role = 'admin';
    $newAdmin->image = $imagePath;
    $newAdmin->save();

    return redirect()->back()->with('success', 'New treasurer assigned.');
}






    // Admin payment
   public function adminPayment()
{
    $admin = Auth::guard('admin')->user(); // logged-in treasurer

    // Get all admins (including past treasurers) for the same org (based on username)
    $organizationUsername = $admin->username;

    $orgAdminIds = Admin::where('username', $organizationUsername)->pluck('id');

    // Get all semesters from any treasurer of the same org
    $semesters = Semester::whereIn('admin_id', $orgAdminIds)
                         ->orderBy('created_at', 'desc')
                         ->get();

    return view('admin.addpayment', [
        'semesters' => $semesters,
        'organization' => strtoupper($organizationUsername),
    ]);
}




public function semStore(Request $request)
{
    $request->validate([
        'semester' => 'required|string',
        'academic_year_from' => 'required|numeric|min:2000|max:2100',
        'academic_year_to' => 'required|numeric|min:2000|max:2100',
    ]);

    $academicYear = $request->academic_year_from . '-' . $request->academic_year_to;

    $loggedInAdmin = Auth::guard('admin')->user();

    // 🔍 Get the current treasurer based on organization
    $currentTreasurer = Admin::where('username', $loggedInAdmin->username)
        ->where('status', 'current')
        ->first();

    if (!$currentTreasurer) {
        return redirect()->back()->withErrors(['treasurer_not_found' => 'No current treasurer found for this organization.']);
    }

    // Check for duplicate semester for the current treasurer
    $exists = Semester::where('admin_id', $currentTreasurer->id)
        ->where('semester', $request->semester)
        ->where('academic_year', $academicYear)
        ->exists();

    if ($exists) {
        return redirect()->back()
            ->withErrors(['duplicate' => 'This semester and academic year already exist for your organization.'])
            ->withInput();
    }

    // 🆕 Create semester with the current treasurer’s ID
    $semester = Semester::create([
        'admin_id' => $currentTreasurer->id,
        'semester' => $request->semester,
        'academic_year' => $academicYear,
    ]);

    // 🧠 Match organization by username
    $organization = Organization::where('name', $loggedInAdmin->username)->first();

    if (!$organization) {
        return redirect()->back()->withErrors(['organization_not_found' => 'Organization not found for this admin.']);
    }

    $students = $organization->students;

    if ($students->isEmpty()) {
        return redirect()->back()->withErrors(['no_students' => 'No students found for this organization.']);
    }

    // 🧾 Attach students with current treasurer as the responsible admin
    foreach ($students as $student) {
        $semester->students()->attach($student->id, [
            'payment_status' => 'Unpaid',
            'admin_id' => $currentTreasurer->id,
            'admin_name' => $currentTreasurer->name,
        ]);
    }

    return redirect()->route('admin.addpayment')->with('success', 'Semester created and students initialized.');
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
        $admin = Auth::guard('admin')->user();
$organization = $admin->username;

        $section = $request->input('section');
       
        $filter = $request->input('filter'); // <-- using filter instead of section
    

        // Try to get from request first, then session
   
$organizationAdminIds = Admin::where('username', $organization)->pluck('id');

$semesterId = $request->input('semester_id') ?? session('current_semester_id');


    // If found in request, update the session (keep it fresh)
    if ($request->has('semester_id')) {
    session(['current_semester_id' => $semesterId]);
}

$currentSemester = Semester::where('id', $semesterId)
    ->whereIn('admin_id', $organizationAdminIds)
    ->first();

if (!$currentSemester) {
    return redirect()->route('admin.addpayment')->withErrors(['error' => 'No semester selected or found.']);
}

$studentsQuery = $currentSemester->students()
    ->withPivot('payment_status', 'admin_id', 'admin_name')
    ->where('students.organization', $organization);


        // Apply section filter if selected
        if ($filter) {
            if (Str::startsWith($filter, 'section_')) {
                $section = substr($filter, 8);
                $studentsQuery->where('students.section', $section);
            } elseif (Str::startsWith($filter, 'year_')) {
                $year = substr($filter, 5);
                $studentsQuery->whereRaw('SUBSTRING(students.section, 3, 1) = ?', [$year]);
            }
        }

        $students = $studentsQuery->get();

        // Get all semesters for the admin
$semesters = Semester::whereIn('admin_id', $organizationAdminIds)->get();
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

       $admin = Auth::guard('admin')->user(); // logged-in treasurer

    // Get all admins (including past treasurers) for the same org (based on username)
    $organizationUsername = $admin->username;

    $orgAdminIds = Admin::where('username', $organizationUsername)->pluck('id');

    // Get all semesters from any treasurer of the same org
    $semesters = Semester::whereIn('admin_id', $orgAdminIds)
                         ->orderBy('created_at', 'desc')
                         ->get();

    return view('admin.paymenthistory', [
        'semesters' => $semesters,
        'organization' => strtoupper($organizationUsername),
    ]);
    }


   
    public function paymentHistoryList(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $organization = $admin->username;
        $semesterId = $request->input('semester_id');
        $filter = $request->input('filter'); // <-- using filter instead of section
    
        $organizationAdminIds = Admin::where('username', $organization)->pluck('id');

$semesterId = $request->input('semester_id') ?? session('current_semester_id');

        // Fetch current semester
        if ($request->has('semester_id')) {
    session(['current_semester_id' => $semesterId]);
}

$currentSemester = Semester::where('id', $semesterId)
    ->whereIn('admin_id', $organizationAdminIds)
    ->first();

if (!$currentSemester) {
    return redirect()->route('admin.addpayment')->withErrors(['error' => 'No semester selected or found.']);
}
    
        // Start base query
        $studentsQuery = Student::where('students.organization', $organization)
            ->join('semester_student', function ($join) use ($semesterId) {
                $join->on('students.id', '=', 'semester_student.student_id')
                     ->where('semester_student.semester_id', '=', $semesterId);
            })
            ->select('students.*', 'semester_student.payment_status');
    
        $section = null;
        $year = null;
    
        if ($filter) {
            if (Str::startsWith($filter, 'section_')) {
                $section = substr($filter, 8);
                $studentsQuery->where('students.section', $section);
            } elseif (Str::startsWith($filter, 'year_')) {
                $year = substr($filter, 5);
                $studentsQuery->whereRaw('SUBSTRING(students.section, 3, 1) = ?', [$year]);
            }
        }
    
        $students = $studentsQuery->get();
    
        // Group sections
        $availableSections = Student::where('organization', $organization)
            ->pluck('section')->unique()->sort()->values();
    
        $groupedSections = [];
        foreach ($availableSections as $sec) {
            $yr = (int) substr($sec, 2, 1);
            $groupedSections[$yr][] = $sec;
        }
    
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
            'section',
            'filter' // optional if used in the view
        ));
    }
    





    public function downloadPaymentHistoryPDF(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $organization = $admin->username;
        $semesterId = $request->input('semester_id');
        $filter = $request->input('filter');
        $search = $request->input('search');
        $orderColumn = $request->input('order_column');
        $orderDir = $request->input('order_dir');
    
        $currentSemester = Semester::where('id', $semesterId)
            ->where('admin_id', $admin->id)
            ->first();
    
        if (!$currentSemester) {
            return back()->withErrors(['error' => 'Semester not found.']);
        }
    
        // Base student query
        $studentsQuery = Student::where('students.organization', $organization)
            ->join('semester_student', function ($join) use ($semesterId) {
                $join->on('students.id', '=', 'semester_student.student_id')
                    ->where('semester_student.semester_id', '=', $semesterId);
            })
            ->select('students.*', 'semester_student.payment_status');
    
        // Apply filter logic
        if ($filter) {
            if (str_starts_with($filter, 'year_')) {
                $year = str_replace('year_', '', $filter);
                $studentsQuery->where('students.year_level', $year);
            } elseif (str_starts_with($filter, 'section_')) {
                $section = str_replace('section_', '', $filter);
                $studentsQuery->where('students.section', $section);
            }
        }
    
        // Apply search
        if ($search) {
            $studentsQuery->where(function ($query) use ($search) {
                $query->where('students.first_name', 'like', "%$search%")
                    ->orWhere('students.last_name', 'like', "%$search%")
                    ->orWhere('students.id_number', 'like', "%$search%")
                    ->orWhere('students.section', 'like', "%$search%")
                    ->orWhere('semester_student.payment_status', 'like', "%$search%");
            });
        }
    
        // Map DataTables index to column names
        $sortableColumns = ['id_number', 'first_name', 'last_name', 'section', 'payment_status'];
        if (is_numeric($orderColumn) && isset($sortableColumns[$orderColumn])) {
            $studentsQuery->orderBy($sortableColumns[$orderColumn], $orderDir === 'desc' ? 'desc' : 'asc');
        }
    
        $students = $studentsQuery->get();
    
        $pdf = Pdf::loadView('admin.paymenthistorypdf', [
            'organization' => $organization,
            'currentSemester' => $currentSemester,
            'students' => $students,
            'section' => $filter,
        ]);
    
        $filename = 'payment_history_' . $currentSemester->semester;
        if ($filter) {
            $filename .= '_filtered';
        }
        $filename .= '.pdf';
    
        return $pdf->download($filename);
    }
    


    public function removeSemester(Request $request)
{
    $semester = Semester::findOrFail($request->semester_id);

    $semester->delete(); // Permanently deletes

    return redirect()->back()->with('success', 'Semester permanently deleted.');
}

   public function treasurerslist(Request $request)
{
    $admin = Auth::guard('admin')->user();
    $organization = $admin->username;

    $treasurers = Admin::where('username', $organization)
        ->whereHas('semesters')
        ->with(['semesters' => function ($query) {
            $query->orderBy('created_at');
        }])
        ->orderBy('created_at')
        ->get();

    return view('admin.treasurerslist', compact('treasurers', 'organization'));
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
