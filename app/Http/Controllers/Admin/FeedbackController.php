<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = Feedback::with(['user', 'treatment', 'doctor', 'booking']);

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('treatment_id')) {
            $query->where('treatment_id', $request->treatment_id);
        }

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        $feedbacks = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'average_rating' => Feedback::avg('rating'),
            'total_feedbacks' => Feedback::count(),
            'five_star' => Feedback::where('rating', 5)->count(),
            'four_star' => Feedback::where('rating', 4)->count(),
            'three_star' => Feedback::where('rating', 3)->count(),
            'two_star' => Feedback::where('rating', 2)->count(),
            'one_star' => Feedback::where('rating', 1)->count(),
        ];

        return view('admin.feedbacks.index', compact('feedbacks', 'stats'));
    }

    public function show(Feedback $feedback)
    {
        $feedback->load(['user', 'treatment', 'doctor', 'booking']);
        
        return view('admin.feedbacks.show', compact('feedback'));
    }

    /**
     * Toggle feedback visibility
     */
    public function toggleVisibility(Feedback $feedback)
    {
        $feedback->update(['is_visible' => !$feedback->is_visible]);

        return back()->with('success', 'Visibility feedback berhasil diubah.');
    }

    /**
     * Delete feedback
     */
    public function destroy(Feedback $feedback)
    {
        $feedback->delete();

        return redirect()
            ->route('admin.feedbacks.index')
            ->with('success', 'Feedback berhasil dihapus.');
    }
}
