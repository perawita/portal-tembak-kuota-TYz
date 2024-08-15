<?php

namespace App\Http\Services;

class refres_token_login
{
    public function __construct()
    {
        $refresh_token = $this->get_refresh_token_from_local();

        $api_url = 'http://token.virtualtunneling.tech/refresh.php';

        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['refresh_token' => $refresh_token]));
        curl_setopt($ch, CURLOPT_ENCODING, '');

        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_status != 200) {
            return ("Request failed with status $http_status");
        }

        $response_data = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            session(['response_json' => json_encode($response_data, JSON_PRETTY_PRINT)]);
            return ("Berhasil melakukan refres token");
        } else {
            return json_encode(['error' => 'Response bukan format JSON: ' . $response]);
        }
    }


    // Fungsi untuk mengambil refresh token dari file response.json
    function get_refresh_token_from_local()
    {
        $filename = session('response_json'); 
        if ($filename) {
            $data = json_decode($filename, true);

            if (isset($data['refresh_token'])) {
                return $data['refresh_token'];
            } else {
                return ("refresh_token tidak ditemukan di data");
            }
        } else {
            return ("File data tidak ditemukan.");
        }
    }
}
