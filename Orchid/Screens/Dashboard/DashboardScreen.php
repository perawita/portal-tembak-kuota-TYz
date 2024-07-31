<?php

namespace App\Orchid\Screens\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Fields\Group;
use Orchid\Support\Color;


use App\Orchid\Layouts\InternetQuotaList\InternetQuotaListLayout;
use App\Orchid\Layouts\InternetQuotaList\InternetQuotaSelection;

use App\Models\Balance;
use App\Models\Quota;


use App\Http\Services\xlv2;
use App\Http\Services\em;
use App\Http\Services\session;

class DashboardScreen extends Screen
{

    public $number_otp = null;
    public $email = null;
    public $option;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): array
    {
        $balance = Balance::where('user_id', auth()->id())->first();
        // Ambil data dari session
        $data = session('response_json');

        $this->option = session('option') ?? false;

        // Mendekodekan data JSON menjadi array asosiatif
        $decoded_data = json_decode($data, true);


        $now = Carbon::now();
        $expiry_time = Carbon::createFromTimestamp($decoded_data['expires_in'] ?? 0);
        $expiry_diff_minutes = $now->diffInMinutes($expiry_time);
        $remainingMinutes = session('response_json') ? $decoded_data['expires_in'] / 60 : 0;


        $param = request()->route()->parameter('quota');

        $query = Quota::with('attachment')
            ->where('status', 'like', '%public%');

        // Jika parameter 'quota' ada, tambahkan kondisi where
        if ($param) {
            $query->where('name', 'like', $param);
        }

        return [
            'balance' => $balance ? $this->konversiMataUang((int)$balance->amount) : 0,
            'expired' => $remainingMinutes > 0 ? sprintf('%02d', $remainingMinutes) . ' minute' : 'Login expired',
            'number' => $remainingMinutes > 0 ? session('number') : 'Please enter your number',
            'quota' => $query->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Dashboards';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'See some details of your activities';
    }

    /**
     * The screen's layout elements.
     *
     * @return string[]|\Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            Layout::metrics([
                'Phone Number' => 'number',
                'Expired' => 'expired',
                'Saldo' => 'balance',
            ]),

            Layout::rows([
                Group::make([
                    Button::make('Login via SMS')
                        ->method('setOption')
                        ->parameters(['option' => false])
                        ->type(Color::BASIC),
                    // Button::make('Login via Email')
                    //     ->method('setOption')
                    //     ->parameters(['option' => true])
                    //     ->type(Color::BASIC),
                ])->autoWidth(),
            ]),

            $this->option === false ?
                //via sms false
                Layout::rows([
                    Group::make([
                        Input::make('phone')
                            ->type('tel')
                            ->title('Phone')
                            ->value($this->number_otp)
                            ->placeholder('Enter phone number')
                            ->horizontal()
                            ->popover('The device’s autocomplete mechanisms kick in and suggest
                                        phone numbers that can be autofilled with a single tap.')
                            ->help('Enter your phone number.'),

                        Input::make('code_otp')
                            ->title('Code OTP')
                            ->placeholder('Enter code otp')
                            ->help('Please enter your code otp')
                            ->horizontal(),
                    ]),


                    Button::make($this->number_otp ? 'Submit' : 'Request OTP')
                        ->method('request_code_otp_sms')
                        ->type(Color::BASIC),
                ])

                :

                //via email true
                Layout::rows([
                    Group::make([
                        Input::make('phone')
                            ->type('tel')
                            ->title('Phone')
                            ->value($this->number_otp)
                            ->placeholder('Enter phone number')
                            ->horizontal()
                            ->popover('The device’s autocomplete mechanisms kick in and suggest
                        phone numbers that can be autofilled with a single tap.')
                            ->help('Enter your phone number.'),

                        Input::make('email')
                            ->type('email')
                            ->title('Email')
                            ->value($this->email)
                            ->placeholder('Enter you email')
                            ->horizontal()
                            ->popover('The device’s autocomplete mechanisms kick in and suggest
                        you email that can be autofilled with a single tap.')
                            ->help('Enter your you email.'),

                        Input::make('code_otp')
                            ->title('Code OTP')
                            ->placeholder('Enter code otp')
                            ->help('Please enter your code otp')
                            ->horizontal(),
                    ]),


                    Button::make($this->number_otp ? 'Submit' : 'Request OTP')
                        ->method('request_code_otp_email')
                        ->type(Color::BASIC),
                ]),

            InternetQuotaSelection::class,
            InternetQuotaListLayout::class,

        ];
    }

    /**
     * Method for requesting OTP via SMS.
     */
    public function request_code_otp_sms(Request $request)
    {
        $request->validate([
            'phone' => 'required',
        ]);

        $phone = $request->input('phone');
        $code_otp = strtoupper($request->input('code_otp'));

        $service = new xlv2($phone);

        if ($phone && !$code_otp) {
            $authenticate = $service->authenticate();
            $message = $authenticate ? __('Otp dikirim ke sms, silahkan cek') : __('Gagal mengirim otp ke sms, silahkan cek nomor anda');
            $authenticate ? $this->number_otp = $phone : $this->number_otp = null;
            Toast::info($message);
        } else if ($phone && $code_otp) {
            $message = $service->processValidatiOtp($code_otp, session('filename'));
            $this->number_otp = null;
            Toast::info(__($message));
        }
    }


    /**
     * Method for requesting OTP via Email.
     */
    public function request_code_otp_email(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'email' => 'required|email',
        ]);

        $phone = $request->input('phone');
        $email = $request->input('email');
        $code_otp = strtoupper($request->input('code_otp'));

        $service = new em($phone, $email);

        if ($phone && $email && !$code_otp) {
            $authenticate = $service->authenticate();
            $message = $authenticate ? __('Otp dikirim ke email, silahkan cek') : __('Gagal mengirim otp ke email, silahkan cek nomor anda');
            $authenticate ? $this->number_otp = $phone : $this->number_otp = null;
            $this->email = $email;
            Toast::info($message);
        } else if ($phone && $email && $code_otp) {
            $message = $service->processValidatiOtp($code_otp, session('auth'));
            $this->number_otp = null;
            $this->email = null;
            Toast::info(__($message));
        }
    }


    /**
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleBuy(Request $request)
    {
        $quota = Quota::findOrFail($request->get('quota_id'));
        $user = Balance::where('user_id', auth()->id())->first();

        if ((int)$quota->price <= (int)$user->amount) {
            return redirect()->route('platform.internet-quota-list.buy', Crypt::encryptString($request->get('quota_id')));
        } else {
            Toast::error(__('Your balance amount is less than the amount to be paid, your balance amount is ' . $user->amount));
            return redirect()->route('platform.internet-quota-list');
        }
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



    /**
     * Set the option for login method.
     *
     * @param bool $option
     */
    public function setOption(bool $option)
    {
        $this->option = $option;

        session(['option' => $option]);
    }
}
