<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        return view('settings.edit');
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_toko' => ['required', 'string', 'max:255'],
            'alamat_toko' => ['nullable', 'string', 'max:500'],
            'telepon_toko' => ['nullable', 'string', 'max:20'],
            'tagline_toko' => ['nullable', 'string', 'max:255'],
            'footer_invoice' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            $oldLogo = Setting::get('logo');

            $path = $request->file('logo')->store('logos', 'public');

            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }

            $data['logo'] = $path;
        } else {
            unset($data['logo']);
        }

        foreach ($data as $key => $value) {
            if ($value === null) {
                $value = '';
            }
            Setting::set($key, $value);
        }
        Setting::forgetCache();

        return back()->with('status', 'Pengaturan berhasil disimpan.');
    }
}
