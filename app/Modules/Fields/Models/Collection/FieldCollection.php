<?php

namespace App\Modules\Fields\Models\Collection;

use Nova\Http\Request;
use Nova\Database\ORM\Collection as BaseCollection;
use Nova\Validation\Validator;
use Nova\Support\Facades\View;
use Nova\Support\Arr;

use App\Modules\Fields\Models\MetaData as MetaItem;
use App\Modules\Fields\Models\Collection\MetaCollection;
use App\Modules\Fields\Fields\BooleanField;


class FieldCollection extends BaseCollection
{

    public function updateValidator(Validator $validator)
    {
        $attributes = array();

        foreach ($this->items as $field) {
            if (($field->hidden === 1) || empty($field->validate)) {
                continue;
            }

            $validator->mergeRules($key = $field->key, $field->validate);

            $attributes[$key] = $field->name;
        }

        $validator->setAttributeNames($attributes);
    }

    public function updateMeta(MetaCollection $items, array $input = array())
    {
        foreach ($this->items as $field) {
            if (($field->hidden === 1) || ! Arr::has($input, $key = $field->key)) {
                continue;
            }

            // We have a valid field.
            else if ($field->type == BooleanField::class) {
                $value = (int) Arr::has($input, $key);
            } else {
                $value = Arr::get($input, $key);
            }

            $items->updateOrAdd($key, $value, $field->type);
        }
    }

    public function renderForEditor(Request $request, MetaCollection $items = null)
    {
        if (is_null($items)) {
            $items = with(new MetaItem())->newCollection();
        }

        return $this->where('hidden', 0)->map(function ($field) use ($request, $items)
        {
            if (! is_null($key = $items->findItem($field->key))) {
                $item = $items->get($key);

                $value = $item->value;

                $type = $item->getTypeInstance();
            } else {
                $value = null;

                //
                $className = $field->type;

                $type = new $className();
            }

            return View::make($type->getView(), compact('field'), 'Fields')
                ->with('value', $request->old($field->key, $value))
                ->render();

        })->implode("\n");
    }
}
