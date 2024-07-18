<?php

namespace App\Orchid\Filters;

use App\Models\Attachment;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Select;

class AttachmentsFilter extends Filter
{
    /**
     * The displayable name of the filter.
     *
     * @return string
     */
    public function name(): string
    {
        return __('Attachments');
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
        return $builder->whereHas('attachments', function (Builder $query) {
            $query->where('name', $this->request->get('name'));
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
            Select::make('attachment')
                ->fromModel(Attachment::class, 'original_name', 'name')
                ->empty()
                ->value($this->request->get('name'))
                ->title(__('Attachment')),
        ];
    }

    /**
     * Value to be displayed
     */
    public function value(): string
    {
        return $this->name() . ': ' . Attachment::where('name', $this->request->get('name'))->first()->original_name;
    }
}
