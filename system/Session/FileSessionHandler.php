<?php

namespace Session;


class FileSessionHandler
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
        $this->path = $path;

        $this->lifetime = $lifetime;

        session_set_save_handler(
            array($this, 'open'),
            array($this, 'close'),
            array($this, 'read'),
            array($this, 'write'),
            array($this, 'destroy'),
            array($this, 'gc')
        );
    }

    /**
     * File open handler.
     *
     * @return bool
     */
    public function open()
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
        }
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
            if($file == '.gitignore') {
                continue;
            }

            $file = $this->path .'/' .$file;

            $mtime  = filemtime($file);

            if (file_exists($file) && ($mtime < $time)) {
                unlink($this->path .'/' .$file);
            }
        }
    }
}
