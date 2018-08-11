<?php

namespace Shared\DataTable;

use Nova\Database\ORM\Builder as ModelBuilder;
use Nova\Http\Request;
use Nova\Support\Arr;
use Nova\Support\Str;

use Closure;
use InvalidArgumentException;


class DataTable
{
    /**
     * @var Shared\DataTable\Factory
     */
    protected $factory;

    /**
     * @var Nova\Database\Query\Builder|Nova\Database\ORM\Builder
     */
    protected $query;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var string
     */
    protected $table;


    /**
     * Server Side Processor for DataTables.
     *
     * @param Nova\Database\Query\Builder|Nova\Database\ORM\Builder $query
     * @param array $options
     *
     * @return array
     */
    public function __construct(Factory $factory, $query, array $options)
    {
        $this->factory = $factory;

        $this->options = $options;

        $this->query = $query;

        // For an ORM query we will set the Model table and add the default * columns.
        if ($query instanceof ModelBuilder) {
            $this->table = $table = $query->getModel()->getTable();

            $query->select($table .'.*');
        }
    }

    /**
     * Adds a column definition to internal options.
     *
     * @param string  $data
     * @param string|null  $name
     * @param \Closure|null  $callback
     *
     * @return array
     */
    public function column($data, $name = null, Closure $callback = null)
    {
        if (preg_match('/^[a-z]\w+/i', $data) !== 1) {
            throw new InvalidArgumentException('Invalid column data.');
        }

        if (is_null($name)) {
            $name = $data;
        } else if ($name instanceof Closure) {
            list ($callback, $name) = array($name, $data);
        }

        // A standard column name.
        else if (preg_match('/^[a-z]\w+/i', $name) !== 1) {
            throw new InvalidArgumentException('Invalid column name.');
        }

        $column = compact('data', 'name');

        if (! is_null($callback)) {
            $column['uses'] = $callback;
        }

        $this->options[] = $column;

        return $this;
    }

    /**
     * Handle a Request.
     *
     * @param Nova\Http\Request $request
     *
     * @return array
     */
    public function handle(Request $request = null)
    {
        $query = $this->getQuery();

        if (is_null($request)) {
            $request = $this->factory->getRequest();
        }

        $input = $request->only('draw', 'columns', 'start', 'length', 'search', 'order');

        // Get the columns from input.
        $columns = Arr::get($input, 'columns', array());

        //
        // Compute the total count.

        $recordsTotal = $query->count();

        //
        // Compute the draw.

        $draw = (int) Arr::get($input, 'draw', 0);

        //
        // Handle the global searching.

        $search = Arr::get($input, 'search.value', '');

        if (! empty($search = trim($search))) {
            $query->where(function ($query) use ($columns, $search)
            {
                foreach ($columns as $column) {
                    if ($column['searchable'] !== 'true') {
                        continue;
                    }

                    $this->handleColumnSearching($query, $column, $search, 'or');
                }
            });
        }

        //
        // Handle the column searching.

        foreach ($columns as $column) {
            $search = Arr::get($column, 'search.value', '');

            if (($column['searchable'] !== 'true') || empty($search = trim($search))) {
                continue;
            }

            $this->handleColumnSearching($query, $column, $search, 'and');
        }

        //
        // Compute the filtered count.

        $recordsFiltered = $query->count();

        //
        // Handle the column ordering.

        $orders = Arr::get($input, 'order', array());

        foreach ($orders as $order) {
            $key = (int) $order['column'];

            if (! isset($columns[$key])) {
                continue;
            }

            $column = $columns[$key];

            $direction = ($order['dir'] === 'asc') ? 'ASC' : 'DESC';

            $this->handleColumnOrdering($query, $column, $direction);
        }

        //
        // Handle the results pagination.

        $start  = Arr::get($input, 'start',  0);
        $length = Arr::get($input, 'length', 25);

        $query->skip($start)->take($length);

        //
        // Retrieve the results from database.

        $results = $query->get();

        //
        // Format the results according with DataTables specifications.

        $data = $results->map(function ($result)
        {
            return $this->createRecord($result);

        })->toArray();

        //
        // Create and return a JSON Response instance.

        return $this->createResponse(
            compact('draw', 'recordsTotal', 'recordsFiltered', 'data')
        );
    }

    /**
     * Handles the search for a column.
     *
     * @param Nova\Database\Query\Builder|Nova\Database\ORM\Builder $query
     * @param array $column
     * @param string $search
     * @param string $boolean
     *
     * @return void
     */
    protected function handleColumnSearching($query, $column, $search, $boolean = 'and')
    {
        if (! is_array($options = $this->getColumnOptions($column))) {
            return;
        } else if (is_null($field = Arr::get($options, 'name'))) {
            return;
        }

        if (($query instanceof ModelBuilder) && Str::contains($field, '.')) {
            list ($relation, $field) = explode('.', $field, 2);

            return $query->has($relation, '>=', 1, $boolean, function ($query) use ($field, $search)
            {
                $query->where($field, 'like', '%' .$search .'%');
            });
        }

        // Not a relationship based search.
        else if (! is_null($table = $this->getTable())) {
            $field = $table .'.' .$field;
        }

        $query->where($field, 'like', '%' .$search .'%', $boolean);
    }

    /**
     * Handles the search for a column.
     *
     * @param Nova\Database\Query\Builder|Nova\Database\ORM\Builder $query
     * @param array $column
     * @param string $direction
     *
     * @return void
     */
    protected function handleColumnOrdering($query, $column, $direction)
    {
        if (! is_array($options = $this->getColumnOptions($column))) {
            return;
        } else if (is_null($field = Arr::get($options, 'name'))) {
            return;
        }

        if (($query instanceof ModelBuilder) && Str::contains($field, '.')) {
            list ($relation, $column) = explode('.', $field, 2);

            //
            $relation = $query->getRelation($relation);

            $table = with($related = $relation->getRelated())->getTable();

            $hasQuery = $relation->getRelationCountQuery($related->newQuery(), $query);

            //
            $column = with($grammar = $query->getGrammar())->wrap($table .'.' .$column);

            $sql = str_replace('count(*)', 'group_concat(distinct ' .$column .')', $hasQuery->toSql());

            //
            $field = str_replace('.', '_', $field) .'_order';

            $query->selectRaw('('. $sql .') as ' .$grammar->wrap($field), $hasQuery->getBindings());
        }

        // Not a relationship based ordering.
        else if (! is_null($table = $this->getTable())) {
            $field = $table .'.' .$field;
        }

        $query->orderBy($field, $direction);
    }

    /**
     * Returns the column options.
     *
     * @param array $column
     *
     * @return array|null
     */
    protected function getColumnOptions(array $column)
    {
        $data = $column['data'];

        return Arr::first($this->options, function ($key, $value) use ($data)
        {
            return ($value['data'] == $data);
        });
    }

    /**
     * Builds a record from a query's result.
     *
     * @param mixed $result
     *
     * @return array
     */
    protected function createRecord($result)
    {
        $record = array();

        foreach ($this->getOptions() as $option) {
            $data = $option['data'];

            $field = Arr::get($option, 'uses', $name = $option['name']);

            if ($field instanceof Closure) {
                $value = call_user_func($field, $result, $name, $data);
            }

            // The column has no custom renderer.
            else if (! Str::contains($field, '.')) {
                $value = $result->{$field};
            } else {
                continue;
            }

            $record[$data] = $value;
        }

        return $record;
    }

    /**
     * Returns the column options.
     *
     * @param array $data
     *
     * @return \Nova\Http\JsonResponse
     */
    protected function createResponse(array $data, $status = 200, array $headers = array(), $options = 0)
    {
        $responseFactory = $this->factory->getResponseFactory();

        return $responseFactory->json($data, $status, $headers, $options);
    }

    /**
     * Returns the DataTable Factory instance.
     *
     * @return \Shared\DataTable\Factory
     */
    protected function getFactory()
    {
        return $this->query;
    }

    /**
     * Returns the current query.
     *
     * @return \Nova\Database\Query\Builder|Nova\Database\ORM\Builder
     */
    protected function getQuery()
    {
        return $this->query;
    }

    /**
     * Returns the options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return $this->options;
    }

    /**
     * Returns cached the current query Model table if any.
     *
     * @return string
     */
    protected function getTable()
    {
        return $this->table;
    }
}
