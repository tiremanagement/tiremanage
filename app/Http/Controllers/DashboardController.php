<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Vehicle;
use App\Models\Tire;
use App\Models\TireRequest;
use App\Models\Driver;
use Illuminate\Support\Facades\Hash;
use App\Models\Approval;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * ---------------- ADMIN DASHBOARD ----------------
     */
    public function admin()
    {
        $vehicles   = Vehicle::orderBy('model', 'asc')->get();
        $tires      = Tire::with('supplier')->get();
        $suppliers  = Supplier::all();
        $drivers    = Driver::with('user')->get();
        $recentRequests = TireRequest::with(['user', 'vehicle', 'tire'])
            ->latest()
            ->take(15)
            ->get();

        // Count all pending requests (any level)
        $pending_requests = TireRequest::where('status', Approval::STATUS_PENDING)->count()
                             + TireRequest::where('status', Approval::STATUS_PENDING_MECHANIC)->count()
                             + TireRequest::where('status', Approval::STATUS_PENDING_TRANSPORT)->count();

        return view('admin.dashboard', [
            'vehicles'          => $vehicles,
            'vehicles_count'    => $vehicles->count(),
            'tires'             => $tires,
            'tires_count'       => $tires->count(),
            'suppliers'         => $suppliers,
            'suppliers_count'   => $suppliers->count(),
            'drivers'           => $drivers,
            'drivers_count'     => $drivers->count(),
            'recentRequests'    => $recentRequests,
            'pending_requests'  => $pending_requests,
        ]);
    }

    /**
     * ---------------- ADMIN: PENDING REQUESTS OVERVIEW ----------------
     */
public function pendingRequests()
{
    // Section Manager pending requests
    $sectionManagerRequests = TireRequest::where('current_level', Approval::LEVEL_SECTION_MANAGER)
        ->where('status', Approval::STATUS_PENDING)
        ->with(['user', 'vehicle', 'tire'])
        ->orderByDesc('created_at')
        ->get();

    // Mechanic Officer pending requests
    $mechanicOfficerRequests = TireRequest::where('current_level', Approval::LEVEL_MECHANIC_OFFICER)
        ->where('status', Approval::STATUS_PENDING_MECHANIC)
        ->with(['user', 'vehicle', 'tire'])
        ->orderByDesc('created_at')
        ->get();

    // Transport Officer pending requests
    $transportOfficerRequests = TireRequest::where('current_level', Approval::LEVEL_TRANSPORT_OFFICER)
        ->where('status', Approval::STATUS_PENDING_TRANSPORT)
        ->with(['user', 'vehicle', 'tire'])
        ->orderByDesc('created_at')
        ->get();

    return view('admin.pending_requests', compact(
        'sectionManagerRequests',
        'mechanicOfficerRequests',
        'transportOfficerRequests'
    ));
}


    /*
     * ---------------- DRIVER DASHBOARD ----------------
     */
    public function driver()
    {
        $user = Auth::user();
        $requests = TireRequest::where('user_id', $user->id)->with(['vehicle', 'tire'])->get();

        return view('dashboard.driver', compact('requests'));
    }

    /*
     * ---------------- SECTION MANAGER DASHBOARD ----------------
     */
    public function sectionManager()
    {
        $pendingRequests = TireRequest::where('current_level', Approval::LEVEL_SECTION_MANAGER)
            ->with(['user', 'vehicle', 'tire'])
            ->get();

        return view('dashboard.section_manager.section_manager', compact('pendingRequests'));
    }

    /*
     * ---------------- MECHANIC OFFICER DASHBOARD ----------------
     */
    public function mechanicOfficer()
    {
        $pendingRequests = TireRequest::where('current_level', Approval::LEVEL_MECHANIC_OFFICER)
            ->with(['user', 'vehicle', 'tire'])
            ->get();

        return view('dashboard.mechanic_officer.pending', compact('pendingRequests'));
    }

    /*
     * ---------------- TRANSPORT OFFICER DASHBOARD ----------------
     */
    public function transportOfficer()
    {
        $pendingRequests = TireRequest::where('current_level', Approval::LEVEL_TRANSPORT_OFFICER)
            ->with(['user', 'vehicle', 'tire'])
            ->get();

        return view('dashboard.transport_officer.pending', compact('pendingRequests'));
    }


    public function changePasswordForm()
{
    return view('driver.change_password');
}

public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:8|confirmed',
    ]);

    $user = Auth::user();

    if (!Hash::check($request->current_password, $user->password)) {
        return back()->with('error', 'Current password is incorrect.');
    }

    $user->update([
        'password' => Hash::make($request->new_password),
        'must_change_password' => false, // Optional: useful if you use this flag
    ]);

    return back()->with('success', 'Password updated successfully!');
}
}
