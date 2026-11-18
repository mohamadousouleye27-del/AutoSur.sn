<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePartnerApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isPartner() && $user->partner_status !== 'approved') {
            return response()->view('partners.pending', ['status' => $user->partner_status]);
        }

        return $next($request);
    }
}