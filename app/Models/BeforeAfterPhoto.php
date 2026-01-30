<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeforeAfterPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'before_photo',
        'after_photo',
        'notes',
        'uploaded_by',
    ];

    /**
     * Relationships
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Helper methods
     */
    public function hasBeforePhoto()
    {
        return !empty($this->before_photo);
    }

    public function hasAfterPhoto()
    {
        return !empty($this->after_photo);
    }

    public function hasCompletePhotos()
    {
        return $this->hasBeforePhoto() && $this->hasAfterPhoto();
    }
}
