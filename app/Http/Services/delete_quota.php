<?php

namespace App\Http\Services;

use App\Models\Balance;

class delete_quota
{
    // URL endpoints for encryption and decryption APIs
    private $encryptionUrl = 'api.virtualtunneling.tech/api/encrypt';
    private $decryptionUrl = 'api.virtualtunneling.tech/api/decrypt';
    private $apiToken = 'VGVzdFNlY3JldEtleQ==';
    
    private $encryption_headers = 
    [
        'Authorization: Bearer VGVzdFNlY3JldEtleQ==',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, seperti Gecko) Chrome/123.0.0.0 Safari/537.36 Edg/123.0.0.0',
        'Content-Type: application/json; charset=utf-8',
        'Host: api-aink.cybertunneling.com:80',
        'Connection: Keep-Alive'
    ];

    public function __construct($filename)
    {
        $this->refreshToken($filename);
    }
    
    // Fungsi untuk memperbarui token
    private function refreshToken($filename) {
        $jsonData = session('response_json');
        $apiToken = 'VGVzdFNlY3JldEtleQ==';
        $data = json_decode($jsonData, true);
    
        $refreshToken = isset($data['refresh_token']) ? $data['refresh_token'] : '';
    
        $arg2 = $refreshToken;
    
        $url = 'ciam-xl.virtualtunneling.tech:10000/v7/refresh/token';
        $headers = [
            'Content-Type: application/json',
            'X-Auth-Api: ' . $apiToken,
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, seperti Gecko) Chrome/123.0.0.0 Safari/537.36 Edg/123.0.0.0'
        ];
    
        $ch = curl_init($url . '?arg1=' . urlencode($filename) . '&arg2=' . urlencode($arg2));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
        $loginResponse = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        if (curl_errno($ch)) {
            return 'Process cURL error';
        } else {
            $login_response_array = json_decode($loginResponse, true);
    
            if ($login_response_array['status_code'] == 200 && $login_response_array['status'] === true) {
                $filtered_response = $login_response_array['data']['success_resp'];
                session(['response_json' => json_encode($filtered_response, JSON_PRETTY_PRINT)]);
            } else {
                return "Failed Login Via SM";
            }
        }
    
        curl_close($ch);
    }

    // Fungsi untuk memuat konfigurasi dari file JSON
    private function loadConfiguration()
    {
        if (session('response_json')) {
            $response = json_decode(session('response_json'), true);        
            if (!isset($response['id_token'])) {
                echo "Token tidak ditemukan dalam file konfigurasi.\n";
                exit;
            }
            return $response['id_token'];
        } else {
            echo "File konfigurasi tidak ditemukan.\n";
            exit;
        }
    }
    
    // Function to encrypt data using curl
    private function encryptData($data)
    {
        return $this->makeCurlRequest($this->encryptionUrl, $data, $this->encryption_headers);
    }
    
    // Function to decrypt data using curl
    private function decryptData($data)
    {
        return $this->makeCurlRequest($this->decryptionUrl, $data, $this->encryption_headers);
    }
    
    // Function to fetch quota details
    public function getQuotaDetails()
    {
        $token = $this->loadConfiguration();

        // Example data to be encrypted
        $dataToEncrypt = [
            'request_body' => [
                'lang' => 'en',
                'is_enterprise' => true
            ]
        ];
        
        // Encrypt data
        $encryptionResponse = $this->encryptData($dataToEncrypt);
        
        // Check if encryption response is valid
        if (!isset($encryptionResponse['xdata']) || !isset($encryptionResponse['xtime'])) {
            echo "Encryption data not found in response or empty.\n";
            exit;
        }
        
        // Prepare encrypted data
        $encryptedData = [
            'xdata' => $encryptionResponse['xdata'],
            'xtime' => $encryptionResponse['xtime']
        ];
        
        // API endpoint for quota details
        $apiUrl = 'https://api.myxl.xlaxiata.co.id/api/v7/packages/quota-details';
        
        // Headers for API request
        $headers = [
            'authorization: Bearer ' . $token,
            'User-Agent: myXL / 7.1.0(1023); com.android.vending; (Xiaomi; M2006C3LG; SDK 29; Android 10)',
            'X-VERSION-APP: 7.1.0',
            'Content-Type: application/json',
            'x-request-id: ' . uniqid(),
            'x-request-at: ' . date('c'),
            'x-version-app: 6.0.0'
        ];
        
        // Make API request for quota details
        $quotaDetailsResponse = $this->makeCurlRequest($apiUrl, $encryptedData, $headers);
        
        // Check if quota details response is valid
        if (!isset($quotaDetailsResponse['xdata']) || !isset($quotaDetailsResponse['xtime'])) {
            echo "'xdata' or 'xtime' not found in response.\n";
            exit;
        }
        
        // Decrypt quota details response
        $decryptedData = $this->decryptData($quotaDetailsResponse);
        
        // Check if decrypted data is valid
        if (isset($decryptedData['decryptedData'])) {
            $decryptedData = json_decode($decryptedData['decryptedData'], true);
            if (isset($decryptedData['data']['quotas'])) {
                return json_encode($decryptedData['data']['quotas']);
            } else {
                echo "Quotas data not found in response.\n";
                exit;
            }
        } else {
            echo "Decrypted data not found in response.\n";
            exit;
        }
    }
    
    // Function to handle unsubscribe package (example)
    public function unsubscribePackage($quotaCode)
    {
        $unsubscribe_data = [
            "lang" => "en",
            "is_enterprise" => false,
            "product_domain" => "VOLUME",
            "product_subscription_type" => "REC",
            "quota_code" => $quotaCode,
            "unsubscribe_reason_code" => ""
        ];
        
        
        $token = $this->loadConfiguration();
        $headers = [
            'authorization: Bearer ' . $token,
            'User-Agent: myXL / 7.1.0(1023); com.android.vending; (Xiaomi; M2006C3LG; SDK 29; Android 10)',
            'X-VERSION-APP: 7.1.0',
            'Content-Type: application/json',
            'x-request-id: ' . uniqid(),
            'x-request-at: ' . date('c'),
            'x-version-app: 6.0.0'
        ];
        
                // Inisialisasi Curl untuk permintaan enkripsi data berhenti langganan
                $ch_encrypt_unsubscribe = curl_init();
                curl_setopt_array($ch_encrypt_unsubscribe, [
                    CURLOPT_URL => $this->encryptionUrl,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => json_encode($unsubscribe_data),
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => $this->encryption_headers,
  
                ]);

                // Eksekusi permintaan enkripsi data berhenti langganan
                $response_encrypt_unsubscribe = curl_exec($ch_encrypt_unsubscribe);
                if ($response_encrypt_unsubscribe === false) {
                    echo "Error saat melakukan permintaan enkripsi: " . curl_error($ch_encrypt_unsubscribe);
                    curl_close($ch_encrypt_unsubscribe);
                    exit;
                }
                curl_close($ch_encrypt_unsubscribe);

                // Dekode respons JSON dari permintaan enkripsi data berhenti langganan
                $response_encrypt_unsubscribe = json_decode($response_encrypt_unsubscribe, true);

                // Pastikan respons enkripsi berhasil dan ambil data terenkripsi
                if (!isset($response_encrypt_unsubscribe['xdata']) || !isset($response_encrypt_unsubscribe['xtime'])) {
                    echo "Error: Data enkripsi tidak ditemukan dalam respons atau kosong.\n";
                    exit;
                }
                $unsubscribe_xdata = $response_encrypt_unsubscribe['xdata'];
                $unsubscribe_xtime = $response_encrypt_unsubscribe['xtime'];

                // URL endpoint API untuk berhenti langganan
                $unsubscribe_url = 'https://api.myxl.xlaxiata.co.id/api/v7/packages/unsubscribe';

                // Payload yang akan dikirim untuk berhenti langganan
                $unsubscribe_encrypted_data = [
                    'xdata' => $unsubscribe_xdata,
                    'xtime' => $unsubscribe_xtime
                ];

                // Inisialisasi Curl untuk permintaan berhenti langganan
                $ch_unsubscribe = curl_init();
                curl_setopt_array($ch_unsubscribe, [
                    CURLOPT_URL => $unsubscribe_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '', // Enable compression
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($unsubscribe_encrypted_data), // Menggunakan data terenkripsi sebagai payload
                    CURLOPT_HTTPHEADER => $headers,
                ]);

                // Eksekusi permintaan berhenti langganan
                $unsubscribe_response = curl_exec($ch_unsubscribe);
                if (curl_errno($ch_unsubscribe)) {
                    echo 'Error:' . curl_error($ch_unsubscribe) . "\n";
                    curl_close($ch_unsubscribe);
                    exit;
                }
                curl_close($ch_unsubscribe);

                // Dekode respons dari API berhenti langganan
                $unsubscribe_response_data = json_decode($unsubscribe_response, true);

                // Mempersiapkan payload untuk permintaan dekripsi hasil berhenti langganan
                $payload_decrypt_unsubscribe = [
                    'xdata' => $unsubscribe_response_data['xdata'],
                    'xtime' => $unsubscribe_response_data['xtime']
                ];

                // Inisialisasi Curl untuk permintaan dekripsi hasil berhenti langganan
                $ch_decrypt_unsubscribe = curl_init();
                curl_setopt_array($ch_decrypt_unsubscribe, [
                    CURLOPT_URL => $this->decryptionUrl,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => json_encode($payload_decrypt_unsubscribe),
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => $this->encryption_headers,
                ]);

                // Eksekusi permintaan dekripsi hasil berhenti langganan
                $response_decrypt_unsubscribe = curl_exec($ch_decrypt_unsubscribe);
                echo "$response_decrypt_unsubscribe";
                if ($response_decrypt_unsubscribe === false) {
                    echo "Error saat melakukan permintaan dekripsi: " . curl_error($ch_decrypt_unsubscribe);
                    curl_close($ch_decrypt_unsubscribe);
                    exit;
                }
                curl_close($ch_decrypt_unsubscribe);

                // Dekode respons JSON dari permintaan dekripsi hasil berhenti langganan
                $response_decrypt_unsubscribe = json_decode($response_decrypt_unsubscribe, true);

                // Tampilkan hasil dekripsi dari respons berhenti langganan
                if (isset($response_decrypt_unsubscribe['decryptedData'])) {
                    $decrypted_unsubscribe_data = json_decode($response_decrypt_unsubscribe['decryptedData'], true);
                    if (isset($decrypted_unsubscribe_data['data']['unsubscribed_quota_code'])) {
                        $user = Balance::where('user_id', auth()->id())->first();
                        $user->amount -= 5000; //ganti 5000 dengan nilai yang akan digunakan
                        $user->save();
                        return "Paket berhasil dihapus: {$decrypted_unsubscribe_data['data']['unsubscribed_quota_code']}";
                    } else {
                        return "Data 'unsubscribed_quota_code' tidak ditemukan dalam hasil dekripsi.";
                    }
                    
                } else {
                    echo "Data kuota tidak ditemukan dalam respons.\n";
                }
    }
    
    // Function to make a curl request
    private function makeCurlRequest($url, $data, $headers)
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '', // Enable compression
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $headers,
        ]);
        
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch) . "\n";
            curl_close($ch);
            exit;
        }
        
        curl_close($ch);
        
        return json_decode($response, true);
    }
}
