<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class xlv2
{
    protected $number;

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
        // Request pertama
        $response1 = Http::withHeaders([
            'accept-api-version' => 'resource=2.1, protocol=1.0',
            'accept-encoding' => 'gzip',
            'user-agent' => 'okhttp/4.3.1'
        ])->post('https://ciam-rajaampat.xl.co.id/am/json/realms/xl/authenticate?authIndexType=service&authIndexValue=otp');

        if ($response1->status() === 200) {
            $data = $response1->json();

            if ($data !== null && isset($data['authId'])) {
                $auth = $data['authId'];
            } else {
                return null;
            }
        } else {
            return null;
        }

        // Request kedua
        $response2 = Http::withHeaders([
            'accept-api-version' => 'resource=2.1, protocol=1.0',
            'content-type' => 'application/json',
            'Accept' => 'application/json',
            'accept-encoding' => 'gzip',
            'user-agent' => 'okhttp/4.3.1'
        ])->post('https://ciam-rajaampat.xl.co.id/am/json/realms/xl/authenticate', [
            'authId' => $auth,
            'stage' => 'MSISDN',
            'callbacks' => [
                [
                    'type' => 'MetadataCallback',
                    'output' => [
                        ['name' => 'data', 'value' => ['stage' => 'MSISDN']]
                    ],
                    '_id' => 0
                ],
                [
                    'type' => 'NameCallback',
                    'output' => [
                        ['name' => 'prompt', 'value' => 'MSISDN']
                    ],
                    'input' => [
                        ['name' => 'IDToken2', 'value' => $this->number]
                    ],
                    '_id' => 1
                ],
                [
                    'type' => 'HiddenValueCallback',
                    'output' => [
                        ['name' => 'value', 'value' => ''],
                        ['name' => 'id', 'value' => 'Language']
                    ],
                    'input' => [
                        ['name' => 'IDToken3', 'value' => 'MYXLU_AND_LOGIN_EN']
                    ],
                    '_id' => 2
                ]
            ]
        ]);

        if ($response2->status() === 200) {
            $data = $response2->json();

            if ($data !== null && isset($data['authId']) && isset($data['callbacks'])) {
                $auth2 = $data['authId'];
                $input = $data['callbacks'];
            } else {
                return null;
            }
        } else {
            return null;
        }

        // Request ketiga
        $response3 = Http::withHeaders([
            'accept-api-version' => 'resource=2.1, protocol=1.0',
            'content-type' => 'application/json',
            'Accept' => 'application/json',
            'accept-encoding' => 'gzip',
            'user-agent' => 'okhttp/4.3.1'
        ])->post('https://ciam-rajaampat.xl.co.id/am/json/realms/xl/authenticate', [
            'authId' => $auth2,
            'stage' => 'DEVICE',
            'callbacks' => [
                [
                    'type' => 'MetadataCallback',
                    'output' => [
                        ['name' => 'data', 'value' => ['stage' => 'DEVICE']]
                    ],
                    '_id' => 4
                ],
                [
                    'type' => 'HiddenValueCallback',
                    'output' => [
                        ['name' => 'value', 'value' => 'Input Device Information'],
                        ['name' => 'id', 'value' => 'DeviceInformation']
                    ],
                    'input' => [
                        ['name' => 'IDToken2', 'value' => '378a0cfdf4486033-e2cda016dd977db14ebd5e27903b3f4f760c8a02']
                    ],
                    '_id' => 5
                ]
            ]
        ]);

        if ($response3->status() === 200) {
            $data = $response3->json();

            if ($data !== null && isset($data['authId']) && isset($data['callbacks'])) {
                $auth3 = $data['authId'];
                $input1 = $data['callbacks'];
            } else {
                return null;
            }
        } else {
            return null;
        }

        // Request keempat
        $response4 = Http::withHeaders([
            'accept-api-version' => 'resource=2.1, protocol=1.0',
            'content-type' => 'application/json',
            'Accept' => 'application/json',
            'accept-encoding' => 'gzip',
            'user-agent' => 'okhttp/4.3.1'
        ])->post('https://ciam-rajaampat.xl.co.id/am/json/realms/xl/authenticate', [
            'authId' => $auth3,
            'stage' => 'VALIDATE',
            'callbacks' => [
                [
                    'type' => 'MetadataCallback',
                    'output' => [
                        ['name' => 'data', 'value' => ['stage' => 'VALIDATE']]
                    ],
                    '_id' => 7
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
                    'input' => [
                        ['name' => 'IDToken2', 'value' => 1]
                    ],
                    '_id' => 8
                ],
                [
                    'type' => 'TextOutputCallback',
                    'output' => [
                        ['name' => 'message', 'value' => json_encode($input1)],
                        ['name' => 'messageType', 'value' => '0']
                    ],
                    '_id' => 9
                ]
            ]
        ]);

        if (
            $response4->status() === 200
        ) {
            $data = $response4->json();

            if ($data !== null && isset($data['authId']) && isset($data['callbacks'])) {
                $auth4 = $data['authId'];
                session(['auth' => $auth4]);
                return ['auth' => $auth4, 'callbacks' => $data['callbacks']];
            } else {
                return null;
            }
        } else {
            return null;
        }
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
        $get_url = 'https://ciam-rajaampat.xl.co.id/am/oauth2/realms/xl/authorize?iPlanetDirectoryPro=' . $auth5 . '&client_id=a80c1af52aae62d1166b73796ae5f378&scope=openid%20profile&response_type=code&redirect_uri=https%3A%2F%2Fmy.xl.co.id&code_challenge=5ZHJ9OiE0d4rvSRsx6O1h31sENMt7KFGyc2-wQR0AKM&code_challenge_method=S256';

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
            'code_verifier' => 'NCcgKSkJieN_NpmeikxitrJlPviAtOhkm6-lDp0HsPJqmnWkjhtYuQrcoAN1Js0fgbWnqf6UY7n9Rfh5SeHSWw',
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
        //return $response_post;
        // Decode respons JSON
        $responseArray = json_decode($response_post, true);

        // Mengambil nilai-nilai yang diinginkan
        $access_token = $responseArray['access_token'];
        $refresh_token = $responseArray['refresh_token'];
        $scope = $responseArray['scope'];
        $id_token = $responseArray['id_token'];

        $data_to_writee = json_encode(array(
            'access_token' => $access_token,
            'refresh_token' => $refresh_token,
            'scope' => $scope,
            'id_token' => $id_token
        ), JSON_PRETTY_PRINT);

        session(['response_json' => $data_to_writee]);



        // DASHBOARD

        $encryption_url = 'api-aink.cybertunneling.com/api/encrypt';
        // URL endpoint untuk API dekripsi
        $decryption_url = 'api-aink.cybertunneling.com/api/decrypt';
        // Header untuk permintaan enkripsi
        $encryption_headers = [
            'Authorization: Bearer VGVzdFNlY3JldEtleQ==',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, seperti Gecko) Chrome/123.0.0.0 Safari/537.36 Edg/123.0.0.0',
            'Content-Type: application/json; charset=utf-8',
            'Host: api-aink.cybertunneling.com',
            'Connection: Keep-Alive'
        ];
        $decryption_headers = [
            'Authorization: Bearer VGVzdFNlY3JldEtleQ==',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, seperti Gecko) Chrome/123.0.0.0 Safari/537.36 Edg/123.0.0.0',
            'Content-Type: application/json; charset=utf-8',
            'Host: apigw.kmsp-store.com',
            'Connection: Keep-Alive'
        ];

        $data_to_encrypt3 = [
            'lang' => 'en',
            'is_enterprise' => false,
            'access_token' => $access_token

        ];

        // Inisialisasi Curl untuk permintaan enkripsi
        $ch_encrypt = curl_init();
        curl_setopt_array($ch_encrypt, [
            CURLOPT_URL => $encryption_url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data_to_encrypt3),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $encryption_headers,
        ]);

        // Eksekusi permintaan enkripsi
        $response_encrypt = curl_exec($ch_encrypt);

        if ($response_encrypt === false) {
            $error_encrypt = curl_error($ch_encrypt);
            echo "Error saat melakukan permintaan enkripsi: " . $error_encrypt . "\n";
            curl_close($ch_encrypt);
            exit;
        }

        curl_close($ch_encrypt);

        // Dekode respons JSON dari permintaan enkripsi
        $response_encrypt = json_decode($response_encrypt, true);
        //echo "Response from encryption API: \n";

        if (!isset($response_encrypt['xdata']) || !isset($response_encrypt['xtime'])) {
            echo "Error: Data enkripsi tidak ditemukan dalam respons atau kosong.\n";
        }
        $xdata = $response_encrypt['xdata'];
        $xtime = $response_encrypt['xtime'];

        // URL endpoint API utama
        $api_url = 'https://api.myxl.xlaxiata.co.id/api/v1/auth/login';

        // Header untuk permintaan ke API utama
        $headers = [
            'Accept-Encoding: gzip',
            'Authorization: Bearer ' . $id_token . '',
            'Connection: Keep-Alive',
            // 'Content-Length: 382',
            'Content-Type: application/json; charset=utf-8',
            'Host: api.myxl.xlaxiata.co.id',
            'User-Agent: myXL / 6.3.0(797); com.android.vending; (samsung; SM-F731; SDK 32; Android 12)',
            'x-api-key: vT8tINqHaOxXbGE7eOWAhA==',
            'x-dynatrace: MT_3_5_2312511378_5-0_24d94a15-af8c-49e7-96a0-1ddb48909564_84_2_521',
            'X-REQUEST-AT: 2024-03-09T14:26:35.70+08:00',
            'X-REQUEST-ID: 071c09c4-d05f-4879-a537-be22de90bca6',
            'X-VERSION-APP: 6.3.0'
        ];
        // Payload yang akan dikirim ke API utama
        $encrypted_data = [
            'xdata' => $xdata,
            'xtime' => $xtime
        ];
        // Inisialisasi cURL untuk permintaan ke API utama
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($encrypted_data),
            CURLOPT_HTTPHEADER => $headers,
        ]);

        // Eksekusi permintaan ke API utama
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch) . "\n";
            curl_close($ch);
            exit;
        }

        curl_close($ch);
        //echo "Response from main API: \n";


        // Dekode respons dari API utama
        $response_data = json_decode($response, true);

        if ($response_data && isset($response_data['data']['msisdn'])) {
            $msisdn = $response_data['data']['msisdn'];

            // Set waktu reset 1 jam ke depan
            $reset_time = Carbon::now()->addMinutes(60);

            // Data yang akan disimpan ke dalam nomor.json
            $data_to_write = json_encode(array('msisdn' => $msisdn, 'reset_time' => $reset_time), JSON_PRETTY_PRINT);
            session(['nomor_json' => $data_to_write]);

            return 'Success Login Via Email';
        }
    }
}
