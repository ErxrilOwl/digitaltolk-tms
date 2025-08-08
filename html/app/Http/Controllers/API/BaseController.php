<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(
 *     version="1.0",
 *     title="DigitalTolk - Translation Management System"
 * )
 * @OA\SecurityScheme(
 *  securityScheme="sanctum",
 *  type="http",
 *  scheme="bearer",
 *  bearerFormat="JWT",
 *  description="Enter token in the format: **Bearer <token>**",
 * )
 */
class BaseController extends Controller
{
    /**
     * @param array $result - contains object for result
     * @param string $message - container message
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResponse($result, $message, $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }

    /**
     * @param array $error - contains error message
     * @param string $errorMessages - array of error messages
     * @param int $code - error code
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendError($error, $errorMessages = [], $code = 404): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
