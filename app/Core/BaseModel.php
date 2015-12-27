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
     * Support for soft_deletes.
     */
    protected $soft_deletes      = false;
    protected $soft_delete_key   = 'deleted';
    protected $temp_with_deleted = false;

    /**
     * Various callbacks available to the Class.
     * They are simple lists of method names (methods will be ran on $this).
     */
    protected $before_insert = array();
    protected $after_insert  = array();
    protected $before_update = array();
    protected $after_update  = array();
    protected $before_find   = array();
    protected $after_find    = array();
    protected $before_delete = array();
    protected $after_delete  = array();

    protected $callback_parameters = array();

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
     * Provide direct access to any of the Database Engine instance methods
     * BUT make it look like it's part of the Class; purely for convenience.
     *
     * @param $name
     * @param $params
     */
    public function __call($name, $params = null)
    {
        $method = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name));

        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $params);
        }

        if (! method_exists($this->db, $name)) {
            throw new \BadMethodCallException("Method $name() does not exist in class " . get_class($this));
        }

        return call_user_func_array(array($this->db, $name), $params);
    }

}
