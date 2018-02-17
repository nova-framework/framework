<?php

namespace Modules\Fields\Models\Collection;

use Nova\Http\Request;
use Nova\Database\ORM\Collection as BaseCollection;
use Nova\Support\Facades\App;
use Nova\Support\Facades\View;
use Nova\Support\Arr;
use Nova\Validation\Validator;

use Modules\Fields\Models\Collection\MetaCollection;
use Modules\Fields\Types\BooleanType;
use Modules\Fields\Types\Registry as TypeRegistry;

use ErrorException;


class FieldCollection extends BaseCollection
{

    /**
     * Get the collection key form an item key.
     *
     * @param mixed $name
     * @return mixed
     */
    public function findItem($name)
    {
        $collection = $this->where('key', $name);

        if ($collection->count() > 0) {
            return $collection->keys()->first();
        }
    }

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
        $typeRegistry = App::make(TypeRegistry::class);

        return $this->where('hidden', 0)->sort(function ($a, $b)
        {
            if ($a->order === $b->order) {
                return strcmp($a->name, $b->name);
            }

            return ($a->order < $b->order) ? -1 : 1;

        })->map(function ($field) use ($typeRegistry, $request, $items)
        {
            $item = null;

            if (! is_null($items) && ! is_null($key = $items->findItem($field->key))) {
                $item = $items->get($key);

                if ($item->type !== $field->type) {
                    throw new ErrorException("Field and MetaData types does not match");
                }
            }

            $value = $request->old($field->key, isset($item) ? $item->value : null);

            return $typeRegistry->get($field->type)->renderForEditor($field, $value);

        })->implode("\n");
    }
}
