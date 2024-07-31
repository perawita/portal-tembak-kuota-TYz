<?php

declare(strict_types=1);

namespace App\Orchid\Screens\PageController;


use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;



use App\Models\MaintenanceSetting;

use App\Orchid\Layouts\PageController\PageListLayout;

class PageListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'maintenance' => MaintenanceSetting::get()
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Pages Controllers';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Setting your page when to maintenance.';
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
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Add'))
                ->icon('bs.plus-circle')
                ->route('platform.page-controller.create-page'),
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
            PageListLayout::class,
        ];
    }
}
