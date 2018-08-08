<?php

namespace Shared\Support;

use Nova\Database\ORM\Builder as ModelBuilder;
use Nova\Http\Request;
use Nova\Support\Arr;
use Nova\Support\Str;

use Closure;


class DataTable
{
    /**
     * Server Side Processor for DataTables.
     *
     * @param Nova\Database\Query\Builder|Nova\Database\ORM\Builder $query
     * @param Nova\Http\Request $request
     * @param array $options
     *
     * @return array
     */
    public static function handle($query, Request $request, array $options)
    {
        $input = $request->only('draw', 'columns', 'start', 'length', 'search', 'order');

        return with(new static)->process($query, $input, $options);
    }

    /**
     * Server Side Processor for DataTables.
     *
     * @param Nova\Database\Query\Builder|Nova\Database\ORM\Builder $query
     * @param array $input
     * @param array $options
     *
     * @return array
     */
    public function process($query, array $input, array $options)
    {
        $ormQuery = ($query instanceof ModelBuilder);

        // Add the default * columns when is used an ORM query.
        if ($ormQuery) {
            $table = $query->getModel()->getTable();

            $query->select($table .'.*');
        }

        // Get the columns from input.
        $columns = Arr::get($input, 'columns', array());

        // Compute the total count.
        $recordsTotal = $query->count();

        // Compute the draw.
        $draw = (int) Arr::get($input, 'draw', 0);

        // Handle the global searching.
        $search = Arr::get($input, 'search.value');

        if (! empty($search = trim($search))) {
            $query->where(function ($query) use ($columns, $options, $search, $ormQuery)
            {
                foreach ($columns as $column) {
                    if ($column['searchable'] !== 'true') {
                        continue;
                    }

                    $data = $column['data'];

                    $option = Arr::first($options, function ($key, $value) use ($data)
                    {
                        return ($value['data'] == $data);
                    });

                    if (! is_array($option) || ! isset($option['name'])) {
                        continue;
                    }

                    // We will try first the standard querying.
                    else if (! Str::contains($field = $option['name'], '.')) {
                        $query->orWhere($field, 'LIKE', '%' .$search .'%');

                        continue;
                    }

                    // The relationships handling needs an ORM query.
                    else if (! $ormQuery) {
                        continue;
                    }

                    list ($relation, $field) = explode('.', $field);

                    $query->orWhereHas($relation, function ($query) use ($field, $search)
                    {
                        $query->where($field, 'LIKE', '%' .$search .'%');
                    });
                }
            });
        }

        // Handle the column searching.
        foreach ($columns as $column) {
            $search = Arr::get($column, 'search.value');

            if (($column['searchable'] !== 'true') || empty($search = trim($search))) {
                continue;
            }

            $data = $column['data'];

            $option = Arr::first($options, function ($key, $value) use ($data)
            {
                return ($value['data'] == $data);
            });

            if (! is_array($option) || ! isset($option['name'])) {
                continue;
            }

            // We will try first the standard querying.
            else if (! Str::contains($field = $option['name'], '.')) {
                $query->where($field, 'LIKE', '%' .$search .'%');

                continue;
            }

            // The relationships handling needs an ORM query.
            else if (! $ormQuery) {
                continue;
            }

            list ($relation, $field) = explode('.', $field);

            $query->whereHas($relation, function ($query) use ($field, $search)
            {
                $query->where($field, 'LIKE', '%' .$search .'%');
            });
        }

        // Compute the filtered count.
        $recordsFiltered = $query->count();

        // Handle the column ordering.
        $orders = Arr::get($input, 'order', array());

        foreach ($orders as $order) {
            $index = intval($order['column']);

            $column = isset($columns[$index]) ? $columns[$index] : array();

            if ($column['orderable'] !== 'true') {
                continue;
            }

            //
            $data = $column['data'];

            $option = Arr::first($options, function ($key, $value) use ($data)
            {
                return ($value['data'] == $data);
            });

            if (! is_array($option) || ! isset($option['name'])) {
                continue;
            }

            $field = $option['name'];

            $direction = ($order['dir'] === 'asc') ? 'ASC' : 'DESC';

            if (! $ormQuery) {
                $query->orderBy($field, $direction);

                continue;
            }

            // An ORM query.
            else if (! Str::contains($field, '.')) {
                $table = $query->getModel()->getTable();

                $query->orderBy($table .'.' .$field, $direction);

                continue;
            }

            $grammar = $query->getGrammar();

            list ($relation, $field) = explode('.', $field, 2);

            // Get the Relation instance by relationship name.
            $relation = $query->getRelation($relation);

            $related = $relation->getRelated();

            $hasQuery = $relation->getRelationCountQuery($related->newQuery(), $query);

            // Build the SQL script needed by the relation's JOIN.
            $field = $related->getTable() .'.' .$field;

            $sql = str_replace('count(*)', 'group_concat(distinct ' .$grammar->wrap($field) .')', $hasQuery->toSql());

            // Create a sub-query select on main query.
            $field = str_replace('.', '_', $field) .'_order';

            $query->selectRaw('('. $sql .') as ' .$grammar->wrap($field), $hasQuery->getBindings());

            // Add the order by the field of sub-query.
            $query->orderBy($field, $direction);
        }

        // Handle the results pagination.
        $start  = Arr::get($input, 'start',  0);
        $length = Arr::get($input, 'length', 25);

        $query->skip($start)->take($length);

        // Retrieve the results from database.
        $results = $query->get();

        // Gather the data records from results.
        $data = $results->map(function ($result) use ($options)
        {
            $record = array();

            foreach ($options as $option) {
                $data = $option['data'];

                $field = Arr::get($option, 'uses', $name = $option['name']);

                if ($field instanceof Closure) {
                    $value = call_user_func($field, $result, $name, $data);
                }

                // The column has no custom renderer.
                else if (! Str::contains($field, '.')) {
                    $value = $result->getAttribute($field);
                } else {
                    continue;
                }

                $record[$data] = $value;
            }

            return $record;

        })->toArray();

        return array(
            "draw"            => $draw,
            "recordsTotal"    => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data"            => $data
        );
    }
}
