<?php

namespace App\Modules\Fields\Support;

use Nova\Http\Request;
use Nova\Database\ORM\Collection as BaseCollection;
use Nova\Validation\Validator;
use Nova\Support\Arr;

use App\Modules\Fields\Models\MetaData as MetaItem;
use App\Modules\Fields\Support\MetaCollection;
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

    public function getFieldTypes(MetaCollection $items = null)
    {
        if (is_null($items)) {
            $items = with(new MetaItem())->newCollection();
        }

        $types = new BaseCollection();

        foreach ($this->items as $field) {
            if ($field->hidden === 1) {
                continue;
            }

            // The field is not hidden.
            else if (! is_null($key = $items->findItem($field->key))) {
                $item = $items->get($key);

                $type = $item->getTypeInstance();
            } else {
                $className = $field->type;

                $type = new $className();
            }

            $type->setField($field);

            $types->add($type);
        }

        return $types;
    }

    public function renderForEditor(Request $request, MetaCollection $items = null)
    {
        return $this->getFieldTypes($items)->map(function ($fieldType) use ($request)
        {
            return $fieldType->renderForEditor($request);

        })->implode("\n");
    }
}
