<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Vehicle
 *
 * @property int $id
 * @property string $plate_no
 * @property string|null $model
 * @property string|null $branch
 * @property bool $is_registered
 * @property string|null $vehicle_type
 * @property string|null $brand
 * @property string|null $user_section
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'plate_no',
        'branch',
        'is_registered',
        'vehicle_type',
        'brand',
        'user_section',
        'driver_id_number',
    ];
}
