<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        $error = $this->convertExceptionToResponse($exception);
        
        if ($exception instanceof \Illuminate\Validation\ValidationException) { /**請求驗證錯誤 */
            $response["code"] = config('response_code.validation_fail');
            $response['message'] =  collect($exception->errors())->map(function($item, $key){ return $item[0];})->first();
        } elseif ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) { /**請求資料不存在 */
            $response["code"] = config('response_code.model_not_found');
            $response['message'] =  '請求資料有誤';
        }  elseif (($exception instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException)
             || ($exception instanceof \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException)) {  /**token錯誤 */
            $response["code"] = config('response_code.token_invalid');
            $response['message'] = '連線已過期，請重新登入';
        } elseif ($exception instanceof \Illuminate\Auth\AuthenticationException) {  /**驗證身份錯誤 */
            $response["code"] = config('response_code.token_invalid');
            $response['message'] = '未授權動作，請登入帳號';
        }  elseif ($exception instanceof \Tymon\JWTAuth\Exceptions\JWTException) {   /**驗證資訊錯誤 */
            $response["code"] = config('response_code.unauthorized');
            $response['message'] = '帳密資訊有誤';
        }  else {
            $response["code"] = ($exception->getCode() > 0) ? $exception->getCode() : config('response_code.validation_fail');
            $response['message'] = empty($exception->getMessage()) ? 'something error' : $exception->getMessage();
        }

        if (config('app.debug')) {
            $response["error"]["errors"]['trace'] = $exception->getTraceAsString();
        }
        return response()->json($response, $response["code"]);
        
    }
}
