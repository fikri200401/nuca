<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManualApprovalDate extends Model
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
