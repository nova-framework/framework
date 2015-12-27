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

    public function insert($data)
    {
        $data = $this->trigger('before_insert', array(
            'method' =>'insert',
            'fields' => $data
        ));

        $result = $this->db->insert($this->table_name, $data);

        $result = $this->trigger('after_insert', array(
            'method' => 'insert',
            'fields' => $data,
            'result' => $result
        ));

        return $result;
    }

    public function update($data, $where)
    {
        $data = $this->trigger('before_update', array(
            'method' =>'update',
            'where'  => $where,
            'fields' => $data
        ));

        $result = $this->db->update($this->table_name, $data, $where);

        $result = $this->trigger('after_update', array(
            'method' => 'update'
            'where'  => $where,
            'fields' => $data,
            'result' => $result,
        ));

        return $result;
    }

    public function select($fields = '*', $where = false, $limits = true, $returnType = 'array')
    {
        $bindParams = array();

        // Prepare the parameters.
        $className = null;

        if($returnType == 'array') {
            $fetchMethod = \PDO::FETCH_ASSOC;
        }
        else if($returnType == 'object') {
            $fetchMethod = \PDO::FETCH_OBJ;
        }
        else {
            $fetchMethod = \PDO::FETCH_CLASS;

            $className = $returnType;
        }

        // Prepare the TABLE details.
        $table = DB_PREFIX .$this->table_name;

        // Prepare the WHAT details.
        $fieldDetails = '*';

        if(is_array($fields)) {
            $fieldDetails = implode(', ', $fields);
        }
        else if(is_string($fields)) {
            $fieldDetails = $fields;
        }

        // Prepare the WHERE details.
        $whereDetails = '';

        if(is_array($where)) {
            ksort($where);

            $idx = 0;

            foreach ($where as $key => $value) {
                if($idx > 0) {
                    $whereDetails .= ' AND ';
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

                $whereDetails .= "$key $operator :$key";

                $bindParams[$key] = $value;

                $idx++;
            }
        }
        else if(is_string($where)) {
            $whereDetails = $where;
        }

        if(! empty($whereDetails)) {
            $whereDetails = 'WHERE ' .$whereDetails;
        }

        // Prepare the LIMIT details.
        $limitDetails = '';

        $fetchAll = true;

        if(is_array($limits) && (count($limits) == 2)) {
            $limitDetails = implode(',', $limits);
        }
        else if(is_numeric($limits)) {
            $limitDetails = $limits;
        }
        else if($limits !== false) {
            $fetchAll = false;
        }

        if(! empty($limitDetails)) {
            $limitDetails = 'LIMIT ' .$limitDetails;
        }

        // Prepare the SQL Query
        $sql = "SELECT $fieldDetails FROM $table $whereDetails $limitDetails ;";

        //
        $data = $this->trigger('before_select', array(
            'method' =>'select',
            'where'  => $where,
            'fields' => $fields
        ));

        $result = $this->db->select($sql, $bindParams, $fetchAll, $fetchMethod, $className);

        $result = $this->trigger('after_select', array(
            'method' => 'select'
            'where'  => $where,
            'fields' => $fields,
            'result' => $result
        ));

        return $result;
    }

    public function delete($where)
    {
        $where = $this->trigger('before_delete', array(
            'method' =>'delete',
            'where'  => $where
        ));

        $result = $this->db->delete($this->table_name, $where);

        $result = $this->trigger('after_delete', array(
            'method' => 'delete'
            'where'  => $where,
            'result' => $result,
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
        $sql = "SELECT $field FROM " .DB_PREFIX .$this->table_name ." WHERE $field = :$field";

        $data = $this->db->selectAll($sql, array($field => $value));

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
    public function as_object($class = null)
    {
        $this->temp_return_type = ! empty($class) ? $class : 'object';

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
        if (empty($row['fields'])) {
            return null;
        }

        $row = $row['fields'];

        // Created_on
        if (! array_key_exists($this->created_field, $row)) {
            $row[$this->created_field] = $this->set_date();
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
        if (empty($row['fields'])) {
            return null;
        }

        $row = $row['fields'];

        if (is_array($row) && ! array_key_exists($this->modified_field, $row)) {
            $row[$this->modified_field] = $this->set_date();
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
