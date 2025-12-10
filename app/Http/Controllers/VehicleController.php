<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;

class VehicleController extends Controller
{
    /**
     * Display all vehicles
     */
public function index(Request $request)
{
    $search = $request->input('search');
    $query = Vehicle::query();

    // ✅ Filter by plate_no or model if search provided
    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->where('plate_no', 'like', "%{$search}%")
              ->orWhere('model', 'like', "%{$search}%");
        });
    }

    // ✅ Show only unregistered vehicles
    $query->where('is_registered', false);

    $vehicles = $query->get();

    // ✅ Detect layout based on logged-in user role
    $user = auth()->user();
    $layout = 'section_manager'; // default

    if ($user && isset($user->role->name)) {
        $layout = ($user->role->name === 'Admin') ? 'admin' : 'section_manager';
    }

    // ✅ Pass layout + vehicles to the view
    return view('vehicles.index', compact('vehicles', 'layout'));
}


    /**
     * Show create vehicle form
     */
    public function create()
    {
        return view('vehicles.create');
    }

    /**
     * Store new vehicle
     */
    public function store(Request $request)
    {
        // Validate all fields and check uniqueness of plate_no
        // Disallow digits in textual fields (model, branch, vehicle_type, brand, user_section)
        // Plate number is allowed to contain digits
        $noDigits = 'regex:/^[^0-9]*$/';

        $request->validate([
            'plate_no'  => 'required|string|max:50|unique:vehicles,plate_no',
            'branch'    => ['required','string','max:100',$noDigits],
            'vehicle_type' => ['nullable','string','max:100',$noDigits],
            'brand'        => ['nullable','string','max:100',$noDigits],
            'user_section' => ['nullable','string','max:150',$noDigits],
            'driver_id_number' => 'nullable|string|max:100|exists:drivers,id_number',
        ], [
            'branch.regex' => 'Branch must not contain numbers.',
            'vehicle_type.regex' => 'Vehicle type must not contain numbers.',
            'brand.regex' => 'Brand must not contain numbers.',
            'user_section.regex' => 'User section must not contain numbers.',
            'driver_id_number.exists' => 'Driver ID number does not match any registered driver.',
        ]);

        // Normalize plate number (uppercase, trim spaces)
        $plateNo = strtoupper(trim($request->plate_no));

        //  Double-check for existing plate (case-insensitive)
        if (Vehicle::whereRaw('LOWER(TRIM(plate_no)) = ?', [strtolower($plateNo)])->exists()) {
            return back()
                ->withInput()
                ->with('error', '⚠️ Vehicle with this plate number already exists.');
        }

        Vehicle::create([
            'plate_no'     => $plateNo,
            'branch'       => $request->branch,
            'is_registered'=> $request->is_registered ?? false,
            'vehicle_type' => $request->vehicle_type,
            'brand'        => $request->brand,
            'user_section' => $request->user_section,
            'driver_id_number' => $request->driver_id_number,
        ]);

        // ✅ Redirect based on role
        $route = auth()->user()->role->name === 'Admin'
            ? 'admin.vehicles.index'
            : 'section_manager.vehicles.index';

        return redirect()->route($route)->with('success', '✅ Vehicle added successfully.');
    }

    /**
     * Edit vehicle
     */
    public function edit(Vehicle $vehicle)
    {
        return view('vehicles.edit', compact('vehicle'));
    }

    /**
     * Update vehicle
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $noDigits = 'regex:/^[^0-9]*$/';

        $request->validate([
            'plate_no' => 'required|string|max:50|unique:vehicles,plate_no,' . $vehicle->id,
            'branch'   => ['required','string','max:255',$noDigits],
            'vehicle_type' => ['nullable','string','max:100',$noDigits],
            'brand'        => ['nullable','string','max:100',$noDigits],
            'user_section' => ['nullable','string','max:150',$noDigits],
            'driver_id_number' => 'nullable|string|max:100|exists:drivers,id_number',
        ], [
            'branch.regex' => 'Branch must not contain numbers.',
            'vehicle_type.regex' => 'Vehicle type must not contain numbers.',
            'brand.regex' => 'Brand must not contain numbers.',
            'user_section.regex' => 'User section must not contain numbers.',
            'driver_id_number.exists' => 'Driver ID number does not match any registered driver.',
        ]);

        $vehicle->update([
            'plate_no'     => strtoupper(trim($request->plate_no)),
            'branch'       => $request->branch,
            'is_registered'=> $request->is_registered ?? $vehicle->is_registered,
            'vehicle_type' => $request->vehicle_type,
            'brand'        => $request->brand,
            'user_section' => $request->user_section,
            'driver_id_number' => $request->driver_id_number,
        ]);

        $route = auth()->user()->role->name === 'Admin'
            ? 'admin.vehicles.index'
            : 'section_manager.vehicles.index';

        return redirect()->route($route)->with('success', '✅ Vehicle updated successfully.');
    }

    /**
     * Delete vehicle
     */
    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        $route = auth()->user()->role->name === 'Admin'
            ? 'admin.vehicles.index'
            : 'section_manager.vehicles.index';

        return redirect()->route($route)->with('success', '🗑️ Vehicle deleted successfully.');
    }

    /**
     * Lookup vehicle by plate number (AJAX)
     */
    public function lookup(Request $request)
    {
        $plate = $request->query('plate_no');

        if (!$plate) {
            return response()->json(['found' => false], 400);
        }

        // Normalize search
        $vehicle = Vehicle::whereRaw('LOWER(TRIM(plate_no)) = ?', [strtolower(trim($plate))])->first();

        if (!$vehicle) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found'    => true,
            'id'       => $vehicle->getKey(),
            'plate_no' => $vehicle->plate_no,
            'branch'   => $vehicle->branch,
            'vehicle_type' => $vehicle->vehicle_type,
            'brand'        => $vehicle->brand,
            'user_section' => $vehicle->user_section,
            'driver_id_number' => $vehicle->driver_id_number,
        ]);
    }
}