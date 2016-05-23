<?php

namespace Session;

use SessionHandlerInterface;


class FileSessionHandler implements SessionHandlerInterface
{
    /**
     * The path where sessions should be stored.
     *
     * @var string
     */
    protected $path;

    /**
     * The session lifetime.
     *
     * @var int
     */
    protected $lifetime;

    /**
     * Create a new instance.
     *
     * @param  string     $path
     * @param  int         $lifetime
     * @return void
     */
    function __construct($path, $lifetime)
    {
        $this->path     = $path;
        $this->lifetime = $lifetime;
    }

    /**
     * File open handler.
     *
     * @return bool
     */
    public function open($save_path, $session_name)
    {
        return true;
    }

    /**
     * File close handler.
     *
     * @return bool
     */
    public function close()
    {
        return true;
    }

    /**
     * File read handler.
     *
     * @param  int  $id
     * @return string
     */
    public function read($id)
    {
        $filePath = $this->path .'/' .$id;

        if (is_readable($filePath)) {
            return file_get_contents($filePath);
        }

        return '';
    }

    /**
     * File write handler.
     *
     * @param  int         $id
     * @param  string     $data
     * @return string
     */
    public function write($id, $data)
    {
        $filePath = $this->path .'/' .$id;

        file_put_contents($filePath, $data);

        return true;
    }

    /**
     * File destroy handler.
     *
     * @param  int  $id
     * @return string
     */
    public function destroy($id)
    {
        $filePath = $this->path .'/' .$id;

        if (file_exists($filePath)) {
            unlink($filePath);

            return true;
        }

        return false;
    }

    /**
     * File gc handler.
     *
     * @param  int  $lifetime
     * @return string
     */
    public function gc($lifetime)
    {
        $lifetime = empty($this->lifetime) ? $lifetime : $this->lifetime;

        $timeout = time() - $this->lifetime;

        foreach (glob($this->path .'/*') as $file) {
            $timestamp  = filemtime($file);

            if (is_writable($file) && ($timestamp < $timeout)) {
                unlink($file);
            }
        }

        return true;
    }
}
