<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TireRequest;
use App\Models\Approval;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Role;
use App\Models\Driver;

class SectionManagerController extends Controller
{
public function __construct()
{
    $this->middleware(function ($request, $next) {
        $user = auth()->user();

        if (!$user || !$user->role) {
            abort(403, 'Unauthorized.');
        }

        $role = strtolower($user->role->name);

        // Restrict *most* actions, but skip for driver management
        if (!in_array($role, ['section manager', 'admin']) &&
            in_array($request->route()->getActionMethod(), [
                'drivers', 'createDriver', 'storeDriver', 'destroyDriver'
            ]) === false
        ) {
            abort(403, 'Access restricted to Section Manager.');
        }

        return $next($request);
    });
}



    /** ---------------- DASHBOARD (Pending Requests) ---------------- */
    public function index()
    {
        $pendingRequests = TireRequest::where('status', Approval::STATUS_PENDING)
            ->where('current_level', Approval::LEVEL_SECTION_MANAGER)
            ->with(['user', 'vehicle', 'tire'])
            ->orderByDesc('created_at')
            ->get();

        return view('dashboard.section_manager.section_manager', compact('pendingRequests'));
    }

    public function pending()
    {
        return $this->index();
    }

    /** ---------------- APPROVED REQUESTS ---------------- */
public function approved()
{
    // Get approvals made by Section Manager
    $approvedRecords = Approval::where('level', Approval::LEVEL_SECTION_MANAGER)
        ->where('status', Approval::STATUS_APPROVED)
        ->with(['request.user', 'request.vehicle', 'request.tire'])
        ->orderByDesc('updated_at')
        ->get();

    return view('dashboard.section_manager.approved', compact('approvedRecords'));
}


    /** ---------------- REJECTED REQUESTS ---------------- */
public function rejected()
{
    $requests = TireRequest::where('status', Approval::STATUS_REJECTED)
        ->where('current_level', Approval::LEVEL_SECTION_MANAGER)
        ->with(['user', 'vehicle', 'tire'])
        ->orderByDesc('updated_at')
        ->get();

    return view('dashboard.section_manager.rejected', compact('requests'));
}


    /** ---------------- APPROVE REQUEST ---------------- */
    public function approve($id)
    {
        $requestItem = TireRequest::findOrFail($id);

        // Step 1: Update TireRequest → forward to Mechanic Officer
        $requestItem->update([
            'status' => Approval::STATUS_PENDING_MECHANIC, // waiting for Mechanic Officer
            'current_level' => Approval::LEVEL_MECHANIC_OFFICER,
        ]);

        // Step 2: Log in Approval table
        Approval::updateOrCreate(
            ['request_id' => $requestItem->id, 'level' => Approval::LEVEL_SECTION_MANAGER],
            [
                'approved_by' => auth()->id(),
                'status' => Approval::STATUS_APPROVED,
            ]
        );

        // Step 3: Redirect → approved list
        return redirect()->route('section_manager.requests.approved_list')
            ->with('success', '✅ Request approved successfully and forwarded to Mechanic Officer.');
    }

    /** ---------------- REJECT REQUEST ---------------- */
    public function reject($id)
    {
        $requestItem = TireRequest::findOrFail($id);

        // Step 1: Update request
        $requestItem->update([
            'status' => Approval::STATUS_REJECTED,
            'current_level' => Approval::LEVEL_SECTION_MANAGER,
        ]);

        // Step 2: Log in Approval table
        Approval::updateOrCreate(
            ['request_id' => $requestItem->id, 'level' => Approval::LEVEL_SECTION_MANAGER],
            [
                'approved_by' => auth()->id(),
                'status' => Approval::STATUS_REJECTED,
            ]
        );

        // Step 3: Redirect → rejected list
        return redirect()->route('section_manager.requests.rejected_list')
            ->with('error', '❌ Request rejected successfully.');
    }

    /** ---------------- EDIT REQUEST ---------------- */
    public function edit($id)
    {
        $requestItem = TireRequest::with(['user', 'vehicle', 'tire', 'approvals'])->findOrFail($id);
        return view('dashboard.section_manager.edit_request', compact('requestItem'));
    }

    /** ---------------- UPDATE REQUEST ---------------- */
public function update(Request $req, $id)
{
    $requestItem = TireRequest::findOrFail($id);

    // Step 1: Update basic fields
    $requestItem->update([
        'damage_description' => $req->damage_description,
        'current_level' => Approval::LEVEL_SECTION_MANAGER,
    ]);

    // Step 2: Handle status updates
    if ($req->filled('status')) {
        $status = strtolower($req->status);

        if ($status === 'approved') {
            // Forward to Mechanic Officer
            $requestItem->update([
                'status' => Approval::STATUS_PENDING_MECHANIC,
                'current_level' => Approval::LEVEL_MECHANIC_OFFICER,
            ]);

            // Log approval
            Approval::updateOrCreate(
                ['request_id' => $requestItem->id, 'level' => Approval::LEVEL_SECTION_MANAGER],
                [
                    'approved_by' => auth()->id(),
                    'status' => Approval::STATUS_APPROVED,
                    'remarks' => $req->remarks ?? null,
                ]
            );

            // Redirect to Mechanic Officer pending requests
            return redirect()->route('section_manager.requests.approved_list')
                ->with('success', '✅ Request approved and forwarded to Mechanic Officer.');
        }
        elseif ($status === 'rejected') {
            $requestItem->update([
                'status' => Approval::STATUS_REJECTED,
                'current_level' => Approval::LEVEL_SECTION_MANAGER,
            ]);

            Approval::updateOrCreate(
                ['request_id' => $requestItem->id, 'level' => Approval::LEVEL_SECTION_MANAGER],
                [
                    'approved_by' => auth()->id(),
                    'status' => Approval::STATUS_REJECTED,
                    'remarks' => $req->remarks ?? null,
                ]
            );

            return redirect()->route('section_manager.requests.rejected_list')
                ->with('error', '❌ Request rejected successfully.');
        }
        else {
            $requestItem->update(['status' => Approval::STATUS_PENDING]);
        }
    }

    // Default redirect (no status change)
    return redirect()->route('section_manager.requests.pending')
        ->with('success', '✏️ Request updated successfully.');
}


    /** ---------------- SEARCH ---------------- */
    public function search(Request $request)
    {
        $search = trim($request->input('search'));

        if (empty($search)) {
            return redirect()->route('section_manager.dashboard');
        }

        $pendingRequests = TireRequest::whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->where('status', Approval::STATUS_PENDING)
            ->with(['user', 'vehicle', 'tire'])
            ->get();

        return view('dashboard.section_manager.section_manager', compact('pendingRequests'));
    }

    /** ---------------- DRIVER MANAGEMENT ---------------- */
    public function drivers(Request $request)
    {
        $query = Driver::with('user');

        if ($search = $request->input('search')) {
            $query->where('full_name', 'like', "%{$search}%");
        }

        $drivers = $query->orderByDesc('id')->get();
        $user = Auth::user(); 

        return view('dashboard.section_manager.drivers.index', compact('drivers'));
    }

    public function createDriver()
    {
        return view('admin.drivers.create');
    }

public function storeDriver(Request $request)
{
    $rawMobile = $request->input('mobile');
    $normalizedMobile = $rawMobile !== null ? preg_replace('/[\s\-\(\)]/', '', $rawMobile) : null;
    $request->merge(['mobile' => $normalizedMobile]);

    $request->validate([
        'name' => 'required|string|unique:users,name',
        'email' => 'required|email|unique:users,email',
        'full_name' => 'required|string|max:255',
        'mobile' => ['nullable', 'string', 'max:50', 'regex:/^(0\d{9}|\+?94\d{9})$/'],
    'id_number' => 'required|string|max:12|unique:drivers,id_number',
    ], [
        'mobile.regex' => 'Mobile must be 10 digits starting with 0 (e.g. 0711234567) or include country code 94 with 9 subscriber digits (e.g. +94711234567).',
    ]);

    // Create User for the driver
    $driverRole = Role::where('name', 'driver')->firstOrFail();

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        // Set initial password to the driver's ID number (hashed)
        'password' => Hash::make($request->id_number),
        'role_id' => $driverRole->id,
        'must_change_password' => true,
    ]);

    // Create Driver record
    Driver::create([
        'user_id' => $user->id,
        'full_name' => $request->full_name,
        'mobile' => $request->mobile,
        'id_number' => $request->id_number,
    ]);

    // After creating a driver from section manager area, go back to the section manager dashboard
    return redirect()->route('section_manager.dashboard')
        ->with('success', '✅ Driver created successfully.');
}


    public function destroyDriver($id)
    {
        Driver::findOrFail($id)->delete();

        return redirect()->route('section_manager.drivers.index')
            ->with('success', '🗑️ Driver deleted successfully.');
    }
}