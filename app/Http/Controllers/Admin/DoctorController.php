<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoctorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $doctors = Doctor::withCount('bookings')->orderBy('name')->paginate(15);
        return view('admin.doctors.index', compact('doctors'));
    }

    public function create()
    {
        return view('admin.doctors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except('photo');

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('doctors', 'public');
        }

        Doctor::create($data);

        return redirect()
            ->route('admin.doctors.index')
            ->with('success', 'Dokter berhasil ditambahkan.');
    }

    public function edit(Doctor $doctor)
    {
        return view('admin.doctors.edit', compact('doctor'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except('photo');

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('doctors', 'public');
        }

        $doctor->update($data);

        return redirect()
            ->route('admin.doctors.index')
            ->with('success', 'Dokter berhasil diupdate.');
    }

    public function destroy(Doctor $doctor)
    {
        if ($doctor->bookings()->exists()) {
            return back()->withErrors(['error' => 'Dokter tidak bisa dihapus karena ada booking terkait.']);
        }

        $doctor->delete();

        return redirect()
            ->route('admin.doctors.index')
            ->with('success', 'Dokter berhasil dihapus.');
    }

    public function toggleStatus(Doctor $doctor)
    {
        $doctor->update(['is_active' => !$doctor->is_active]);

        return back()->with('success', 'Status dokter berhasil diubah.');
    }

    /**
     * Manage doctor schedules
     */
    public function schedules(Doctor $doctor)
    {
        $schedules = $doctor->schedules()->orderByRaw("FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')")->get();
        
        return view('admin.doctors.schedules', compact('doctor', 'schedules'));
    }

    public function storeSchedule(Request $request, Doctor $doctor)
    {
        $request->validate([
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        // Check for overlapping schedules
        $exists = $doctor->schedules()
            ->where('day_of_week', $request->day_of_week)
            ->where(function($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                      ->orWhereBetween('end_time', [$request->start_time, $request->end_time]);
            })
            ->exists();

        if ($exists) {
            return back()->withErrors(['error' => 'Jadwal bentrok dengan jadwal yang sudah ada.']);
        }

        $doctor->schedules()->create($request->all());

        return back()->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function deleteSchedule(Doctor $doctor, DoctorSchedule $schedule)
    {
        if ($schedule->doctor_id !== $doctor->id) {
            abort(404);
        }

        $schedule->delete();

        return back()->with('success', 'Jadwal berhasil dihapus.');
    }

    public function toggleScheduleStatus(Doctor $doctor, DoctorSchedule $schedule)
    {
        if ($schedule->doctor_id !== $doctor->id) {
            abort(404);
        }

        $schedule->update(['is_active' => !$schedule->is_active]);

        return back()->with('success', 'Status jadwal berhasil diubah.');
    }
}
