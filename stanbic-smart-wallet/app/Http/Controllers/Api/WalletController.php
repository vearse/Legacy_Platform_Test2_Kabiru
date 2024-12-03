<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends BaseController
{
    public function balance()
    {
        $user = auth()->user();

        return $this->responseApi(true, __("Wallet Ballance"),['balance' => $user->wallet_balance], 200);
    }

    public function transfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'recipient_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return $this->responseWithError(false, __("Validation Error"), $errors, 400);
        }

        $user = $request->user();
        $recipient = User::findOrFail($request->recipient_id);

        if ($user->id  === $recipient->id) {
            return $this->responseApi(false, __("Invalid Transaction."), [], 400);
        }

        if ($user->wallet_balance < $request->amount) {
            return $this->responseApi(false, __("Insufficient balance."), [], 400);
        }

        $user->wallet_balance -= $request->amount;
        $recipient->wallet_balance += $request->amount;

        $user->save();
        $recipient->save();
        // Transaction Log for User and Recipient can be added here
        $data = ['remaining_balance' => $user->wallet_balance];

        return $this->responseApi(true, __("Transfer completed"), $data, 200);
    }

}
