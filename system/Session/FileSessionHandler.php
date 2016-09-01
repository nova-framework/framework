<?php

namespace Session;

use Filesystem\Filesystem;

use Symfony\Component\Finder\Finder;


class FileSessionHandler implements \SessionHandlerInterface
{
    /**
     * The filesystem instance.
     *
     * @var \Filesystem\Filesystem
     */
    protected $files;

    /**
     * The path where sessions should be stored.
     *
     * @var string
     */
    protected $path;

    /**
     * Create a new file driven handler instance.
     *
     * @param  \Filesystem\Filesystem  $files
     * @param  string  $path
     * @return void
     */
    public function __construct(Filesystem $files, $path)
    {
        $this->path = $path;

        $this->files = $files;
    }

    /**
     * {@inheritDoc}
     */
    public function open($savePath, $sessionName)
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function close()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function read($sessionId)
    {
        if ($this->files->exists($path = $this->path .DS .$sessionId)) {
            return $this->files->get($path);
        }

        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function write($sessionId, $data)
    {
        $this->files->put($this->path .DS .$sessionId, $data, true);
    }

    /**
     * {@inheritDoc}
     */
    public function destroy($sessionId)
    {
        $this->files->delete($this->path .DS .$sessionId);
    }

    /**
     * {@inheritDoc}
     */
    public function gc($lifetime)
    {
        $files = Finder::create()
                    ->in($this->path)
                    ->files()
                    ->ignoreDotFiles(true)
                    ->date('<= now - ' .$lifetime .' seconds');

        foreach ($files as $file) {
            $this->files->delete($file->getRealPath());
        }
    }

}
