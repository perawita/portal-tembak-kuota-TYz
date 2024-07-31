<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Crypt;

use App\Models\Quota;
use App\Models\Balance;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function reduce_balance(Request $request)
    {
        $quota = Quota::where('id', (int)$request->input('id'))->first();
        $user = Balance::where('user_id', auth()->id())->first();

        $user->amount -= $quota->price;
        $user->save();

        return response()->json(['success'], 200);
    }
}
