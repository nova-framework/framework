<?php

namespace Session;

use Database\Connection;

use SessionHandlerInterface;


class DatabaseSessionHandler implements SessionHandlerInterface
{
    /**
     * The Database Connection instance.
     *
     * @var \Database\Connection
     */
    protected $db;

    /**
     * The Database Table.
     *
     * @var string
     */
    protected $table;

    /**
     * The existence state of the Session.
     *
     * @var bool
     */
    protected $exists = false;


    /**
     * Create a new instance.
     *
     * @param  array     $config
     * @return void
     */
    function __construct(array $config)
    {
        $this->db = Connection::getInstance();

        $this->table = array_get($config, 'table', 'options');
    }

    /**
     * Database open handler.
     *
     * @return bool
     */
    public function open($savePath, $sessionName)
    {
        return true;
    }

    /**
     * Database close handler.
     *
     * @return bool
     */
    public function close()
    {
        return true;
    }

    /**
     * Database read handler.
     *
     * @param  int  $sessionId
     * @return string
     */
    public function read($sessionId)
    {
        $session = (object) $this->getQuery()->find($sessionId);

        if (isset($session->payload)) {
            $this->exists = true;

            return base64_decode($session->payload);
        }

        return '';
    }

    /**
     * Database write handler.
     *
     * @param  int      $sessionId
     * @param  string   $sessionData
     * @return string
     */
    public function write($sessionId, $sessionData)
    {
       if ($this->exists) {
            $data = array(
                'payload'       => base64_encode($sessionData),
                'last_activity' => time(),
            );

            $this->getQuery()->where('id', $sessionId)->update($data);
        } else {
            $data = array(
                'id'            => $sessionId,
                'payload'       => base64_encode($sessionData),
                'last_activity' => time(),
            ));

            $this->getQuery()->insert($data);
        }

        $this->exists = true;

        return true;
    }

    /**
     * Database destroy handler.
     *
     * @param  int  $sessionId
     * @return string
     */
    public function destroy($sessionId)
    {
        $this->getQuery()->where('id', $sessionId)->delete();
    }

    /**
     * Database gc handler.
     *
     * @param  int  $lifeTime
     * @return string
     */
    public function gc($lifeTime)
    {
        $this->getQuery()
            ->where('last_activity', '<=', (time() - $lifeTime))
            ->delete();
    }

    /**
     * Get a fresh QueryBuilder instance for the Table.
     *
     * @return \Database\Query\Builder
     */
    protected function getQuery()
    {
        return $this->connection->table($this->table);
    }

    /**
     * Set the existence state for the Session.
     *
     * @param  bool  $value
     * @return $this
     */
    public function setExists($value)
    {
        $this->exists = $value;

        return $this;
    }
}
