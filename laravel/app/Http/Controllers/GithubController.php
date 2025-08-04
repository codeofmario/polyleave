<?php

namespace App\Http\Controllers;

use App\Enums\RoleType;
use App\Models\Role;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class GithubController extends Controller
{
    private const STATE_TTL = 600;

    private const GITHUB_SCOPES = 'read:user user:email';

    public function authUrl(Request $request): JsonResponse
    {
        $state = bin2hex(random_bytes(20));

        Redis::setex("gh_state:$state", self::STATE_TTL, '1');

        $url = 'https://github.com/login/oauth/authorize?' . http_build_query([
                'client_id' => config('services.github.client_id'),
                'redirect_uri' => config('services.github.redirect'),
                'scope' => self::GITHUB_SCOPES,
                'state' => $state,
            ]);

        return response()->json(['url' => $url]);
    }

    public function callback(Request $request, JwtService $jwt): JsonResponse
    {
        $state = $request->input('state');

        if (!$state || !Redis::del("gh_state:$state")) {
            return response()->json(['msg' => 'Invalid state.'], Response::HTTP_FORBIDDEN);
        }

        try {
            $tokenResp = Http::asForm()
                ->acceptJson()
                ->timeout(10)
                ->post('https://github.com/login/oauth/access_token', [
                    'client_id' => config('services.github.client_id'),
                    'client_secret' => config('services.github.client_secret'),
                    'code' => $request->input('code'),
                    'redirect_uri' => config('services.github.redirect'),
                ]);
        } catch (\Throwable $e) {
            return response()->json(
                ['msg' => 'GitHub unreachable.', 'error' => $e->getMessage()],
                Response::HTTP_BAD_GATEWAY
            );
        }

        if ($tokenResp->failed() || !$tokenResp->json('access_token')) {
            return response()->json(
                ['msg' => 'Token exchange failed.', 'gh_error' => $tokenResp->json()],
                Response::HTTP_BAD_REQUEST
            );
        }
        $ghToken = $tokenResp->json('access_token');

        $ghHeaders = [
            'Authorization' => "Bearer {$ghToken}",
            'Accept' => 'application/vnd.github+json',
        ];

        $ghUser = Http::withHeaders($ghHeaders)->timeout(10)
            ->get('https://api.github.com/user')
            ->throw()
            ->json();

        if (empty($ghUser['email'])) {
            $emails = Http::withHeaders($ghHeaders)->timeout(10)
                ->get('https://api.github.com/user/emails')
                ->throw()
                ->collect();

            $primary = $emails->firstWhere(fn($e) => $e['primary'] && $e['verified']);
            $ghUser['email'] = $primary['email'] ?? null;
        }

        $email = $ghUser['email'] ?? "{$ghUser['id']}@users.noreply.github.com";

        $user = User::firstWhere([
            'provider' => 'github',
            'provider_id' => $ghUser['id'],
        ]) ?: User::whereNull('provider')->where('email', $email)->first();

        $payload = [
            'name' => $ghUser['name'] ?? $ghUser['login'],
            'email' => $email,
            'provider' => 'github',
            'provider_id' => $ghUser['id'],
        ];

        $user ? $user->update($payload) : $user = User::create($payload);

        $roleId = Role::where('name', RoleType::User->value)->value('id');
        $user->roles()->syncWithoutDetaching($roleId);

        $tokens = $jwt->issue($user->id);

        return response()->json([
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
        ]);
    }
}
