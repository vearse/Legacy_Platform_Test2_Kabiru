<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use OpenApi\Annotations as OA;

$SAS_URL = env('SAS_URL', 'http://localhost:7001');
$SWM_URL = env('SWM_URL', 'http://localhost:7002');
$SMC_URL = env('SMC_URL', 'http://localhost:7003');



/**
 * @OA\Info(
 *     title="SAS API Proxy",
 *     version="1.0.0",
 *     description="Proxy routes for SAS Authentication",
 *     @OA\Contact(
 *         email="support@yourcompany.com",
 *         name="Your Company Support"
 *     )
 * )
 *
 * @OA\Server(
 *     url="/api/sas",
 *     description="SAS API Proxy Endpoint"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */


// SAS Routes
Route::prefix('sas')->middleware('cors')->group(function () use ($SAS_URL) {
    /**
     * @OA\Post(
     *     path="/login",
     *     summary="SAS User Login",
     *     tags={"SAS Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="expires_in", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */

    Route::post('/login', function (Request $request) use ($SAS_URL) {
        return Http::post("$SAS_URL/api/login", $request->all());
    });

    /**
     * @OA\Post(
     *     path="/register",
     *     summary="SAS User Registration",
     *     tags={"SAS Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful registration",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object"),
     *             @OA\Property(property="token", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error"
     *     )
     * )
     */
    Route::post('/register', function (Request $request) use ($SAS_URL) {
        return Http::post("$SAS_URL/api/register", $request->all());
    });

    Route::middleware(['auth:api', 'auth.proxy'])->group(function () use ($SAS_URL) {
         /**
             * @OA\Post(
             *     path="/refresh",
             *     summary="Refresh SAS Authentication Token",
             *     tags={"SAS Authentication"},
             *     security={{"bearerAuth":{}}},
             *     @OA\Response(
             *         response=200,
             *         description="Token refreshed successfully",
             *         @OA\JsonContent(
             *             @OA\Property(property="access_token", type="string"),
             *             @OA\Property(property="token_type", type="string", example="bearer"),
             *             @OA\Property(property="expires_in", type="integer")
             *         )
             *     )
             * )
             */
        Route::post('/refresh', function (Request $request) use ($SAS_URL) {
            return Http::withHeaders(['Authorization' => $request->header('Authorization')])->post("$SAS_URL/api/refresh", $request->all());
        });

        // Route::post('/refresh', fn(Request $request) => Http::post("$SAS_URL/api/refresh", $request->all()));

        /**
         * @OA\Post(
         *     path="/logout",
         *     summary="SAS User Logout",
         *     tags={"SAS Authentication"},
         *     security={{"bearerAuth":{}}},
         *     @OA\Response(
         *         response=200,
         *         description="Successful logout",
         *         @OA\JsonContent(
         *             @OA\Property(property="success", type="boolean", example=true),
         *             @OA\Property(property="message", type="string", example="Logged out successfully")
         *         )
         *     )
         * )
         */

        // Route::post('/logout', fn(Request $request) => Http::post("$SAS_URL/api/logout", $request->all()));
        Route::post('/logout', function (Request $request) use ($SAS_URL) {
            return Http::withHeaders(['Authorization' => $request->header('Authorization')])->post("$SAS_URL/api/logout", $request->all());
        });
    });
});

// Stanbic Wallet Manager  Routes
Route::middleware(['auth:api', 'auth.proxy'])->prefix('swm')->group(function () use ($SWM_URL) {
    Route::get('/wallet/balance', function (Request $request) use ($SWM_URL) {
       return Http::withHeaders(['Authorization' => $request->header('Authorization')])
                    ->get("$SWM_URL/api/wallet/balance");
    });

    Route::post('/wallet/transfer', function (Request $request) use ($SWM_URL) {
       return Http::withHeaders(['Authorization' => $request->header('Authorization')])
                    ->post("$SWM_URL/api/wallet/transfer", $request->all());
    });
});

// SMC Routes
// SMC Routes
Route::prefix('smc')->group(function () use ($SMC_URL) {
    Route::get('/order/status/{orderId}', function (Request $request, $orderId) use ($SMC_URL) {
        return Http::withHeaders(['Authorization' => $request->header('Authorization')])
                     ->get("$SMC_URL/api/order/status/$orderId");
    });

    Route::post('/order/initiate', function (Request $request) use ($SMC_URL) {
        return Http::withHeaders(['Authorization' => $request->header('Authorization')])
                     ->post("$SMC_URL/api/order/initiate", $request->all());
    });
});


Route::get('/health-check', function (Request $request) {
    return response()->json([
        'status' => true,
        'message' => "Hi. The API Gateway server is working pretty fine",
    ], 200);
});
