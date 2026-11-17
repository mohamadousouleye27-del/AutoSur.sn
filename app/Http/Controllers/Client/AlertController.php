<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index(Request $request)
    {
        return view('client.alerts.index', [
            'notifications' => $request->user()->notifications()->latest()->take(50)->get(),
        ]);
    }

    /** Marque comme lue puis ouvre l'élément concerné. */
    public function read(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect($notification->data['url'] ?? route('client.alerts.index'));
    }

    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}