<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Services\client_xlv2;

class ClientController
{
    public function input_number(Request $request)
    {
        $validati = $request->only('nomor');
        $client_xl = new client_xlv2($validati['nomor']);
        $authenticate = $client_xl->authenticate();

        $date = Date('d-m-y');
        return view('welcome', [
            'body' => $authenticate,
            'massage' => "$date [info] > nomor anda: ". $authenticate['number'] . "\n" .
                         "$date [info] > filename: ". $authenticate['filename'] . "\n",
        ]);
    }
    
    public function api_input_number($nomor)
    {
        $client_xl = new client_xlv2($nomor);
        $authenticate = $client_xl->authenticate();

        return response()->json([
            'body' => $authenticate,
        ], 200);
    }
    
    public function api_input_otp($nomor, $otp, $file)
    {
        $client_xl = new client_xlv2($nomor);
        $otp = $client_xl->processValidatiOtp($otp, $file, $nomor);

        return response()->json([
            'body' => $otp,
        ], 200);
    }
    
    public function input_otp(Request $request)
    {
        $validati = $request->only(['nomor','otp','file']);
        $client_xl = new client_xlv2($validati['nomor']);
        $otp = $client_xl->processValidatiOtp($validati['otp'], $validati['file'], $validati['nomor']);
        return view('status-login-client', [
            'massage' => $otp,
        ]);
    }
}