<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class IsAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['msg' => 'Missing token'], 401);
        }

        try {
            $payload = JWT::decode(
                $token,
                new Key(config('app.jwt_secret', env('JWT_SECRET')), 'HS256')
            );
        } catch (\Throwable $e) {
            return response()->json(['msg' => 'Invalid or expired token'], 401);
        }

        $allowListKey = "{$payload->sub}.{$payload->jti}.at";
        if (!Redis::exists($allowListKey)) {
            return response()->json(['msg' => 'Token revoked'], 401);
        }

        $request->setUserResolver(fn () => User::find($payload->sub));

        $request->attributes->set('jwt_payload', $payload);

        return $next($request);
    }
}
