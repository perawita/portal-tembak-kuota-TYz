<?php

namespace App\Http\Controllers;

use App\Http\Services\session;

class SessionController extends Controller
{
    public function index()
    {
        $session = new session(['payment', 'methodPay']);
        return $session ? $session : response()->json(['message' => 'Session cleaned up successfully'], 200);
    }
}
