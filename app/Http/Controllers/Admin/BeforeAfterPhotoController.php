<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BeforeAfterPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BeforeAfterPhotoController extends Controller
{
    /**
     * Display a listing of before-after photos
     */
    public function index()
    {
        $photos = BeforeAfterPhoto::with(['booking.user', 'booking.treatment'])
            ->latest()
            ->paginate(15);

        return view('admin.before-after-photos.index', compact('photos'));
    }

    /**
     * Upload before-after photos for a booking
     */
    public function upload(Request $request, Booking $booking)
    {
        $request->validate([
            'before_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'after_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string',
        ]);

        if (!$request->hasFile('before_photo') && !$request->hasFile('after_photo')) {
            return back()->withErrors(['error' => 'Minimal upload 1 foto.']);
        }

        $data = [
            'booking_id' => $booking->id,
            'uploaded_by' => Auth::id(),
            'notes' => $request->notes,
        ];

        if ($request->hasFile('before_photo')) {
            $data['before_photo'] = $request->file('before_photo')->store('before-after', 'public');
        }

        if ($request->hasFile('after_photo')) {
            $data['after_photo'] = $request->file('after_photo')->store('before-after', 'public');
        }

        // Check if already exists
        if ($booking->beforeAfterPhotos) {
            $beforeAfterPhoto = $booking->beforeAfterPhotos;
            
            // Delete old photos if replacing
            if (isset($data['before_photo']) && $beforeAfterPhoto->before_photo) {
                Storage::disk('public')->delete($beforeAfterPhoto->before_photo);
            }
            if (isset($data['after_photo']) && $beforeAfterPhoto->after_photo) {
                Storage::disk('public')->delete($beforeAfterPhoto->after_photo);
            }

            $beforeAfterPhoto->update($data);
        } else {
            BeforeAfterPhoto::create($data);
        }

        return back()->with('success', 'Foto before-after berhasil diupload.');
    }

    /**
     * Delete photos
     */
    public function destroy(Booking $booking)
    {
        if (!$booking->beforeAfterPhotos) {
            return back()->withErrors(['error' => 'Tidak ada foto untuk dihapus.']);
        }

        $photos = $booking->beforeAfterPhotos;

        // Delete files
        if ($photos->before_photo) {
            Storage::disk('public')->delete($photos->before_photo);
        }
        if ($photos->after_photo) {
            Storage::disk('public')->delete($photos->after_photo);
        }

        $photos->delete();

        return back()->with('success', 'Foto before-after berhasil dihapus.');
    }
}
