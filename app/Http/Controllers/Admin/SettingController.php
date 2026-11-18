<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        return view('admin.settings.edit', [
            'mail' => Setting::bool('alerts_mail', (bool) config('alerts.channels.mail')),
            'sms'  => Setting::bool('alerts_sms', (bool) config('alerts.channels.sms')),
        ]);
    }

    public function update(Request $request)
    {
        Setting::put('alerts_mail', $request->boolean('alerts_mail') ? '1' : '0');
        Setting::put('alerts_sms', $request->boolean('alerts_sms') ? '1' : '0');

        return back()->with('success', 'Paramètres enregistrés.');
    }
}