<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;


class RefreshToken extends BaseMiddleware
{
    public function __construct()
    {
        $this->auth = Auth::guard('member');
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // if ($request->getMethod() === "OPTIONS") {
        //     return response('');
        // }

        try {
            $this->checkForToken($request);
            try {
                // if ($this->auth->parseToken()->authenticate()) { 
                //     throw new UnauthorizedHttpException('jwt-auth', 'User not found');
                // }
                JWTAuth::parseToken()->authenticate();
                $payload = $this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray();
                $next_request = $next($request);
                if (isset($next_request->getData()->message) && ($next_request->getData()->message === 'Unauthenticated.')) {
                    throw new UnauthorizedHttpException('jwt-auth', 'Unauthenticated.');
                }
                return $next_request;
            } catch (TokenExpiredException $t) {
                $payload = $this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray();
                $key = 'block_refresh_token_for_user_' . $payload['sub'];
                $cachedBefore = (int)Cache::has($key);
                if ($cachedBefore) {
                    Auth::onceUsingId($payload['sub']);
                    return $next($request);
                }
                try {
                    $newtoken = $this->auth->refresh();
                    $gracePeriod = $this->auth->manager()->getBlacklist()->getGracePeriod();
                    $expiresAt = Carbon::now()->addSeconds($gracePeriod);
                    Cache::put($key, $newtoken, $expiresAt);
                } catch (JWTException $e) {
                    throw new UnauthorizedHttpException('jwt-auth','連線階段已過期, 請重新登入');
                }
            }
        } catch (TokenInvalidException $e) {
            throw new TokenInvalidException('TokenInvalidException', $e->getCode());
            // return response()->json(["error" => [
            //     'message' => $e->getMessage(),
            //     'code'=>500,
            //     'errors' => [
            //         'reason' => "TokenInvalidException",
            //         // 'domain' => "",
            //         // 'code' => $e->getCode(),
            //         // 'file' => $e->getFile(),
            //         // 'line' => $e->getLine(),
            //     ]
            // ]], 500);
        }
        $response = $next($request);
        return $this->setAuthenticationHeader($response, $newtoken);
    }

    public function checkForToken(Request $request)
    {
        if (!$this->auth->parser()->setRequest($request)->hasToken()) {
            throw new UnauthorizedHttpException('jwt-auth', 'Token not provided');
        }
    }

}
