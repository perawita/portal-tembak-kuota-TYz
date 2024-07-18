<?php

declare(strict_types=1);

namespace App\Orchid\Screens\InternetQuotaList;


use Illuminate\Http\Request;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

use Orchid\Screen\Fields\RadioButtons;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Orchid\Support\Color;

use App\Models\Balance;
use App\Models\Quota;

class InternetQuotaDetailScreen extends Screen
{
    protected $detail_quota = null;
    protected $payments = false;
    protected $path = null;
    protected $methodPay = null;
    protected $nomor = null;

    public function query(): array
    {
        $this->detail_quota = Quota::with('attachment')
            ->where('id', Crypt::decryptString(request()->route()->parameter('encryptedId')))
            ->first();

        $balance = Balance::where('user_id', auth()->id())->first();
        $this->path = $this->detail_quota->attachment->disk . '/' . $this->detail_quota->attachment->path . $this->detail_quota->attachment->name . '.' . $this->detail_quota->attachment->extension;

        $this->nomor = session('nomor_json') ?? null;

        $this->payments = session('payment') ? true : false;
        $this->methodPay = session('methodPay') ? session('methodPay') : 'Dana';

        if (empty($this->nomor)) {
            $this->handleWindows();
        } else {
            $decoded_data = json_decode($this->nomor, true);
        }

        $now = Carbon::now();
        $expiry_diff = $now->diffInMinutes($decoded_data['reset_time'] ?? null);
        $remainingMinutes = $expiry_diff % 60;

        return [
            'name' => $this->detail_quota ? $this->detail_quota->name : '',
            'id' => $this->detail_quota ? Crypt::decryptString(request()->route()->parameter('encryptedId')) : 0,
            'information' => 'It is strictly forbidden to refresh the page before the transaction is completed because it can cause your balance to be deducted.',
            'expired' => $remainingMinutes > 0 ? sprintf('%02d', $remainingMinutes) . ' minute' : 'Login expired',
            'number' => $remainingMinutes > 0 ? $decoded_data['msisdn'] : 'Please enter your number',
            'path' => $this->path,
            'status' => $this->payments ? 'Payment confirmed' : 'Waiting for payment',
            'methodPay' => $this->methodPay,
            'balance' => $balance ? $this->konversiMataUang((int)$balance->amount) : 0,
            'description' => $this->detail_quota->description ? $this->detail_quota->description : '',
        ];
    }

    public function name(): ?string
    {
        return 'Detail Quota Internet';
    }

    public function description(): ?string
    {
        return 'Quota internet is cheap and easy to use.';
    }


    public function layout(): array
    {
        if (empty($this->nomor)) {
            $this->handleWindows();
        } else {
            $decoded_data = json_decode($this->nomor, true);
        }

        $now = Carbon::now();
        $expiry_diff = $now->diffInMinutes($decoded_data['reset_time'] ?? null);
        $remainingMinutes = $expiry_diff % 60;

        $button = Button::make('Pay')
            ->disabled($remainingMinutes > 0 ? false : true)
            ->method('deductBalance')
            ->type(Color::BASIC);

        $paymentLayout = $this->payments === false ?
            Layout::rows([
                RadioButtons::make('paymentsMethod')
                    ->options([
                        1 => 'Dana',
                        2 => 'Gopay',
                        3 => 'Balance',
                    ])
                    ->help('Choose to make advanced payments'),
                $button,
            ]) :
            Layout::view('payment', [
                'path' => $this->path,
                'payment' => $this->methodPay,
                'detail_quota' => $this->detail_quota
            ]);

        return [
            Layout::metrics([
                'Destination phone number' => 'number',
                'Expired' => 'expired',
                'Quota Name' => 'name',
            ]),

            Layout::metrics([
                'Warning: ' => 'information',
            ]),

            Layout::metrics([
                'Status Payments' => 'status',
                'Payment methods' => 'methodPay',
                'Saldo' => 'balance',
            ]),

            Layout::metrics([
                'Description: ' => 'description',
            ]),

            Layout::columns([
                $paymentLayout,
            ]),
        ];
    }


    public function deductBalance(Request $request)
    {
        $quota = Quota::where('id', Crypt::decryptString(request()->route()->parameter('encryptedId')))->first();
        $user = Balance::where('user_id', auth()->id())->first();

        if ((int)$quota->price <= (int)$user->amount) {

            $methodPay = $request->input('paymentsMethod');

            if ($methodPay === '1') {
                session(['methodPay' => 'Dana']);
            } elseif ($methodPay === '2') {
                session(['methodPay' => 'Gopay']);
            } elseif ($methodPay === '3') {
                session(['methodPay' => 'Balance']);
            } else {
                session(['methodPay' => null]);
            }

            session(['payment' => true]);
            // Toast::info('Your balance has been deducted for purchasing a quota.');
        } else {
            Toast::warning(
                __('Your balance amount is less than the amount to be paid, your balance amount is ' . $user->amount)
            );
        }
    }

    public function handleWindows()
    {
        Toast::error(__('Please login for continue this pages'));
        return redirect()->route('platform.internet-quota-list');
    }


    /**
     * Fungsi untuk mengonversi nilai angka menjadi format yang lebih mudah dibaca.
     *
     * @param float $nilai Nilai angka yang akan dikonversi
     * @return string Nilai angka dalam format yang lebih mudah dibaca
     */
    function konversiMataUang($nilai)
    {
        $huruf = ['', 'k', 'M', 'B', 'T', 'Q'];
        $step = 1000;
        $log = floor(log($nilai, $step));
        $nilaiBaru = $nilai / pow($step, $log);
        $nilaiBaruFormatted = number_format($nilaiBaru, 2);
        return $nilaiBaruFormatted . ' ' . $huruf[$log];
    }
}
