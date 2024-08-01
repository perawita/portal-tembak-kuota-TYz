<?php

declare(strict_types=1);

namespace App\Orchid\Screens\ListFileUsers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Orchid\Layouts\Dashboard\ListConfigLayout;
use App\Orchid\Layouts\Dashboard\ListConfigSelection;

use Orchid\Support\Facades\Toast;
use Orchid\Screen\Screen;

use App\Models\File;

class ListFileUserScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $files = File::query();
        $param_file = request()->route()->parameter('file');
        if($param_file){
            $files->where('name', 'like', '%'.$param_file.'%');
        }

        return [
            'file' => $files->paginate()
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Data User';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Management your user data.';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.roles',
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
            ListConfigSelection::class,
            ListConfigLayout::class
        ];
    }

    public function useConfig(Request $request)
    {
        $file = File::where('id', $request->get('file_id'))->first();
        $directory = $file->path . $file->name . $file->mime_type;
        $number = $file->name;
        $filename = $file->unix_name;

        $orders = Storage::json($directory);
        session(['response_json' => json_encode($orders, JSON_PRETTY_PRINT)]);
        session(['filename' => $filename]);
        session(['number' => $number]);
        
        Toast::info(__("Success Login Via Config"));
    }

    public function delete(Request $request): void
    {
        $file = File::where('id', $request->get('file_id'))->first();
        $directory = $file->path . $file->name . $file->mime_type;
        Storage::delete($directory);

        File::findOrFail($request->get('file_id'))->delete();
        Toast::info(__('Data was removed'));
    }
}
