<?php

namespace App\Modules\Fields\Support;

use Nova\Http\Request;
use Nova\Database\ORM\Collection as BaseCollection;
use Nova\Validation\Validator;

use App\Modules\Fields\Models\MetaData as MetaItem;
use App\Modules\Fields\Support\MetaCollection;


class FieldCollection extends BaseCollection
{

    public function updatables()
    {
        $results = array();

        foreach ($this->items as $value) {
            if ($value->hidden === 1) {
                continue;
            }

            $key = data_get($value, 'key');

            $results[$key] = $value;
        }

        return $results;
    }

    public function validate(Validator $validator)
    {
        $attributes = array();

        foreach ($this->items as $field) {
            if (! isset($field->validate)) {
                continue;
            }

            $validator->mergeRules($key = $field->getAttribute('key'), $field->validate);

            $attributes[$key] = $field->name;
        }

        $validator->setAttributeNames($attributes);
    }

    public function getMetaTypes(MetaCollection $items = null)
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
        return $this->getMetaTypes($items)->map(function ($type) use ($request)
        {
            return $type->renderForEditor($request);

        })->implode("\n");
    }
}
