<?php

namespace App\Modules\Fields\Support;

use Nova\Http\Request;
use Nova\Database\ORM\Collection as BaseCollection;
use Nova\Validation\Validator;

use App\Modules\Fields\Models\MetaData as MetaItem;
use App\Modules\Fields\Support\MetaCollection;


class FieldCollection extends BaseCollection
{

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

    public function getMetaFields(MetaCollection $items = null)
    {
        if (is_null($items)) {
            $items = with(new MetaItem())->newCollection();
        }

        $fields = new BaseCollection();

        foreach ($this->items as $model) {
            if ($model->hidden === 1) {
                continue;
            }

            // The field is not hidden.
            else if (! is_null($key = $items->findItem($model->key))) {
                $item = $items->get($key);

                $field = $item->getField();
            } else {
                $fieldClass = $model->type;

                $field = new $fieldClass();
            }

            $field->setItem($model);

            $fields->add($field);
        }

        return $fields;
    }

    public function renderForEditor(Request $request, MetaCollection $items = null)
    {
        return $this->getMetaFields($items)->map(function ($field) use ($request)
        {
            return $field->renderForEditor($request);

        })->implode("\n");
    }
}
