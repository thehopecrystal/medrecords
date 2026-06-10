<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\SiteSetting;
use Inertia\Inertia;

class LandingController extends Controller
{
    public function index()
    {
        $plans = Plan::active()->get()->map(fn($p) => [
            'id'             => $p->id,
            'name'           => $p->name,
            'price'          => $p->price,
            'billing_period' => $p->billing_period,
            'description'    => $p->description,
            'features'       => $p->features ?? [],
            'badge'          => $p->badge,
            'is_featured'    => $p->is_featured,
        ]);

        return Inertia::render('Landing', [
            'heroHead'    => SiteSetting::get('hero_heading', 'Your health record,<br>always with you'),
            'heroSub'     => SiteSetting::get('hero_subtext', 'MedRecords is Nigeria\'s first patient-controlled electronic health record platform. Securely share your records with any doctor, pharmacist, or specialist — on your terms.'),
            'heroCta1'    => SiteSetting::get('hero_cta1', 'Get started free'),
            'heroCta2'    => SiteSetting::get('hero_cta2', 'Learn more'),
            'footerAbout' => SiteSetting::get('footer_about', 'MedRecords is building a connected health ecosystem for Nigeria — empowering patients with control, privacy, and access to their own records.'),
            'contact'     => [
                'phone1'   => SiteSetting::get('phone1'),
                'phone2'   => SiteSetting::get('phone2'),
                'email'    => SiteSetting::get('contact_email'),
                'address'  => SiteSetting::get('address'),
                'city'     => SiteSetting::get('city'),
                'state'    => SiteSetting::get('state'),
            ],
            'social' => [
                'facebook'  => SiteSetting::get('facebook'),
                'twitter'   => SiteSetting::get('twitter'),
                'instagram' => SiteSetting::get('instagram'),
                'linkedin'  => SiteSetting::get('linkedin'),
            ],
            'plans' => $plans,
        ]);
    }
}
