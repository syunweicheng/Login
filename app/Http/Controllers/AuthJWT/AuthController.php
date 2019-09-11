<?php

namespace App\Http\Controllers\AuthJWT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;

/** Service Models */
use App\Repositories\MemberRepository;

use Exception;

class AuthController extends Controller
{
    public function __construct(
        MemberRepository $MemberRepository
    ) {
        $this->MemberRepository = $MemberRepository;
    }


    /**
     * Login | 登入 - AuthJWT.login
     * Get a JWT token via given credentials.
     * 登入
     *
     * @param  App\Http\Requests\AuthRequest $request
     * @return \Illuminate\Http\Response
     *
     */
    public function login(Request $request)
    {
        return response()->json($this->user_login($request), config('response_code.success'));
    }

    /**
     * Logout | 登出 - user.logout
     * Log the user out (Invalidate the token).
     * 登出
     * 
     * Headers need Authorization.
     * ex. Authorization: bearer xxxxxxxxxxx
     * 
     */
    public function logout(Request $request)
    {
        auth()->logout();
        return response()->json(
            ['data' => ['message' => "登出成功"]], config('response_code.success')
        );
    }

    /**
     * Refresh | 更新 - user.refresh
     * Refresh a token.
     * 更新Token
     * 
     * headers need Authorization
     * @return \Illuminate\Http\Response
     */
    public function refresh(Request $request)
    {
        return $this->respondWithToken(auth('member')->refresh(), auth('member')->factory()->getTTL() * 60, $request);
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\Response
     */
    protected function respondWithToken($token, $ttl, $request)
    {
        return response()->json(['data' => [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $ttl
        ]], 200);
    }

    public function user_login(Request $request)
    { 
        try{
            $credentials = $request->only('mobile', 'password');
            if (!$token = auth('member')->attempt($credentials)) {
                throw new JWTException(); 
            }
            $this->MemberRepository->setPresenter('App\Presenters\MemberPresenter');
            $user = $this->MemberRepository->find(auth('member')->user()->id);
            $expires_in = auth('member')->factory()->getTTL() * 60;
            
        } catch (JWTException $e) {
            throw new JWTException(__('response.authorize.invalid_credentials'), config('response_code.unauthorize'));
        }
        
        return ['data' => [
            'member' => $user['data'],
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expires_in
        ]];
    }
}
