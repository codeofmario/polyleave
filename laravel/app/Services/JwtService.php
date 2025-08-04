<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use UnexpectedValueException;

class JwtService
{
    private string $secret;

    private const AT_TTL = 900;
    private const RT_TTL = 604800;

    public function __construct()
    {
        $this->secret = env('JWT_SECRET');
    }

    public function issue(int $userId): array
    {
        $jti = (string)Str::uuid();

        $at = $this->encode($userId, $jti, self::AT_TTL);
        $rt = $this->encode($userId, $jti, self::RT_TTL);

        Redis::setex("$userId.$jti.at", self::AT_TTL, $at);
        Redis::setex("$userId.$jti.rt", self::RT_TTL, $rt);

        return ['access_token' => $at, 'refresh_token' => $rt];
    }

    public function refresh(string $refreshToken): array
    {
        $payload = $this->decode($refreshToken);

        $redisKey = "{$payload->sub}.{$payload->jti}.rt";
        if (Redis::get($redisKey) !== $refreshToken) {
            throw new UnexpectedValueException('Refresh token is not recognized (already used or revoked).');
        }

        Redis::del([
            "{$payload->sub}.{$payload->jti}.at",
            "{$payload->sub}.{$payload->jti}.rt",
        ]);

        return $this->issue($payload->sub);
    }

    public function logout(string $jwt): void
    {
        try {
            $payload = $this->decode($jwt);
        } catch (\Throwable $e) {
            return;
        }

        Redis::del([
            "{$payload->sub}.{$payload->jti}.at",
            "{$payload->sub}.{$payload->jti}.rt",
        ]);
    }

    public function decode(string $jwt): \stdClass
    {
        return JWT::decode($jwt, new Key($this->secret, 'HS256'));
    }

    private function encode(int $sub, string $jti, int $expSeconds): string
    {
        return JWT::encode(
            ['sub' => $sub, 'jti' => $jti, 'exp' => time() + $expSeconds],
            $this->secret,
            'HS256'
        );
    }
}
