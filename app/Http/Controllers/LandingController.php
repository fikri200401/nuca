<?php

namespace App\Http\Controllers;

use App\Models\Treatment;
use App\Models\Voucher;
use App\Models\Setting;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        // Get popular treatments
        $popularTreatments = Treatment::active()
            ->popular()
            ->limit(6)
            ->get();

        // Get active vouchers for landing page
        $activeVouchers = Voucher::active()
            ->forLanding()
            ->get();

        // Get clinic info from settings
        $clinicInfo = [
            'name' => Setting::get('clinic_name', 'Klinik Kecantikan'),
            'address' => Setting::get('clinic_address', ''),
            'phone' => Setting::get('clinic_phone', ''),
            'whatsapp' => Setting::get('clinic_whatsapp', ''),
            'operating_hours' => Setting::get('operating_hours', 'Senin - Sabtu: 09:00 - 18:00'),
            'about' => Setting::get('about', ''),
        ];

        return view('landing.index', compact('popularTreatments', 'activeVouchers', 'clinicInfo'));
    }

    /**
     * Show all treatments
     */
    public function treatments()
    {
        $treatments = Treatment::active()
            ->withCount('feedbacks')
            ->get();

        return view('landing.treatments', compact('treatments'));
    }

    /**
     * Show treatment detail
     */
    public function treatmentDetail($id)
    {
        $treatment = Treatment::active()
            ->with(['feedbacks' => function($query) {
                $query->visible()->orderBy('created_at', 'desc')->limit(10);
            }])
            ->findOrFail($id);

        return view('landing.treatment-detail', compact('treatment'));
    }

    /**
     * Show all active vouchers
     */
    public function vouchers()
    {
        $vouchers = Voucher::active()->get();

        return view('landing.vouchers', compact('vouchers'));
    }
}
