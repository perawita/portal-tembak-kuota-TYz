<?php

declare(strict_types=1);

namespace App\Orchid\Screens\UserBalance;

use Illuminate\Http\Request;

use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

use App\Orchid\Layouts\UserBalance\UserBalanceEditLayout;


use App\Models\Balance;

class UserBalanceEditScreen extends Screen
{
    /**
     * @var Balance
     */
    public $Balance;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Balance $Balance): iterable
    {
        $Balance->load(['user']);

        return [
            'Balance'       => $Balance,
        ];
    }


    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Create your product';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Create and publish your products.';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.users',
        ];
    }

    /**
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            Layout::block(UserBalanceEditLayout::class)
                ->title(__('Information Product'))
                ->description(__('Add a price name and some details about your product.'))
                ->commands(
                    Button::make(__('Save'))
                        ->type(Color::BASIC)
                        ->icon('bs.check-circle')
                        ->method('save')
                ),

        ];
    }

    /**
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Request $request)
    {
        // Ambil user_id dari request
        $userIds = $request->input('user-balance.user_id');
        $amount = $request->input('user-balance.amount');

        // Loop melalui setiap user_id
        foreach ($userIds as $userId) {
            // Temukan objek Balance berdasarkan user_id
            $balance = Balance::where('user_id', $userId)->first();

            // Periksa apakah objek Balance ditemukan
            if ($balance) {
                // Jika ditemukan, perbarui data
                $balance->amount += $amount;
                $balance->setAddedBy(auth()->id());
                $balance->setLastTopUp(now());
                $balance->save();

                // Berikan pesan sukses
                Toast::info(__('Data berhasil diperbarui.'));
            } else {
                // Jika tidak ditemukan, tambahkan data baru
                $newBalance = new Balance();
                $newBalance->user_id = $userId;
                $newBalance->amount = $amount;
                $newBalance->setAddedBy(auth()->id());
                $newBalance->setLastTopUp(now());
                $newBalance->save();

                // Berikan pesan sukses
                Toast::info(__('Data berhasil disimpan.'));
            }
        }

        return redirect()->route('platform.user-balance');
    }

}
