<?php

namespace App\Virtual;

/**
 * @OA\Info(
 *     title="SAS API Proxy",
 *     version="1.0.0",
 *     description="Proxy routes for SAS Authentication",
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
 *
 * @OA\Schema(
 *     schema="LoginRequest",
 *     required={"email", "password"},
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="password", type="string", format="password", example="password123")
 * )
 *
 * @OA\Schema(
 *     schema="RegisterRequest",
 *     required={"name", "email", "password"},
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="password", type="string", format="password", example="password123")
 * )
 *
 * @OA\Schema(
 *     schema="AuthResponse",
 *     @OA\Property(property="access_token", type="string"),
 *     @OA\Property(property="token_type", type="string", example="bearer"),
 *     @OA\Property(property="expires_in", type="integer")
 * )
 *
 * @OA\PathItem(
 *     path="/sas",
 *     description="SAS API Proxy Endpoints"
 * )
 */
class OpenApiSchemas
{

}
