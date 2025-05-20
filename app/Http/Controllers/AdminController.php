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
use Illuminate\Support\Str; 
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

        if (!$currentSemester) {
            return view('admin.dashboard')->with([
                'organization' => $organization,
                'totalMembers' => 0,
                'totalPaid' => 0,
                'totalUnpaid' => 0,
                'recentTransactions' => collect(),
                'labels' => collect(),
                'paidData' => collect(),
                'totalData' => collect(),
            ]);
        }

        $semesterId = $currentSemester->id;
        $isYearLevelOrg = in_array($organization, ['FCO', 'SCO', 'JCO', 'SENCO']);
        $organizationInstance = Organization::where('code', $organization)->first();

        if (!$organizationInstance) {
            return view('admin.dashboard')->with([
                'organization' => $organization,
                'totalMembers' => 0,
                'totalPaid' => 0,
                'totalUnpaid' => 0,
                'recentTransactions' => collect(),
                'labels' => collect(),
                'paidData' => collect(),
                'totalData' => collect(),
            ]);
        }

        $totalMembers = Student::whereHas('semesters', function ($q) use ($semesterId) {
                $q->where('semesters.id', $semesterId);
            })
            ->whereHas('organizations', function ($q) use ($organizationInstance) {
                $q->where('organizations.id', $organizationInstance->id);
            })
            ->count();

        $totalPaid = Student::whereHas('semesters', function ($q) use ($semesterId) {
                $q->where('semesters.id', $semesterId)
                  ->where('semester_student.payment_status', 'Paid');
            })
            ->whereHas('organizations', function ($q) use ($organizationInstance) {
                $q->where('organizations.id', $organizationInstance->id);
            })
            ->count();

        $totalUnpaid = Student::whereHas('semesters', function ($q) use ($semesterId) {
                $q->where('semesters.id', $semesterId)
                  ->where('semester_student.payment_status', 'Unpaid');
            })
            ->whereHas('organizations', function ($q) use ($organizationInstance) {
                $q->where('organizations.id', $organizationInstance->id);
            })
            ->count();

        $recentTransactions = Student::whereHas('semesters', function ($q) use ($semesterId) {
        $q->where('semesters.id', $semesterId)
          ->where('semester_student.payment_status', 'Paid');
    })
    ->whereHas('organizations', function ($q) use ($organizationInstance) {
        $q->where('organizations.id', $organizationInstance->id);
    })
    ->select('id_number as student_id', 'first_name', 'last_name', 'section', 'updated_at')
    ->orderBy('updated_at', 'desc')
    ->limit(5)
    ->get();


        // 🛠️ FIXED: Use leftJoin and logic that includes semesters even if no paid students exist
        $paymentStats = DB::table('semesters')
            ->leftJoin('semester_student', 'semesters.id', '=', 'semester_student.semester_id')
            ->leftJoin('students', 'students.id', '=', 'semester_student.student_id')
            ->select(
                'semesters.created_at',
                DB::raw("CONCAT(semesters.semester, ' AY ', semesters.academic_year) AS sem_label"),
                DB::raw('COUNT(semester_student.student_id) as total_students'),
                DB::raw('COALESCE(SUM(CASE WHEN semester_student.payment_status = "Paid" THEN 1 ELSE 0 END), 0) as total_paid')
            )
            ->whereIn('semesters.admin_id', $organizationAdminIds)
            ->when($isYearLevelOrg, function ($query) use ($admin) {
                return $query->where('semester_student.admin_id', $admin->id);
            }, function ($query) use ($organizationInstance) {
                return $query->where('students.organization', $organizationInstance->code);
            })
            ->groupBy('sem_label', 'semesters.created_at')
            ->orderBy('semesters.created_at', 'asc')
            ->get();

        // 🗂️ Keep last 4 semesters
        $latestStats = $paymentStats->sortByDesc('created_at')->take(4)->sortBy('created_at')->values();

        $labels = $latestStats->pluck('sem_label');
        $paidData = $latestStats->pluck('total_paid');
        $totalData = $latestStats->pluck('total_students');
        //     dd([
        //     'labels' => $labels,
        //     'paidData' => $paidData,
        //     'totalData' => $totalData,
        //     'unpaidCalc' => $totalData->map(fn($total, $i) => $total - $paidData[$i]),
        // ]);


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
        // Get current admins grouped by organization (case-insensitive)
        $admins = Admin::where('status', 'current')
            ->where('role', 'admin')
            ->get()
            ->groupBy(fn($admin) => strtoupper($admin->username))
            ->map(fn($group) => $group->sortByDesc('created_at')->first());

        // Attach student counts
        $organizations = $admins->map(function ($admin) {
            $latestSemester = Semester::where('admin_id', $admin->id)
                ->orderBy('created_at', 'desc')
                ->first();

            $studentsCount = 0;

            if ($latestSemester) {
                $studentsCount = \DB::table('semester_student')
                ->join('students', 'semester_student.student_id', '=', 'students.id')
                ->join('organization_student', 'students.id', '=', 'organization_student.student_id')
                ->join('organizations', 'organization_student.organization_id', '=', 'organizations.id')
                ->where('semester_student.semester_id', $latestSemester->id)
                ->whereRaw('LOWER(organizations.code) = LOWER(?)', [$admin->username])
                ->count();

            }

            $admin->students_count = $studentsCount;
            return $admin;
        });

        return view('superadmin.dashboard', ['organizations' => $organizations]);
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
            'password' => 'required|string|min:5',
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
            'password' => 'required|string|min:5',
            'organization' => 'required|string'
        ]);

        // Mark current treasurer as past
        try {
            DB::transaction(function () use ($request) {
                $currentAdmin = Admin::whereRaw('UPPER(username) = ?', [strtoupper($request->organization)])
                    ->where('status', 'current')
                    ->first();

                if ($currentAdmin) {
                    $currentAdmin->status = 'past';
                    $currentAdmin->save();
                }

                $orgSlug = strtoupper(str_replace(' ', '_', $request->organization));
                $imagePath = 'images/OrganizationLogo/' . $orgSlug . '.png';

                $newAdmin = new Admin();
                $newAdmin->name = $request->name;
                $newAdmin->username = strtoupper($request->organization);
                $newAdmin->password = bcrypt($request->password);
                $newAdmin->plain_password = $request->password;
                $newAdmin->status = 'current';
                $newAdmin->role = 'admin';
                $newAdmin->image = $imagePath;
                $newAdmin->save();
            });
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

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
            ->whereHas('organizations', function ($query) use ($organization) {
                $query->where('code', $organization);
            });

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
        $availableSections = Student::whereHas('organizations', function ($query) use ($organization) {
            $query->where('code', $organization);
        })->pluck('section')->unique()->sort()->values();

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

        $section = $request->input('section');

        $filter = $request->input('filter'); // <-- using filter instead of section

        // Try to get from request first, then session

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
            return redirect()->route('admin.paymenthistorylist')->withErrors(['error' => 'No semester selected or found.']);
        }

        // Start base query
        $studentsQuery = $currentSemester->students()
            ->withPivot('payment_status', 'admin_id', 'admin_name')
            ->whereHas('organizations', function ($query) use ($organization) {
                $query->where('code', $organization);
            });

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
        $semesters = Semester::whereIn('admin_id', $organizationAdminIds)->get();
        // Group sections by year for dropdown filtering
        $availableSections = Student::whereHas('organizations', function ($query) use ($organization) {
            $query->where('code', $organization);
        })->pluck('section')->unique()->sort()->values();

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
        ini_set('memory_limit', '512M'); // Increase memory limit

        $admin = Auth::guard('admin')->user();
        $organization = $admin->username;
        $semesterId = $request->input('semester_id');
        $filter = $request->input('filter');
        $search = $request->input('search');
        $orderColumn = $request->input('order_column');
        $orderDir = $request->input('order_dir');

        $organizationAdminIds = Admin::where('username', $organization)->pluck('id');
         $organizationInstance = Organization::where('code', $organization)->first();

        $currentSemester = Semester::where('id', $semesterId)
            ->whereIn('admin_id', $organizationAdminIds)
            ->first();

        if (!$currentSemester) {
            return back()->withErrors(['error' => 'Semester not found.']);
        }

        // Base query using pivot relationship
        $studentsQuery = $currentSemester->students()
            ->withPivot('payment_status', 'admin_id', 'admin_name')
            ->whereHas('organizations', function ($query) use ($organization) {
                $query->where('code', $organization);
            });

        // Apply filter logic
        if ($filter) {
            if (Str::startsWith($filter, 'year_')) {
                $year = substr($filter, 5);
                $studentsQuery->whereRaw('SUBSTRING(students.section, 3, 1) = ?', [$year]);
            } elseif (Str::startsWith($filter, 'section_')) {
                $section = substr($filter, 8);
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
                    ->orWherePivot('payment_status', 'like', "%$search%");
            });
        }

        // Handle sorting (map DataTables index to column)
        $sortableColumns = ['id_number', 'first_name', 'last_name', 'section', 'payment_status'];
        if (is_numeric($orderColumn) && isset($sortableColumns[$orderColumn])) {
            $column = $sortableColumns[$orderColumn];

            if ($column === 'payment_status') {
                $studentsQuery->orderByPivot('payment_status', $orderDir === 'desc' ? 'desc' : 'asc');
            } else {
                $studentsQuery->orderBy("students.$column", $orderDir === 'desc' ? 'desc' : 'asc');
            }
        }


        $filteredStudentsQuery = Student::whereHas('semesters', function ($q) use ($semesterId) {
        $q->where('semesters.id', $semesterId);
    })
    ->whereHas('organizations', function ($q) use ($organizationInstance) {
        $q->where('organizations.id', $organizationInstance->id);
    });

    // Apply filter to count queries as well
    if ($filter) {
        if (Str::startsWith($filter, 'year_')) {
            $year = substr($filter, 5);
            $filteredStudentsQuery->whereRaw('SUBSTRING(section, 3, 1) = ?', [$year]);
        } elseif (Str::startsWith($filter, 'section_')) {
            $section = substr($filter, 8);
            $filteredStudentsQuery->where('section', $section);
        }
    }

    $totalPaid = (clone $filteredStudentsQuery)->whereHas('semesters', function ($q) use ($semesterId) {
        $q->where('semesters.id', $semesterId)
        ->where('semester_student.payment_status', 'Paid');
    })->count();

    $totalUnpaid = (clone $filteredStudentsQuery)->whereHas('semesters', function ($q) use ($semesterId) {
        $q->where('semesters.id', $semesterId)
        ->where('semester_student.payment_status', 'Unpaid');
    })->count();


        $students = $studentsQuery->get();

        $pdf = Pdf::loadView('admin.paymenthistorypdf', [
            'organization' => $organization,
            'currentSemester' => $currentSemester,
            'students' => $students,
            'section' => $filter,
            'totalPaid' => $totalPaid,
            'totalUnpaid' => $totalUnpaid,
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