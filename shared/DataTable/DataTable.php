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
    protected $columns = array();


    /**
     * Server Side Processor for DataTables.
     *
     * @param Nova\Database\Query\Builder|Nova\Database\ORM\Builder $query
     * @param array $columns
     *
     * @return array
     */
    public function __construct(Factory $factory, $query, array $columns)
    {
        $this->factory = $factory;

        $this->query = $query;

        foreach ($columns as $column) {
            if (! is_array($column) || is_null($name = Arr::get($column, 'name'))) {
                throw new InvalidArgumentException('Invalid column specified.');
            }

            $this->columns[$name] = $column;
        }

        if ($query instanceof ModelBuilder) {
            $baseQuery = $query->getQuery();

            if (is_null($baseQuery->columns)) {
                $table = $query->getModel()->getTable();

                $query->select($table .'.*');
            }
        }
    }

    /**
     * Adds a column definition to internal options.
     *
     * @param string  $name
     * @param string|\Closure|null  $data
     * @param \Closure|null  $callback
     *
     * @return array
     */
    public function column($name, $data = null, Closure $callback = null)
    {
        if (isset($this->columns[$name])) {
            throw new InvalidArgumentException('Column already exists.');
        } else if (preg_match('/^[a-z]\w+/i', $name) !== 1) {
            throw new InvalidArgumentException('Invalid column name.');
        }

        $safeName = str_replace('.', '_', $name);

        if (is_null($data)) {
            $data = $safeName;
        } else if ($data instanceof Closure) {
            list ($callback, $data) = array($data, $safeName);
        }

        // A standard column data.
        else if (preg_match('/^[a-z]\w+/i', $data) !== 1) {
            throw new InvalidArgumentException('Invalid column name.');
        }

        $column = compact('name', 'data');

        if (! is_null($callback)) {
            $column['uses'] = $callback;
        }

        $this->columns[$name] = $column;

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
            $request = $this->getRequest();
        }

        $input = $request->only('draw', 'columns', 'start', 'length', 'search', 'order');

        // Get the columns from input.
        $columns = array_filter($columns = Arr::get($input, 'columns', array()), function ($column)
        {
            return is_array($column) && ! empty($column);
        });

        //
        // Compute the draw.

        $draw = (int) Arr::get($input, 'draw', 0);

        //
        // Compute the total count.

        $recordsTotal = $query->count();

        //
        // Handle the global searching.

        $value = Arr::get($input, 'search.value', '');

        if ($this->validSearchValue($value = trim($value))) {
            $query->where(function ($query) use ($columns, $value)
            {
                foreach ($columns as $column) {
                    $searchable = Arr::get($column, 'searchable', 'false');

                    if (($searchable === 'true') && ! is_null($field = Arr::get($column, 'name'))) {
                        $this->columnSearch($query, $field, $value, 'or');
                    }
                }
            });
        }

        //
        // Handle the column searching.

        foreach ($columns as $column) {
            $searchable = Arr::get($column, 'searchable', 'false');

            if (($searchable === 'true') && ! is_null($field = Arr::get($column, 'name'))) {
                $value = Arr::get($column, 'search.value', '');

                if ($this->validSearchValue($value = trim($value))) {
                    $this->columnSearch($query, $field, $value, 'and');
                }
            }
        }

        //
        // Compute the filtered count.

        $recordsFiltered = $query->count();

        //
        // Handle the column ordering.

        $orders = array_filter($orders = Arr::get($input, 'order', array()), function ($order)
        {
            return is_array($order) && isset($order['column']) && isset($order['dir']);
        });

        foreach ($orders as $order) {
            $key = (int) Arr::get($order, 'column', -1);

            $column = (($key !== -1) && isset($columns[$key])) ? $columns[$key] : array();

            $orderable = Arr::get($column, 'orderable', 'false');

            if (($orderable === 'true') && ! is_null($field = Arr::get($column, 'name'))) {
                $direction = ($order['dir'] === 'asc') ? 'ASC' : 'DESC';

                $this->columnOrdering($query, $field, $direction);
            }
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
     * @param string $field
     * @param string $search
     * @param string $boolean
     *
     * @return void
     */
    protected function columnSearch($query, $field, $search, $boolean = 'and')
    {
        if (($query instanceof ModelBuilder) && Str::contains($field, '.')) {
            list ($relation, $field) = explode('.', $field, 2);

            return $query->has($relation, '>=', 1, $boolean, function ($query) use ($field, $search)
            {
                $query->where($field, 'like', '%' .$search .'%');
            });
        }

        $query->where($field, 'like', '%' .$search .'%', $boolean);
    }

    /**
     * Handles the search for a column.
     *
     * @param Nova\Database\Query\Builder|Nova\Database\ORM\Builder $query
     * @param string $field
     * @param string $direction
     *
     * @return void
     */
    protected function columnOrdering($query, $field, $direction)
    {
        if (($query instanceof ModelBuilder) && Str::contains($field, '.')) {
            list ($relation, $column) = explode('.', $field, 2);

            //
            $relation = $query->getRelation($relation);

            $table = with($related = $relation->getRelated())->getTable();

            $hasQuery = $relation->getRelationCountQuery($related->newQuery(), $query);

            //
            $column = with($grammar = $query->getGrammar())->wrap($table .'.' .$column);

            $sql = str_replace('count(*)', 'group_concat(distinct ' .$column .')', $hasQuery->toSql());

            $field = str_replace('.', '_', $field) .'_order';

            $query->selectRaw('('. $sql .') as ' .$grammar->wrap($field), $hasQuery->getBindings());
        }

        $query->orderBy($field, $direction);
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

        foreach ($this->columns as $name => $column) {
            $field = Arr::get($column, 'uses', $name);

            $data = Arr::get($column, 'data', str_replace('.', '_', $name));

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
     * @param int $status
     * @param array $headers
     * @param int $options
     *
     * @return \Nova\Http\JsonResponse
     */
    protected function createResponse(array $data, $status = 200, array $headers = array(), $options = 0)
    {
        $responseFactory = $this->getResponseFactory();

        return $responseFactory->json($data, $status, $headers, $options);
    }

    /**
     * Validate the given string for validity as search query.
     *
     * @param string $value
     *
     * @return bool
     */
    protected function validSearchValue($value)
    {
        return preg_match('/^[\p{L}\p{M}\p{N}\p{P}\p{Zs}_-]+$/u', $value);
    }

    /**
     * Returns the Request instance.
     *
     * @return \Nova\Http\Request
     */
    protected function getRequest()
    {
        return $this->factory->getRequest();
    }

    /**
     * Returns the Response Factory instance.
     *
     * @return \Nova\Routing\ResponseFactory
     */
    protected function getResponseFactory()
    {
        return $this->factory->getResponseFactory();
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
    protected function getColumns()
    {
        return array_map(function ($column)
        {
            return $column;

        }, $this->columns);
    }
}
