<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->filled('role') && array_key_exists($request->query('role'), User::ROLE_LABELS),
                fn ($q) => $q->where('role', $request->query('role')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $like = '%' . $request->query('q') . '%';
                $q->where(fn ($w) => $w->where('name', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhere('email', 'like', $like));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function toggle(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => 'Vous ne pouvez pas suspendre votre propre compte.']);
        }

        $user->forceFill(['is_active' => ! $user->is_active])->save();

        // Un compte suspendu est déconnecté immédiatement.
        if (! $user->is_active) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        return back()->with('success', $user->is_active ? 'Compte réactivé.' : 'Compte suspendu.');
    }
}