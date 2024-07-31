<?php

namespace App\Orchid\Filters;

use App\Models\File;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Select;

class FileFilter extends Filter
{
    /**
     * The displayable name of the filter.
     *
     * @return string
     */
    public function name(): string
    {
        return __('file');
    }

    /**
     * The array of matched parameters.
     *
     * @return array|null
     */
    public function parameters(): ?array
    {
        return ['name'];
    }

    /**
     * Apply to a given Eloquent query builder.
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function run(Builder $builder): Builder
    {
        return $builder->whereHas('file', function (Builder $query) {
            $query->where('name', $this->request->get('file'));
        });
    }

    /**
     * Get the display fields.
     *
     * @return Field[]
     */
    public function display(): iterable
    {
        return [
            Select::make('file')
                ->fromModel(file::class, 'name', 'name')
                ->empty()
                ->value($this->request->get('name'))
                ->title(__('File')),
        ];
    }

    /**
     * Value to be displayed
     */
    public function value(): string
    {
        return $this->name() . ': ' . File::where('name', $this->request->get('file'))->first()->name;
    }
}
