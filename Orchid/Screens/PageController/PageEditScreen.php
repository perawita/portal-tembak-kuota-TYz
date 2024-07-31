<?php

declare(strict_types=1);

namespace App\Orchid\Screens\PageController;

use Illuminate\Http\Request;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;


use Orchid\Platform\Models\Role;
use App\Models\MaintenanceSetting;


use App\Orchid\Layouts\PageController\PageEditLayout;

class PageEditScreen extends Screen
{
    /**
     * @var Maintenance
     */
    public $maintenance;

    /**
     * Fetch data to be displayed on the screen.
     *
     *
     * @return array
     */
    public function query(MaintenanceSetting $maintenance): iterable
    {
        return [
            'maintenance'       => $maintenance,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Page Controllers';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Update or add pages that you want to control.';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.users',
        ];
    }

    /**
     * The screen's action buttons.
     *
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(__('Save'))
                ->icon('bs.check-circle')
                ->method('save'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return string[]|\Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            Layout::block([
                PageEditLayout::class,
            ])
                ->title('Maintenance')
                ->description('Add your page to set when it should be Maintenance or not, after creating please add Middleware on the route to the page to be added.'),
        ];
    }


    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MaintenanceSetting  $maintenance
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Request $request, MaintenanceSetting $maintenance)
    {
        $requestData = $request->input('maintenance');

        // Validasi input
        $request->validate([
            'maintenance.page_name' => 'required',
            'maintenance.is_open' => 'required|boolean',
        ]);

        // Jika ada id yang diterima melalui route
        if ($request->route('id')) {
            $maintenance = $maintenance->findOrFail($request->route('id'));

            // Update data maintenance
            $maintenance->update([
                'page_name' => $requestData['page_name'],
                'is_open' => $requestData['is_open'],
            ]);
        } else {
            // Isi model dengan data maintenance
            $maintenance->fill($requestData);
            // Simpan data maintenance baru
            $maintenance->save();
        }

        // Tampilkan pesan sukses
        Toast::info(__('Page was saved.'));

        // Redirect ke halaman yang sesuai
        return redirect()->route('platform.page-controller');
    }
}
