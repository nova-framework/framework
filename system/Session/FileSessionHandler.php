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
        if (empty($this->lifetime)) {
            $this->lifetime = $lifetime;
        }

        $time = time() - $this->lifetime;

        foreach (scandir($this->path) as $file) {
            if(($file == '.') || ($file == '..') || ($file == '.gitignore')) {
                continue;
            }

            $file = $this->path .'/' .$file;

            $mtime  = filemtime($file);

            if (file_exists($file) && ($mtime < $time)) {
                unlink($this->path .'/' .$file);
            }
        }

        return true;
    }
}
