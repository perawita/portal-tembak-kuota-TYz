<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

use App\Models\File;

class client_xlv2
{
    protected $number = null;
    private $otp_api_url = null;
    private $auth_api_url = null;
    private $api_token = null;

    /**
     * Constructor method for xlv2 class.
     *
     * Initializes a new instance of the class with the provided phone number.
     * If the provided number starts with '0', it will be replaced with '62' to comply with
     * the Indonesian phone number format.
     *
     * @param string $number The phone number to be processed.
     */
    public function __construct($number)
    {
        
        $this->otp_api_url = 'http://ciam-xl.virtualtunneling.tech:10000/v7/validate/otp';
        $this->auth_api_url = 'http://ciam-xl.virtualtunneling.tech:10000/v7/validate/auth';
        $this->api_token = 'VGVzdFNlY3JldEtleQ==';

        if (substr($number, 0, 1) === '0') {
            // If the provided number starts with '0', replace it with '62'
            $this->number = '62' . substr($number, 1);
        } else {
            // If the provided number does not start with '0', keep it as is
            $this->number = $number;
        }
    }



    /**
     * Carry out the authentication process using the provided API.
     *
     * @return array|null The array contains authentication data if successful, null if failed.
     */
    public function authenticate()
    {
        // Step 1: Request OTP
        $otp_args = urlencode($this->number);
        $filename = 'root/cache/ayz/' . uniqid();

        $url = $this->otp_api_url . '?arg1=' . urlencode($otp_args) . '&arg2=' . urlencode($filename);

        // Inisialisasi curl untuk OTP request
        $ch = curl_init($url);

        // Mengatur opsi curl
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Auth-Api: ' . $this->api_token,
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, seperti Gecko) Chrome/123.0.0.0 Safari/537.36 Edg/123.0.0.0'
        ]);

        // Eksekusi curl dan mendapatkan respons
        $response = curl_exec($ch);

        // Memeriksa kesalahan curl
        if (curl_errno($ch)) {
            return null;
            exit;
        } else {
            return [
                'filename' => $filename,
                'number' => $this->number
            ];
        }
    }

    /**
     * OTP validation process for authentication.
     *
     * @param string $otp One Time Password to be validated.
     * @return void
     */
    public function processValidatiOtp($otp, $filename, $nomor)
    {
        //$authArgs = urlencode($otp . ' ' . $deviceId . ' ' . $nomor);
        $url = $this->auth_api_url . '?arg1=' . urlencode($otp) . '&arg2=' . urlencode($filename);

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Auth-Api: ' . $this->api_token,
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, seperti Gecko) Chrome/123.0.0.0 Safari/537.36 Edg/123.0.0.0'
        ]);

        $loginResponse = curl_exec($ch);

        if (curl_errno($ch)) {
            return 'Process cURL error';
        } else {
            $login_response_array = json_decode($loginResponse, true);

            if ($login_response_array['status_code'] == 200 && $login_response_array['status'] === true) {
                $filtered_response = $login_response_array['data']['success_resp'];
                Storage::disk('local')->put(
                    'Data-users/'.$nomor.'.json', 
                    json_encode($filtered_response, JSON_PRETTY_PRINT
                ));


                $file = File::updateOrCreate(
                    ['name' => $nomor],
                    [
                        'unix_name' => $filename,
                        'path' => 'Data-users/',
                        'mime_type' => '.json',
                        'size' => Storage::disk('local')->size('Data-users/'.$nomor.'.json')
                    ]
                );

                return 'verifikasi nomor berhasil di lakukan. tolong informasikan ke admin. terimakasi sudah menggunakan layanan';

            } else {
                return 'verifikasi nomor gagal di lakukan. tolong masukan data yang di butuhkan dengan sesuai. terimakasi sudah menggunakan layanan '. env('APP_NAME');
            }
        }
    }
}
