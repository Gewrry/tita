<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = BusinessSetting::current();
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|in:sari_sari,restaurant',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'tax_id' => 'nullable|string|max:50',
            'currency' => 'nullable|string|max:10',
            'receipt_footer' => 'nullable|string|max:255',
            'default_table_count' => 'nullable|integer|min:1|max:100',
            'logo' => 'nullable|image|max:2048',
        ]);

        $settings = BusinessSetting::current();

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $validated['logo_path'] = $path;
        }

        unset($validated['logo']);
        $settings->update($validated);

        return redirect()->route('settings.index')
            ->with('success', 'Business settings updated successfully.');
    }
}
