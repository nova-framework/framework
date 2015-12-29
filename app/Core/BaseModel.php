<?php
/**
 * BaseModel - Extended Base Class for all the Application Models.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 27th, 2015
 */

namespace App\Core;

use Nova\Core\Model;


class BaseModel extends Model
{
    /**
     * The Model's default Table name.
     *
     * @var string;
     */
    protected $table_name;

    /**
     * The model's default primary key.
     *
     * @var string
     */
    protected $primary_key = 'id';

    /**
     * The type of date/time field used for created_on and modified_on fields.
     * Valid types are: 'int', 'datetime', 'date'
     *
     * @var string
     * @access protected
     */
    protected $date_format = 'datetime';

    /**
     * Whether or not to auto-fill a 'created_on' field on inserts.
     *
     * @var boolean
     * @access protected
     */
    protected $set_created = true;

    /**
     * Field name to use to the created time column in the DB table.
     *
     * @var string
     * @access protected
     */
    protected $created_field = 'created_on';

    /**
     * Whether or not to auto-fill a 'modified_on' field on updates.
     *
     * @var boolean
     * @access protected
     */
    protected $set_modified = true;

    /**
     * Field name to use to the modified time column in the DB table.
     *
     * @var string
     * @access protected
     */
    protected $modified_field = 'modified_on';

    /**
     * Various callbacks available to the Class.
     * They are simple lists of method names (methods will be ran on $this).
     */
    protected $before_find   = array();
    protected $after_find    = array();
    protected $before_insert = array();
    protected $after_insert  = array();
    protected $before_update = array();
    protected $after_update  = array();
    protected $before_select = array();
    protected $after_select  = array();
    protected $before_delete = array();
    protected $after_delete  = array();

    protected $callback_parameters = array();

    /**
     * By default, we return items as objects. You can change this for the entire class by setting this
     * value to 'array' instead of 'object', or you can specify a qualified Class name for the returned object.
     * Alternatively, you can do it on a per-instance basis using the 'as_array()' and 'as_object()' methods.
     */
    protected $return_type = 'object';
    protected $temp_return_type = null;

    /**
     * Temporary select's WHERE attributes.
     */
    protected $temp_select_where = array();

    /**
     * Temporary select's LIMIT attribute.
     */
    protected $temp_select_limit = null;

    /**
     * Temporary select's ORDER attribute.
     */
    protected $temp_select_order = null;

    /**
     * Protected, non-modifiable attributes
     */
    protected $protected_attributes = array();


    //--------------------------------------------------------------------

    /**
     * Constructor
     */
    public function __construct($linkName = 'default')
    {
        parent __construct($linkName);

        // Always protect our attributes
        array_unshift($this->before_insert, 'protect_attributes');
        array_unshift($this->before_update, 'protect_attributes');

        //
        // Check our auto-set features and make sure they are part of our Observer System.

        if ($this->set_created === true) {
            array_unshift($this->before_insert, 'created_on');
        }

        if ($this->set_modified === true) {
            array_unshift($this->before_update, 'modified_on');
        }

        // Make sure our temp return type is correct.
        $this->temp_return_type = $this->return_type;
    }

    //--------------------------------------------------------------------
    // CRUD Methods
    //--------------------------------------------------------------------

    /**
     * A simple way to grab the first result of a search only.
     */
    public function first()
    {
        $result = $this->limit(1, 0)->find_all();

        if (is_array($result) && count($result)) {
            return $result[0];
        }

        return $result;
    }

    /**
     * Finds a single record based on it's primary key.
     *
     * @param  mixed $id The primary_key value of the object to retrieve.
     * @return object
     */
    public function find($id)
    {
        if(! is_integer($id)) {
            throw new \UnexpectedValueException('Parameter should be an Integer');
        }

        //
        $this->trigger('before_find', array('id' => $id, 'method' => 'find'));

        // Prepare the SQL Query.
        $sql = "SELECT * FROM " .$this->table() ." WHERE " .$this->primary_key ." = :param";

        $result = $this->select($sql, array('param' => $id));

        if (! empty($result)) {
            $result = $this->trigger('after_find', array('id' => $id, 'method' => 'find', 'fields' => $result));
        }

        return $result;
    }

    /**
     * Fetch a single record based on an arbitrary WHERE call.
     *
     * @return object
     */
    public function find_by()
    {
        $bindParams = array();

        // Prepare the WHERE parameters.
        $params = func_get_args();

        $this->set_where($params);

        // Prepare the WHERE details.
        $whereDetails = $this->where_details($this->temp_select_where, $bindParams);

        // Prepare the SQL Query.
        $sql = "SELECT * FROM " .$this->table() ." $whereDetails";

        //
        $this->trigger('before_find', array('method' => 'find_by', 'fields' => $where));

        $result = $this->select($sql, $bindParams);

        if (! empty($result)) {
            $result = $this->trigger('after_find', array('method' => 'find_by', 'fields' => $result));
        }

        // Reset our select WHEREs
        $this->temp_select_where = array();

        return $result;
    }

    /**
     * Retrieves a number of items based on an array of primary_values passed in.
     *
     * @param  array $values An array of primary key values to find.
     *
     * @return object or FALSE
     */
    public function find_many($values)
    {
        if(! is_array($values)) {
            throw new \UnexpectedValueException('Parameter should be an Array');
        }

        // Prepare the ORDER details.
        $orderDetails = $this->order_details($this->temp_select_order);

        // Prepare the SQL Query.
        $sql = "SELECT * FROM " .$this->table() ." WHERE " .$this->primary_key ." IN (".implode(',', $values) .") $orderDetails";

        //
        $result = $this->select($sql, array(), true);

        // Reset our select ORDER
        $this->temp_select_order = null;

        return $result;
    }

    /**
     * Retrieves a number of items based on an arbitrary WHERE call.
     *
     * @return object or FALSE
     */
    public function find_many_by()
    {
        $params = func_get_args();

        $this->set_where($params);

        return $this->find_all();
    }

    /**
     * Fetch all of the records in the table.
     * Can be used with scoped calls to restrict the results.
     *
     * @return object or FALSE
     */
    public function find_all()
    {
        $bindParams = array();

        // Prepare the WHERE details.
        $whereDetails = $this->where_details($this->temp_select_where, $bindParams);

        // Prepare the LIMIT details.
        $limitDetails = $this->limit_details($this->temp_select_limit);

        // Prepare the ORDER details.
        $orderDetails = $this->order_details($this->temp_select_order);

        // Prepare the SQL Query.
        $sql = "SELECT * FROM " .$this->table() ." $whereDetails $limitDetails $orderDetails";

        //
        $this->trigger('before_find', array('method' => 'find_all', 'fields' => $where));

        $result = $this->select($sql, $bindParams, true);

        if (is_array($result)) {
            foreach ($result as $key => &$row) {
                $row = $this->trigger('after_find', array('method' => 'find_all', 'fields' => $row));
            }
        }

        // Reset our select WHEREs
        $this->temp_select_where = array();

        // Reset our select LIMIT
        $this->temp_select_limit = null;

        // Reset our select ORDER
        $this->temp_select_order = null;

        return $result;
    }

    /**
     * Inserts data into the database.
     *
     * @param  array $data An array of key/value pairs to insert to database.
     * @return mixed       The primary_key value of the inserted record, or FALSE.
     */
    public function insert($data)
    {
        $data = $this->trigger('before_insert', array('method' => 'insert', 'fields' => $data));

        $result = $this->db->insert($this->table(), $data);

        if($result !== false) {
            $this->trigger('after_insert', ['id' => $result, 'fields' => $data, 'method' => 'insert']);
        }

        return false;
    }

    /**
     * Inserts multiple rows into the database at once. Takes an associative
     * array of value pairs.
     *
     * $data = array(
     *     array(
     *         'title' => 'My title'
     *     ),
     *     array(
     *         'title'  => 'My Other Title'
     *     )
     * );
     *
     * @param  array $data An associate array of rows to insert
     * @return bool
     */
    public function insert_batch($data)
    {
        $data['batch'] = true;

        $data = $this->trigger('before_insert', array('method' => 'insert_batch', 'fields' => $data);

        unset($data['batch']);

        return $this->db->insertBatch($this->table_name, $data);
    }

    /**
     * Updates an existing record in the database.
     *
     * @param  mixed $id The primary_key value of the record to update.
     * @param  array $data An array of value pairs to update in the record.
     * @return bool
     */
    public function update($id, $data)
    {
        $data = $this->trigger('before_update', array('id' => $id, 'method' =>'update', 'fields' => $data));

        $result = $this->db->update($this->table(), $data, array($this->primary_key => $id));

        $result = $this->trigger('after_update', array(
            'id'     => $id,
            'method' => 'update'
            'fields' => $data,
            'result' => $result,
        ));

        return $result;
    }

    /**
     * Updates multiple records in the database at once.
     *
     * $data = array(
     *     array(
     *         'title'  => 'My title',
     *         'body'   => 'body 1'
     *     ),
     *     array(
     *         'title'  => 'Another Title',
     *         'body'   => 'body 2'
     *     )
     * );
     *
     * The $where_key should be the name of the column to match the record on.
     * If $whereKey == 'title', then each record would be matched on that
     * 'title' value of the array. This does mean that the array key needs
     * to be provided with each row's data.
     *
     * @param  array $data An associate array of row data to update.
     * @param  string $whereKey The column name to match on.
     * @return bool
     */
    public function update_batch($data, $whereKey)
    {
        foreach ($data as &$row) {
            $row = $this->trigger('before_update', array('method' => 'update_batch', 'fields' => $row));
        }

        $result = $this->db->updateBatch($this->table(), $data, $whereKey);

        foreach ($data as &$row) {
            $this->trigger('after_update', array('fields' => $data, 'result' => $result, 'method' => 'update_batch'));
        }

        return $result;
    }

    /**
     * Updates many records by an array of ids.
     *
     * While update_batch() allows modifying multiple, arbitrary rows of data
     * on each row, update_many() sets the same values for each row.
     *
     * $ids = array(1, 2, 3, 5, 12);
     * $data = array(
     *     'deleted_by' => 1
     * );
     *
     * $this->model->update_many($ids, $data);
     *
     * @param  array $ids An array of primary_key values to update.
     * @param  array $data An array of value pairs to modify in each row.
     * @return bool
     */
    public function update_many($ids, $data)
    {
        if (! is_array($ids) || (count($ids) == 0)) return NULL;

        $data = $this->trigger('before_update', array('ids' => $ids, 'method' => 'update_many', 'fields' => $data));

        // Prepare the custom WHERE.
        $where = $this->primary_key ." IN (".implode(',', $values) .")";

        //
        $result = $this->db->update($this->table(), $data, $where);

        $this->trigger('after_update', array(
            'ids'    => $ids,
            'fields' => $data,
            'result' => $result,
            'method' => 'update_many'
        ));

        return $result;
    }

    /**
     * Update records in the database using a standard WHERE clause.
     *
     * Your last parameter should be the $data array with values to update
     * on the rows. Any additional parameters should be provided to make up
     * a typical WHERE clause. This could be a single array, or a column name
     * and a value.
     *
     * $data = array('deleted_by' => 1);
     * $wheres = array('user_id' => 15);
     *
     * $this->update_by($wheres, $data);
     * $this->update_by('user_id', 15, $data);
     *
     * @param array $data An array of data pairs to update
     * @param one or more WHERE-acceptable entries.
     * @return bool
     */
    public function update_by()
    {
        $params = func_get_args();

        $data = array_pop($params);

        if(empty($params) || empty($data)) {
            throw new \UnexpectedValueException('Invalid parameters');
        }

        // Prepare the WHERE parameters.
        if(is_array($params[0])) {
            $where = $params[0];
        }
        else {
            $value = isset($params[1]) ? $params[1] : '';

            $where = array($params[0] => $value);
        }

        //
        $data = $this->trigger('before_update', array('method' => 'update_by', 'fields' => $data));

        $result = $this->db->update($this->table(), $data, $where);

        $this->trigger('after_update', array(
            'method' => 'update_by',
            'fields' => $data,
            'result' => $result
        ));

        return $result;
    }

    /**
     * Updates all records and sets the value pairs passed in the array.
     *
     * @param  array $data An array of value pairs with the data to change.
     * @return bool
     */
    public function update_all($data)
    {
        $data = $this->trigger('before_update', array('method' => 'update_all', 'fields' => $data));

        $result = $this->db->update($this->table(), $data, '1');

        $this->trigger('after_update', array(
            'method' => 'update_all',
            'fields' => $data,
            'result' => $result
        ));

        return $result;
    }

    /**
     * Increments the value of field for a given row, selected by the primary key for the table.
     *
     * @param $id
     * @param $field
     * @param int $value
     * @return mixed
     */
    public function increment($id, $field, $value = 1)
    {
        $value = (int)abs($value);

        //
        $data = array($field => "{$field}+{$value}");

        $where = array($this->primary_key => $id);

        return $this->db->update($this->table(), $data, $where);
    }

    /**
     * Increments the value of field for a given row, selected by the primary key for the table.
     *
     * @param $id
     * @param $field
     * @param int $value
     * @return mixed
     */
    public function decrement($id, $field, $value = 1)
    {
        $value = (int)abs($value);

        //
        $data = array($field => "{$field}-{$value}");

        $where = array($this->primary_key => $id);

        return $this->db->update($this->table(), $data, $where);
    }

    /**
     * Execute Select Query, bind values into the $sql query. And give optional method and class for fetch result
     * The result MUST be an array!
     *
     * @param string $sql
     * @param array $bindParams
     * @param bool $fetchAll Ask the method to fetch all the records or not.
     * @return array|null
     *
     * @throws \Exception
     */
    public function select($sql, $bindParams = array(), $fetchAll = false)
    {
        // Firstly, simplify the white spaces and trim the SQL query.
        $sql = preg_replace('/\s+/', ' ', trim($sql));

        $result = $this->db->select($sql, $bindParams, $fetchAll, $this->temp_return_type);

        // Make sure our temp return type is correct.
        $this->temp_return_type = $this->return_type;

        return $result;
    }

    /**
     * Fetch first one record, optionally with WHEREs.
     *
     * @return array|null|false
     * @throws \Exception
     */
    public function select_by()
    {
        $bindParams = array();

        //
        $params = func_get_args();

        $fields = array_pop($params);

        if(empty($params) && empty($fields)) {
            throw new \UnexpectedValueException('Invalid parameters');
        }

        // Prepare the WHERE parameters.
        if(is_array($params[0])) {
            $where = $params[0];
        }
        else {
            $value = isset($params[1]) ? $params[1] : '';

            $where = array($params[0] => $value);
        }

        // Prepare the FIELDS details.
        $fieldDetails = '*';

        if(is_array($fields)) {
            $fieldDetails = implode(', ', $fields);
        }
        else if(is_string($fields)) {
            $fieldDetails = $fields;
        }

        // Prepare the WHERE details.
        $whereDetails = $this->where_details($where, $bindParams);

        // Prepare the SQL Query
        $sql = "SELECT $fieldDetails FROM " .$this->table() ." $whereDetails";

        //
        $this->trigger('before_select', array('method' => 'select_one', 'fields' => $fields));

        $result = $this->select($sql, $bindParams);

        if (! empty($result)) {
            $result = $this->trigger('after_select', array('method' => 'select_one', 'fields' => $result));
        }

        return $result;
    }

    /**
     * Fetch all records, optionally with WHEREs and LIMITs.
     *
     * @param string $fields
     * @return array|null|false
     * @throws \Exception
     */
    public function select_all($fields = '*')
    {
        $bindParams = array();

        // Prepare the WHAT details.
        $fieldDetails = '*';

        if(is_array($fields)) {
            $fieldDetails = implode(', ', $fields);
        }
        else if(is_string($fields)) {
            $fieldDetails = $fields;
        }

        // Prepare the WHERE details.
        $whereDetails = $this->where_details($this->temp_select_where, $bindParams);

        // Prepare the LIMIT details.
        $limitDetails = $this->limit_details($this->temp_select_limit);

        // Prepare the ORDER details.
        $orderDetails = $this->order_details($this->temp_select_order);

        // Prepare the SQL Query
        $sql = "SELECT $fieldDetails FROM " .$this->table() ." $whereDetails $limitDetails $orderDetails";

        //
        $this->trigger('before_select', array('method' => 'select_all', 'fields' => $fields));

        $result = $this->select($sql, $bindParams, true);

        if (is_array($result)) {
            foreach ($result as $key => &$row) {
                $row = $this->trigger('after_select', array('method' => 'select_all', 'fields' => $row));
            }
        }

        // Reset our select WHEREs
        $this->temp_select_where = array();

        // Reset our select LIMIT
        $this->temp_select_limit = null;

        // Reset our select LIMIT
        $this->temp_select_order = null;

        return $result;
    }

    /**
     * Deletes a row by it's primary key value.
     *
     * @param  mixed $id The primary key value of the row to delete.
     * @return bool
     */
    public function delete($id)
    {
        if(! is_integer($id)) {
            throw new \UnexpectedValueException('Parameter should be an Integer');
        }

        $where($this->primary_key => $id);

        //
        $this->trigger('before_delete', array('id' => $id, 'method' => 'delete'));

        $result = $this->db->delete($this->table(), $where);

        $this->trigger('after_delete', array(
            'id' => $id,
            'method' => 'delete',
            'result' => $result
        ));

        return $result;
    }

    public function delete_by()
    {
        $params = func_get_args();

        if(empty($params)) {
            throw new \UnexpectedValueException('Invalid parameters');
        }

        // Prepare the WHERE parameters.
        if(is_array($params[0])) {
            $where = $params[0];
        }
        else {
            $value = isset($params[1]) ? $params[1] : '';

            $where = array($params[0] => $value);
        }

        //
        $where = $this->trigger('before_delete', array('method' => 'delete_by', 'fields' => $where));

        $result = $this->db->delete($this->table(), $where);

        $this->trigger('after_delete', array(
            'method' => 'delete_by',
            'fields' => $where,
            'result' => $result
        ));

        return $result;
    }

    public function delete_many($ids)
    {
        if (! is_array($ids) || (count($ids) == 0)) return NULL;

        $ids = $this->trigger('before_delete', array('ids' => $ids, 'method' => 'delete_many'));

        //
        $where = $this->primary_key ." IN (".implode(',', $ids) .")";

        $result = $this->db->delete($this->table(), $where);

        $this->trigger('after_delete', array(
            'ids' => $ids,
            'method' => 'delete_many',
            'result' => $result
        ));

        return $result;
    }

    public function query($sql)
    {
        return $this->db->rawQuery($sql);
    }

    public function prepare($sql, $bindParams = array())
    {
        return $this->db->rawPrepare($sql, $bindParams);
    }

    /**
     * Getter for the table name.
     *
     * @return string The name of the table used by this class.
     */
    public function table()
    {
        return $this->table_name;
    }

    /**
     * Checks whether a field/value pair exists within the table.
     *
     * @param string $field The field to search for.
     * @param string $value The value to match $field against.
     *
     * @return bool TRUE/FALSE
     */
    public function is_unique($field, $value)
    {
        $sql = "SELECT $field FROM " .$this->table() ." WHERE $field = :$field";

        $data = $this->select($sql, array($field => $value), true);

        if (is_array($data) && (count($data) == 0)) {
            return true;
        }

        return true;
    }

    /**
     * Adds a field to the protected_attributes array.
     *
     * @param $field
     *
     * @return mixed
     */
    public function protect($field)
    {
        $this->protected_attributes[] = $field;

        return $this;
    }

    /**
     * Temporarily sets our return type to an array.
     */
    public function as_array()
    {
        $this->temp_return_type = 'array';

        return $this;
    }

    /**
     * Temporarily sets our return type to an object.
     *
     * If $class is provided, the rows will be returned as objects that
     * are instances of that class. $class MUST be an fully qualified
     * class name, meaning that it must include the namespace, if applicable.
     *
     * @param string $class
     * @return $this
     */
    public function as_object($className = null)
    {
        $this->temp_return_type = ! empty($className) ? $className : 'object';

        return $this;
    }

    public function where($field, $value = '')
    {
        array_push($this->temp_select_where, $field, $value);

        return $this;
    }

    public function limit($limit, $start = 0)
    {
        $this->temp_select_limit = array($start => $limit);

        return $this;
    }

    public function order($sense = 'ASC')
    {
        $sense = strtoupper($sense);

        if(($sense != 'ASC') && ($sense != 'DESC')) {
            throw new \UnexpectedValueException('Invalid parameter');
        }

        $this->temp_select_order = array($this->primary_key => $sense);

        return $this;
    }

    public function order_by($field, $sense = 'ASC')
    {
        $sense = strtoupper($sense);

        if(empty($field) || (($sense != 'ASC') && ($sense != 'DESC'))) {
            throw new \UnexpectedValueException('Invalid parameters');
        }

        $this->temp_select_order = array($field => $sense);

        return $this;
    }

    //--------------------------------------------------------------------
    // Observers
    //--------------------------------------------------------------------

    /**
     * Sets the created on date for the object based on the
     * current date/time and date_format. Will not overwrite existing.
     *
     * @param array $row The array of data to be inserted
     *
     * @return array
     */
    public function created_on($row)
    {
        if (! is_array($row) || empty($row['fields'])) {
            return null;
        }

        $row = $row['fields'];

        // Created_on
        $field =& $this->created_field;

        if (is_array($row) && ! array_key_exists($field, $row)) {
            $row[$field] = $this->set_date();
        }

        return $row;
    }

    /**
     * Sets the modified_on date for the object based on the
     * current date/time and date_format. Will not overwrite existing.
     *
     * @param array $row The array of data to be inserted
     *
     * @return array
     */
    public function modified_on($row)
    {
        if (! is_array($row) || empty($row['fields'])) {
            return null;
        }

        $row = $row['fields'];

        // Modified_on
        $field =& $this->modified_field;

        if (is_array($row) && ! array_key_exists($field, $row)) {
            $row[$field] = $this->set_date();
        }

        return $row;
    }

    //--------------------------------------------------------------------
    // Internal Methods
    //--------------------------------------------------------------------

    /**
     * Protect attributes by removing them from $row array. Useful for
     * removing id, or submit buttons names if you simply throw your $_POST
     * array at your model. :)
     *
     * @param object /array $row The value pair item to remove.
     */
    public function protect_attributes($row)
    {
        foreach ($this->protected_attributes as $attr) {
            if (is_object($row)) {
                unset($row->$attr);
            } else {
                unset($row[$attr]);
            }
        }

        return $row;
    }

    /**
     * Triggers a model-specific event and call each of it's Observers.
     *
     * @param string $event The name of the event to trigger
     * @param mixed $data The data to be passed to the callback functions.
     *
     * @return mixed
     */
    public function trigger($event, $data = false)
    {
        if (! isset($this->$event) || ! is_array($this->$event))
        {
            if (isset($data['fields'])) {
                return $data['fields'];
            }

            return $data;
        }

        foreach ($this->$event as $method) {
            if (strpos($method, '(') !== false) {
                preg_match('/([a-zA-Z0-9\_\-]+)(\(([a-zA-Z0-9\_\-\., ]+)\))?/', $method, $matches);

                $this->callback_parameters = explode(',', $matches[3]);
            }

            $data = call_user_func_array(array($this, $method), array($data));
        }

        // In case no method called or method returned the entire data array, we typically just need the $fields
        if (isset($data['fields'])) {
            return $data['fields'];
        }

        // A few methods might need to return 'ids'
        if (isset($data['ids'])) {
            return $data['ids'];
        }

        return $data;
    }

    /**
     * A utility function to allow child models to use the type of
     * date/time format that they prefer. This is primarily used for
     * setting created_on and modified_on values, but can be used by
     * inheriting classes.
     *
     * The available time formats are:
     * * 'int'      - Stores the date as an integer timestamp.
     * * 'datetime' - Stores the date and time in the SQL datetime format.
     * * 'date'     - Stores teh date (only) in the SQL date format.
     *
     * @param mixed $user_date An optional PHP timestamp to be converted.
     *
     * @access protected
     *
     * @return int|null|string The current/user time converted to the proper format.
     */
    protected function set_date($user_date = NULL)
    {
        $curr_date = ! empty($user_date) ? $user_date : time();

        switch ($this->date_format) {
            case 'int':
                return $curr_date;
                break;

            case 'datetime':
                return date('Y-m-d H:i:s', $curr_date);
                break;

            case 'date':
                return date('Y-m-d', $curr_date);
                break;
        }
    }

    protected function set_where($params)
    {
        if(empty($params)) {
            throw new \UnexpectedValueException('Parameters can not be empty');
        }

        if(is_array($params[0])) {
            $this->tmp_select_where = array_merge($this->tmp_select_where, $params[0]);
        }
        else {
            array_push($this->temp_select_where, $params[0], isset($params[1]) ? $params[1] : '');
        }
    }

    protected function where_details(array $where, &$bindParams = array())
    {
        $result = '';

        ksort($where);

        $idx = 0;

        foreach ($where as $key => $value) {
            if($idx > 0) {
                $whereDetails .= ' AND ';
            }

            $idx++;

            if(empty($value)) {
                // A string based condition; simplify its white spaces and use it directly.
                $result .= preg_replace('/\s+/', ' ', trim($key));

                continue;
            }

            if(strpos($key, ' ') !== false) {
                $key = preg_replace('/\s+/', ' ', trim($key));

                $segments = explode(' ', $key);

                $key      = $segments[0];
                $operator = $segments[1];
            }
            else {
                $operator = '=';
            }

            $result .= "$key $operator :$key";

            $bindParams[$key] = $value;
        }

        if(! empty($result)) {
            $result = 'WHERE ' .$result;
        }

        return $result;
    }

    protected function limit_details($select_limit)
    {
        $result = '';

        if(is_numeric($select_limit)) {
            $result = '0, ' .$select_limit;
        }
        else if(is_array($select_limit) && ! empty($select_limit)) {
            list($key, $value) = each($select_limit);

            $result = $key .' ' .$value;
        }

        if(! empty($result)) {
            $result = 'LIMIT ' .$result;
        }

        return $result;
    }

    protected function limit_details($select_order)
    {
        $result = '';

        if(is_array($select_order) && ! empty($select_order)) {
            list($key, $value) = each($select_order);

            $result = 'LIMIT ' .$key .' ' .$value;
        }

        return $result;
    }

    //--------------------------------------------------------------------
    // Magic Methods
    //--------------------------------------------------------------------

    /**
     * Magic method to capture calls to undefined class methods.
     * In this case we are attempting to convert camel case formatted methods into underscore formatted methods.
     *
     * This allows us to call Model methods using camel case and remain backwards compatible.
     *
     * @param  string   $name
     * @param  array    $params
     */
    public function __call($name, $params = null)
    {
        $method = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name));

        if (! method_exists($this, $method)) {
            throw new \BadMethodCallException("Method $name() does not exist in class " . get_class($this));
        }

        return call_user_func_array(array($this, $method), $params);
    }

}
