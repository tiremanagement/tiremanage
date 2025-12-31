<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'contact', 'email', 'address', 'town'];

    // relation: supplier has many tires
    public function tires()
    {
        return $this->hasMany(Tire::class);
    }
}
