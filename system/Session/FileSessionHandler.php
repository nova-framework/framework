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
     * @param  string    $path
     * @param  int       $lifetime
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
    public function open($savePath, $sessionName)
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
     * @param  string  $sessionId
     * @return string
     */
    public function read($sessionId)
    {
        $filePath = $this->savePath .'sess_' .$sessionId;

        if(is_readable($filePath)) {
            return file_get_contents($filePath);
        } else {
            return '';
        }
    }

    /**
     * File write handler.
     *
     * @param  string     $sessionId
     * @param  string     $sessionData
     * @return string
     */
    public function write($sessionId , $sessionData)
    {
        $filePath = $this->savePath .'sess_' .$sessionId;

        return (file_put_contents($filePath, $sessionData) !== false);
    }

    /**
     * File destroy handler.
     *
     * @param  string  $sessionId
     * @return string
     */
    public function destroy($sessionId)
    {
        $filePath = $this->savePath .'sess_' .$sessionId;

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return true;
    }

    /**
     * File Garbage Collector handler.
     *
     * @param  int  $lifeTime
     * @return bool
     */
    public function gc($lifeTime)
    {
        foreach (glob($this->savePath .'sess_*') as $file) {
            clearstatcache(true, $file);

            $lastTime = filemtime($file) + $lifeTime;

            if (($lastTime < time()) && file_exists($file)) {
                unlink($file);
            }
        }

        return true;
    }
}
