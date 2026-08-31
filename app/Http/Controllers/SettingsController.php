<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
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
            'demo_mode' => ['sometimes', 'boolean'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
        ]);

        $newDemoMode = $request->boolean('demo_mode');
        $oldDemoMode = Setting::get('demo_mode', '1') === '1';

        $data['demo_mode'] = $newDemoMode ? '1' : '0';

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

        if ($newDemoMode !== $oldDemoMode) {
            $this->syncDemoData($newDemoMode);
        }

        return redirect()->back()->with('status', 'Pengaturan berhasil disimpan.');
    }

    private function syncDemoData(bool $enabled): void
    {
        if ($enabled) {
            Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\DatabaseSeeder',
                '--force' => true,
            ]);

            return;
        }

        DB::table('invoice_items')->truncate();
        DB::table('invoices')->truncate();
        DB::table('notification_logs')->truncate();
        DB::table('service_logs')->truncate();
        DB::table('service_orders')->truncate();
        DB::table('devices')->truncate();

        $accountIds = User::whereIn('email', ['admin@mail.com', 'teknisi@mail.com', 'customer@mail.com'])->pluck('id');
        Customer::query()->whereNotIn('user_id', $accountIds)->orWhereNull('user_id')->delete();
    }
}
