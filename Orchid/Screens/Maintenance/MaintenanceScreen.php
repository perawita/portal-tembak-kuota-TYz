<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Maintenance;

use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class MaintenanceScreen extends Screen
{

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'information' => 'This page is in the maintenance period please come back in a while, if the page still has problems again please contact the admin.',
        ];
    }
    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Error Pages';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Ooopssss............';
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
                'Information: ' => 'information',
            ]),
        ];
    }
}
