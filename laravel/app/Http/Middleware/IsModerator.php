<?php

namespace App\Http\Middleware;

use App\Enums\RoleType;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class IsModerator
{
    public function handle(Request $request, Closure $next): \Illuminate\Http\JsonResponse
    {

        $user = $request->user();

        if (!$user || !$user->hasRole(RoleType::Moderator)) {
            return response()->json(['msg' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
