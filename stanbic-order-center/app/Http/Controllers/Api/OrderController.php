<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends BaseController
{
    public function status(Request $request, $orderId)
    {
        $user = auth()->user();

        $order = Order::where('id', $orderId)->where('user_id', $user->id)->first();

        if (!$order) {

            return $this->responseApi(false, __("Order not found"),['status' => $order->status,], 404);
        }

        return $this->responseApi(true, __("Order Status"),['status' => $order->status,], 200);
    }

    public function initiate(Request $request)
    {
        // Validation Request can be use
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'item' => 'required|between:3,30'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return $this->responseWithError(false, __("Validation Error"), $errors, 400);
        }

        $user = $request->user();

        if ($user->wallet_balance < $request->amount) {
            return $this->responseApi(false, __("Insufficient wallet balance to initiate the order.."), [], 400);
        }

        // Debit wallet account
        $user->wallet_balance -= $request->amount;
        $user->save();

        $order = Order::create([
            'user_id' => $user->id,
            'name' => $request->item,
            'amount' => $request->amount,
            'status' => 'pending',
        ]);

        $user->save();
        $data = ['order' => $order];

        return $this->responseApi(true, __("Order initiated successfully."), $data, 200);
    }

}
