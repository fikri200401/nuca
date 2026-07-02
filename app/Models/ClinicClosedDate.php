<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicClosedDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
