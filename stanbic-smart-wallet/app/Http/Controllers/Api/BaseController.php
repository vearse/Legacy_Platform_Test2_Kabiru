<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function responseApi($status,$message, $data,  $code)
    {
        return response()->json([
            'status' => $status,
            'data' => $data,
            'message' => $message
        ], $code);
    }


    public function responseWithError($status,$message, $errors,  $code)
    {
        return response()->json([
            'status' => $status,
            'errors' => $errors,
            'message' => $message,
            'data' => [],
        ], $code);
    }
}
