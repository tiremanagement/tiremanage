<?php

namespace App\Exports;

use App\Models\Vehicle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VehiclesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Vehicle::select('id', 'plate_no', 'driver_id_number', 'branch', 'is_registered')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Plate No', 'Driver ID', 'Branch', 'Is Registered'];
    }
}