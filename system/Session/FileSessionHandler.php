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
    protected $savePath;

    /**
     * Create a new instance.
     *
     * @param  string     $path
     * @param  int         $lifetime
     * @return void
     */
    function __construct($path)
    {
        $this->savePath = rtrim($path, '/') .DS;
    }

    /**
     * File open handler.
     *
     * @return bool
     */
    public function open($save_path, $name)
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
     * @param  string  $session_id
     * @return string
     */
    public function read($session_id)
    {
        $filePath = $this->savePath .'sess_' .$session_id;

        return is_readable($filePath) ? (string) @file_get_contents($filePath) : '';
    }

    /**
     * File write handler.
     *
     * @param  string     $session_id
     * @param  string     $session_data
     * @return string
     */
    public function write($session_id , $session_data)
    {
        $filePath = $this->savePath .'sess_' .$session_id;

        return (file_put_contents($filePath, $session_data) !== false);
    }

    /**
     * File destroy handler.
     *
     * @param  string  $session_id
     * @return string
     */
    public function destroy($session_id)
    {
        $filePath = $this->savePath .'sess_' .$session_id;

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return true;
    }

    /**
     * File Garbage Collector handler.
     *
     * @param  int  $maxlifetime
     * @return bool
     */
    public function gc($maxlifetime)
    {
        foreach (glob($this->savePath .'sess_*') as $file) {
            clearstatcache(true, $file);

            if (((filemtime($file) + $maxlifetime) < time()) && file_exists($file)) {
                unlink($file);
            }
        }

        return true;
    }
}
