<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        return view('backend.settings.edit', [
            'logo' => Setting::getValue('logo'),
            'customer_service_number' => Setting::getValue('customer_service_number'),
            'footer_text' => Setting::getValue('footer_text'),
            'header_text' => Setting::getValue('header_text'),
            'company_imformation' => Setting::getValue('company_imformation'),
            'customer_facebook' => Setting::getValue('customer_facebook'),
            'customer_youtube' => Setting::getValue('customer_youtube'),
            'customer_tiktok' => Setting::getValue('customer_tiktok'),
            'customer_instagram' => Setting::getValue('customer_instagram'),
            'customer_whatsapp' => Setting::getValue('customer_whatsapp'),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'customer_service_number' => 'nullable|string',
            'footer_text' => 'nullable|string',
            'header_text' => 'nullable|string',
            'company_imformation' => 'nullable|string',
            'customer_facebook' => 'nullable|string',
            'customer_youtube' => 'nullable|string',
            'customer_tiktok' => 'nullable|string',
            'customer_instagram' => 'nullable|string',
            'customer_whatsapp' => 'nullable|string',
            'hero_banners.*' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048'
        ]);

        // ✅ Logo Upload
        if ($request->hasFile('logo')) {
            $oldLogo = Setting::getValue('logo');
            if ($oldLogo && file_exists(public_path($oldLogo))) {
                unlink(public_path($oldLogo));
            }

            $logo = $request->file('logo');
            $logoName = time() . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('website-header-image'), $logoName);
            Setting::setValue('logo', 'website-header-image/' . $logoName);
        }

        // ✅ Hero Banners Upload (with delete old ones first)
        if ($request->hasFile('hero_banners')) {
            // Remove old banners
            $oldBanners = json_decode(Setting::getValue('hero_banners'), true);
            if ($oldBanners && is_array($oldBanners)) {
                foreach ($oldBanners as $old) {
                    if (file_exists(public_path($old))) {
                        @unlink(public_path($old));
                    }
                }
            }

            // Save new banners
            $bannerPaths = [];
            foreach ($request->file('hero_banners') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('website-banners'), $fileName);
                $bannerPaths[] = 'website-banners/' . $fileName;
            }

            Setting::setValue('hero_banners', json_encode($bannerPaths));
        }

        // ✅ Other Settings
        Setting::setValue('customer_service_number', $request->customer_service_number);
        Setting::setValue('footer_text', $request->footer_text);
        Setting::setValue('header_text', $request->header_text);
        Setting::setValue('company_imformation', $request->company_imformation);
        Setting::setValue('customer_facebook', $request->customer_facebook);
        Setting::setValue('customer_youtube', $request->customer_youtube);
        Setting::setValue('customer_tiktok', $request->customer_tiktok);
        Setting::setValue('customer_instagram', $request->customer_instagram);
        Setting::setValue('customer_whatsapp', $request->customer_whatsapp);

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}
