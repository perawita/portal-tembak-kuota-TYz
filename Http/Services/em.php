<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class em
{
    protected $number;
    protected $mail;

    /**
     * Constructor method for Em class.
     *
     * @param string $number The phone number to be processed.
     * @param string $email The email to be processed.
     */
    public function __construct($number, $email)
    {
        // Validasi number
        if (substr($number, 0, 1) === '0') {
            // If the provided number starts with '0', replace it with '62'
            $this->number = '62' . substr($number, 1);
        } else {
            // If the provided number does not start with '0', keep it as is
            $this->number = $number;
        }

        // Validasi email
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->mail = $email;
        } else {
            return null;
        }
    }

    // Fungsi untuk membuat UUID v4
    private function generateUUIDv4()
    {
        return (string) Str::uuid();
    }

    // Fungsi untuk mengirimkan permintaan HTTP
    private function sendHttpRequest($url, $postData, $headers)
    {
        $response = Http::withHeaders($headers)->post($url, $postData);

        if ($response->failed()) {
            return null;
        }

        return $response->json();
    }

    public function authenticate()
    {
        $urlCurl = "https://ciam-rajaampat.xl.co.id/am/json/realms/xl/authenticate?authIndexType=service&authIndexValue=xlslotpbyemail";

        // Langkah 1: Mendapatkan authId awal
        $headersStep1 = [
            'x-dynatrace: MT_3_4_2797956835_2-0_24d94a15-af8c-49e7-96a0-1ddb48909564_0_2_68',
            'Accept-API-Version: resource=2.1, protocol=1.0',
            'Content-Type: application/json; charset=utf-8',
            'Host: ciam-rajaampat.xl.co.id',
            'Connection: Keep-Alive'
        ];

        $responseStep1 = $this->sendHttpRequest($urlCurl, [], $headersStep1);
        $authId = $responseStep1['authId'] ?? null;
        if (!$authId) {
            return null;
        }

        // Langkah 2: Mengirim data email dan nomor telepon
        $postDataStep2 = [
            'authId' => $authId,
            'stage' => 'PAIREMAIL',
            'callbacks' => [
                [
                    'type' => 'MetadataCallback',
                    'output' => [
                        ['name' => 'data', 'value' => ['stage' => 'PAIREMAIL']]
                    ],
                    '_id' => 0
                ],
                [
                    'type' => 'NameCallback',
                    'output' => [['name' => 'prompt', 'value' => 'EMAIL']],
                    'input' => [['name' => 'IDToken2', 'value' => $this->mail]],
                    '_id' => 1
                ],
                [
                    'type' => 'HiddenValueCallback',
                    'output' => [['name' => 'value', 'value' => ''], ['name' => 'id', 'value' => 'MSISDN']],
                    'input' => [['name' => 'IDToken3', 'value' => $this->number]],
                    '_id' => 2
                ],
                [
                    'type' => 'PasswordCallback',
                    'output' => [['name' => 'prompt', 'value' => 'Password']],
                    'input' => [['name' => 'IDToken4', 'value' => 'NOICCID8000']],
                    '_id' => 3
                ]
            ]
        ];

        $headersStep2 = [
            'x-dynatrace: MT_3_4_2797956835_2-0_24d94a15-af8c-49e7-96a0-1ddb48909564_0_510_72',
            'Accept-API-Version: resource=2.1, protocol=1.0',
            'Content-Type: application/json; charset=utf-8',
            'Host: ciam-rajaampat.xl.co.id',
            'Connection: Keep-Alive',
            'User-Agent: okhttp/4.3.1'
        ];

        $responseStep2 = $this->sendHttpRequest($urlCurl, $postDataStep2, $headersStep2);
        $authId1 = $responseStep2['authId'] ?? null;
        if (!$authId1) {
            return null;
        }

        // Langkah 3: Mengirim data perangkat
        $uuid = $this->generateUUIDv4();

        $postDataStep3 = [
            'authId' => $authId1,
            'stage' => 'DEVICE',
            'callbacks' => [
                [
                    'type' => 'MetadataCallback',
                    'output' => [
                        ['name' => 'data', 'value' => ['stage' => 'DEVICE']]
                    ],
                    '_id' => 5
                ],
                [
                    'type' => 'HiddenValueCallback',
                    'output' => [
                        ['name' => 'value', 'value' => 'Input Device Information'],
                        ['name' => 'id', 'value' => 'DeviceInformation']
                    ],
                    'input' => [['name' => 'IDToken2', 'value' => $uuid]],
                    '_id' => 6
                ]
            ]
        ];

        $headersStep3 = [
            'x-dynatrace: MT_3_4_2797956835_2-0_24d94a15-af8c-49e7-96a0-1ddb48909564_0_583_136',
            'Accept-API-Version: resource=2.1, protocol=1.0',
            'Content-Type: application/json; charset=utf-8',
            'Host: ciam-rajaampat.xl.co.id',
            'Connection: Keep-Alive',
            'User-Agent: okhttp/4.3.1'
        ];

        $responseStep3 = $this->sendHttpRequest($urlCurl, $postDataStep3, $headersStep3);
        $authId2 = $responseStep3['authId'] ?? null;
        if (!$authId2) {
            return null;
        }

        // Langkah 4: Validasi data
        $postDataStep4 = [
            'authId' => $authId2,
            'stage' => 'VALIDATE',
            'callbacks' => [
                [
                    'type' => 'MetadataCallback',
                    'output' => [
                        ['name' => 'data', 'value' => ['stage' => 'VALIDATE']]
                    ],
                    '_id' => 8
                ],
                [
                    'type' => 'ConfirmationCallback',
                    'output' => [
                        ['name' => 'prompt', 'value' => 'Validate'],
                        ['name' => 'messageType', 'value' => 0],
                        ['name' => 'options', 'value' => ['0 = NO', '1 = YES']],
                        ['name' => 'optionType', 'value' => -1],
                        ['name' => 'defaultOption', 'value' => 0]
                    ],
                    'input' => [['name' => 'IDToken2', 'value' => 1]],
                    '_id' => 9
                ],
                [
                    'type' => 'TextOutputCallback',
                    'output' => [
                        ['name' => 'message', 'value' => '{"subscriber-id": "\"634083342\"", "subscriber-status": "\"A\"", "subscriber-type": "\"PREPAID\"", "isFirstLogin": "\"FALSE\""}'],
                        ['name' => 'messageType', 'value' => '0']
                    ],
                    '_id' => 10
                ]
            ]
        ];

        $headersStep4 = [
            'x-dynatrace: MT_3_4_2797956835_2-0_24d94a15-af8c-49e7-96a0-1ddb48909564_97_259_168',
            'Accept-API-Version: resource=2.1, protocol=1.0',
            'Content-Type: application/json; charset=utf-8',
            'Host: ciam-rajaampat.xl.co.id',
            'Connection: Keep-Alive',
            'User-Agent: okhttp/4.3.1'
        ];

        $responseStep4 = $this->sendHttpRequest($urlCurl, $postDataStep4, $headersStep4);

        session(['auth' => $responseStep4['authId']]);
        return ['auth' => $responseStep4['authId'], 'callbacks' => $responseStep4['callbacks']];
    }

    /**
     * OTP validation process for authentication.
     *
     * @param string $otp One Time Password to be validated.
     * @return void
     */
    public function processValidatiOtp($otp, $auth)
    {

        $curl  = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://ciam-rajaampat.xl.co.id/am/json/realms/xl/authenticate',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{"authId":"' . $auth . '","stage":"OTP","callbacks":[{"type":"MetadataCallback","output":[{"name":"data","value":{"stage":"OTP"}}],"_id":0},{"type":"PasswordCallback","output":[{"name":"prompt","value":"One Time Password"}],"input":[{"name":"IDToken2","value":"' . $otp . '"}],"_id":1},{"type":"TextOutputCallback","output":[{"name":"message","value":"{\"code\":\"000\",\"data\":{\"max_validation_attempt_suspend_duration\":\"900\",\"max_validation_attempt\":5,\"sent_to\":\"SMS\",\"next_resend_allowed_at\":\"1705854524\"},\"status\":\"SUCCESS\"}"},{"name":"messageType","value":"0"}],"_id":2},{"type":"ConfirmationCallback","output":[{"name":"prompt","value":""},{"name":"messageType","value":0},{"name":"options","value":["Submit OTP","Request OTP"]},{"name":"optionType","value":-1},{"name":"defaultOption","value":0}],"input":[{"name":"IDToken4","value":0}],"_id":3}]}',
            CURLOPT_HTTPHEADER => array(
                'Host: ciam-rajaampat.xl.co.id',
                'accept-api-version: resource=2.1, protocol=1.0',
                'content-type: application/json',
                'Accept: application/json',
                'accept-encoding: gzip',
                'user-agent: okhttp/4.3.1',
            ),
        ));

        // $response5 = curl_exec($curl);

        // curl_close($curl);
        // return $response5;

        // Eksekusi cURL dan dapatkan respons
        $response5 = curl_exec($curl);

        // Periksa apakah ada kesalahan cURL
        if ($response5 === false) {
            return 'CURL Error: ' . curl_error($curl);
        } else {
            // Memeriksa status kode HTTP
            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            // Periksa apakah permintaan berhasil (status HTTP 200 OK)
            if ($http_status === 200) {
                // Cetak respons JSON untuk debugging
                // return 'Respons JSON: ' . $response5;

                // Decode JSON respons
                $data = json_decode($response5, true);

                // Periksa apakah decoding JSON berhasil
                if ($data !== null) {
                    // Periksa apakah 'authId' ada dalam respons
                    if (isset($data['tokenId'])) {
                        // Ambil nilai dari properti 'authId'
                        $auth5 = $data['tokenId'];
                        // Gunakan nilai $auth2 sesuai kebutuhan Anda
                        echo 'Nilai authId: ' . $auth5;
                    } else {
                        return 'Error: Key "authId" not found in the response.';
                    }
                } else {
                    return 'Error decoding JSON.';
                }
            } else {
                return 'Error: HTTP Status ' . $http_status;
            }
        }

        // Tutup koneksi cURL
        curl_close($curl);

        // MEMINTA AKSES TOKEN


        // URL GET pertama
        $get_url = 'https://ciam-rajaampat.xl.co.id/am/oauth2/realms/xl/authorize?iPlanetDirectoryPro=' . $auth5 . '&client_id=a80c1af52aae62d1166b73796ae5f378&scope=openid%20profile&response_type=code&redirect_uri=https%3A%2F%2Fmy.xl.co.id&code_challenge=sbc5tSaT5kSDTh8-aWH7ITOkfb1d8CQYjQqacHneHig&code_challenge_method=S256';

        $curl_get = curl_init();

        curl_setopt_array($curl_get, array(
            CURLOPT_URL => $get_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HEADER => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Host: ciam-rajaampat.xl.co.id',
                'accept-api-version: resource=2.1, protocol=1.0',
                'content-type: application/json',
                'Accept: application/json',
                'Cookie: iPlanetDirectoryPro=' . $auth5 . '; route=1709831904.582.19531.628520; amlbcookie=01; __cf_bm=c9tkI1wa9QZMtQd2QR1Z64iRQqVi8ktUiGlK_4tfFZA-1709831903-1.0.1.1-epA0SFVCuCGcTILX9HD0xLAOK6yNTcqNjs_puox_QV0uPh52HdivZoCmX_P9guAZOmdP09GdSJlQ6uRKRlcUlw; dtCookie=v_4_srv_6_sn_9EE4AB5C4275E79DCDA9DE68A635182E_perc_100000_ol_0_mul_1_app-3A923787986dfd1874_0; dtCookie=v_4_srv_6_sn_6F91763E334854242003A085EEDA3504_perc_100000_ol_0_mul_1_app-3A923787986dfd1874_0',
                'accept-encoding: gzip',
                'user-agent: okhttp/4.3.1',
            ),
        ));

        $response_get = curl_exec($curl_get);
        $header_size = curl_getinfo($curl_get, CURLINFO_HEADER_SIZE);
        $header = substr($response_get, 0, $header_size);
        $body = substr($response_get, $header_size);

        curl_close($curl_get);

        // Mendapatkan nilai 'code' dari URL pertama
        $code = null;
        if (preg_match('/\bcode=([^&]+)/', $response_get, $matches)) {
            $code = $matches[1];
        }

        // Mendapatkan nilai 'client_id' dari URL pertama
        $client_id = null;
        if (preg_match('/\bclient_id=([^&]+)/', $get_url, $matches)) {
            $client_id = $matches[1];
        }

        // URL POST kedua
        $post_url = 'https://ciam-rajaampat.xl.co.id/am/oauth2/realms/xl/access_token';

        // Data untuk permintaan POST
        $post_data = array(
            'client_id' => $client_id,
            'code' => $code,
            'redirect_uri' => 'https://my.xl.co.id',
            'grant_type' => 'authorization_code',
            'code_verifier' => '7Cu01QjB1TVJSupAfwfVGp-dl_YlgcrHLbtZx8EIEEkceXrCD90G2PbFtGxdil2oXlx-fJ_alcbFCRCn6CHNkg',
            // tambahkan 'client_secret' jika diperlukan oleh layanan OAuth
        );

        $curl_post = curl_init();

        curl_setopt_array($curl_post, array(
            CURLOPT_URL => $post_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query($post_data),
            CURLOPT_HTTPHEADER => array(
                'Accept-API-Version: resource=2.1, protocol=1.0',
                'Accept-Encoding: gzip',
                'Connection: Keep-Alive',
                'Content-Type: application/x-www-form-urlencoded',
                'Cookie: iPlanetDirectoryPro=' . $auth5 . '; route=1709831904.582.19531.628520; amlbcookie=01; __cf_bm=c9tkI1wa9QZMtQd2QR1Z64iRQqVi8ktUiGlK_4tfFZA-1709831903-1.0.1.1-epA0SFVCuCGcTILX9HD0xLAOK6yNTcqNjs_puox_QV0uPh52HdivZoCmX_P9guAZOmdP09GdSJlQ6uRKRlcUlw; dtCookie=v_4_srv_6_sn_9EE4AB5C4275E79DCDA9DE68A635182E_perc_100000_ol_0_mul_1_app-3A923787986dfd1874_0; dtCookie=v_4_srv_6_sn_6F91763E334854242003A085EEDA3504_perc_100000_ol_0_mul_1_app-3A923787986dfd1874_0',
                'Host: ciam-rajaampat.xl.co.id',
                'User-Agent: okhttp/4.3.1',
                'x-dynatrace: MT_3_8_2198573305_131-0_24d94a15-af8c-49e7-96a0-1ddb48909564_0_278_94',
            ),
        ));
        system('clear');
        // Eksekusi permintaan POST
        $response_post = curl_exec($curl_post);

        // Tutup sesi cURL
        curl_close($curl_post);

        // Decode respons JSON
        $responseArray = json_decode($response_post, true);

        session(['response_json' => json_encode($responseArray)]);

        $reset_time = Carbon::now()->addMinutes(60);
        $data = [
            'msisdn' => $this->number,
            'reset_time' => $reset_time
        ];

        $data_to_write = json_encode(array('msisdn' => $data['msisdn'], 'reset_time' => $data['reset_time']), JSON_PRETTY_PRINT);
        session(['nomor_json' => $data_to_write]);


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.myxl.xlaxiata.co.id/api/v1/auth/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HEADER => false,
            CURLOPT_POSTFIELDS => '{
            "lang": "en",
            "is_enterprise": false,
            "access_token": "' . $responseArray['access_token'] . '"
        }',
            CURLOPT_HTTPHEADER => array(
                'Accept-Encoding: gzip',
                'Authorization: Bearer ' . $responseArray['id_token'] . '',
                'Connection: Keep-Alive',
                'Content-Type: application/json; charset=utf-8',
                'Host: api.myxl.xlaxiata.co.id',
                'User-Agent: myXL / 6.0.1(755); StandAloneInstall; (samsung; SM-N935F; SDK 32; Android 12)',
                'x-api-key: vT8tINqHaOxXbGE7eOWAhA==',
                'x-dynatrace: MT_3_5_4130970026_6-0_24d94a15-af8c-49e7-96a0-1ddb48909564_0_340_140',
                'X-REQUEST-AT: 2024-03-22T10:15:14.58+08:00',
                'X-REQUEST-ID: b4b8a8cb-ee3e-44f7-874f-d23b98b77e04',
                'X-VERSION-APP: 6.0.1'
            ),
        ));

        $response_json = curl_exec($curl);
        curl_close($curl);

        // Simpan respons ke file nomor.json
        $response_data = json_decode($response_json, true);
        if ($response_data && isset($response_data['data']['msisdn'])) {
            $msisdn = $response_data['data']['msisdn'];

            // Set waktu reset 1 jam ke depan
            $reset_time = Carbon::now()->addMinutes(60);

            // Data yang akan disimpan ke dalam nomor.json
            $data_to_write = json_encode(array('msisdn' => $msisdn, 'reset_time' => $reset_time), JSON_PRETTY_PRINT);
            session(['nomor_json' => $data_to_write]);
            return 'Success Login Via Email';
        } else {
            return 'Success Login Via Email';
        }
    }
}
