<?php

namespace App\Http\Controllers\Web\Backend\Settings;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class SystemSettingController extends Controller
{
    /**
     * Display the system settings page.
     *
     * @return View
     */
    public function index()
    {

        $setting = SystemSetting::latest('id')->first();
        return view('backend.layouts.settings.system_settings', compact('setting'));
    }

    /**
     * Update the system settings.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'system_name'    => 'nullable|string',
            'email'          => 'required|email',
            'copyright_text' => 'nullable|string',
            'logo'           => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'favicon'        => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'platform_fee'    => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = SystemSetting::first();
        try {
            $setting                 = SystemSetting::firstOrNew();
            $setting->system_name    = $request->system_name;
            $setting->email          = $request->email;
            $setting->copyright_text = $request->copyright_text;
            $setting->logo           = $request->logo;
            $setting->favicon        = $request->favicon;
            $setting->platform_fee   = $request->platform_fee;

            if ($request->hasFile('logo')) {
                $setting->logo = uploadImage($request->file('logo'), 'logos');

                if ($data->logo) {
                    $previousImagePath = public_path($data->logo);
                    if (file_exists($previousImagePath)) {
                        unlink($previousImagePath);
                    }
                }
            } else {
                $setting->logo = $data->logo;
            }

            if ($request->hasFile('favicon')) {
                $setting->favicon = uploadImage($request->file('favicon'), 'favicons');

                if ($data->favicon) {
                    $previousImagePath = public_path($data->favicon);
                    if (file_exists($previousImagePath)) {
                        unlink($previousImagePath);
                    }
                }
            } else {
                $setting->favicon = $data->favicon;
            }


            $setting->save();
            return back()->with('t-success', 'Updated successfully');
        } catch (Exception) {
            return back()->with('t-error', 'Failed to update');
        }
    }
}
