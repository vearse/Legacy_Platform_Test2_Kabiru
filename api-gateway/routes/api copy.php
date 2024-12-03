<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use OpenApi\Annotations as OA;

$SAS_URL = env('SAS_URL', 'http://localhost:7001');
$SWM_URL = env('SWM_URL', 'http://localhost:7002');
$SMC_URL = env('SMC_URL', 'http://localhost:7003');

// SAS Routes
Route::prefix('sas')->group(function () use ($SAS_URL) {


    Route::post('/login', function (Request $request) use ($SAS_URL) {
        return Http::post("$SAS_URL/api/login", $request->all());
    });


    Route::post('/register', function (Request $request) use ($SAS_URL) {
        return Http::post("$SAS_URL/api/register", $request->all());
    });

    Route::middleware(['auth:api', 'auth.proxy'])->group(function () use ($SAS_URL) {

        Route::post('/refresh', function (Request $request) use ($SAS_URL) {
            info(['Forwarding Authorization Header:', $SAS_URL,$request->header('Authorization')]);
            return Http::withHeaders([
                'Authorization' => $request->header('Authorization'),
            ])->post("$SAS_URL/api/refresh", $request->all());
            // return $response->json();
        });

        // Route::post('/logout', fn(Request $request) => Http::post("$SAS_URL/api/logout", $request->all()));
        Route::post('/logout', function (Request $request) use ($SAS_URL) {
            info("$SAS_URL/api/logout", $request->all());
            return Http::post("$SAS_URL/api/logout", $request->all());
        });
    });
});

// SWM Routes
Route::prefix('swm')->group(function () use ($SWM_URL) {
    Route::get('/balance/{userId}', function ($userId) use ($SWM_URL) {
        return Http::get("$SWM_URL/api/balance/$userId");
    });

    Route::post('/fund-wallet', function (Request $request) use ($SWM_URL) {
        return Http::post("$SWM_URL/api/fund-wallet", $request->all());
    });
});

// SMC Routes
Route::prefix('smc')->group(function () use ($SMC_URL) {
    Route::post('/mint', function (Request $request) use ($SMC_URL) {
        return Http::post("$SMC_URL/api/mint", $request->all());
    });
});


Route::get('/health-check', function (Request $request) {
    return response()->json([
        'status' => true,
        'message' => "Hi. The API Gateway server is working pretty fine",
    ], 200);
});
