<?php

namespace View;

use Filesystem\Filesystem;
use View\ViewFinderInterface;


class FileViewFinder implements ViewFinderInterface
{
    /**
     * The Filesystem instance.
     *
     * @var \Filesystem\Filesystem
     */
    protected $files;

    /**
     * The array of Views that have been located.
     *
     * @var array
     */
    protected $views = array();

    /**
     * Register a View extension with the finder.
     *
     * @var array
     */
    protected $extensions = array('php');


    /**
     * Create a new file view loader instance.
     *
     * @param  \Filesystem\Filesystem  $files
     * @param  array  $extensions
     * @return void
     */
    public function __construct(Filesystem $files, array $extensions = null)
    {
        $this->files = $files;

        if (isset($extensions)) {
            $this->extensions = $extensions;
        }
    }

    /**
     * Get the fully qualified location of the view.
     *
     * @param  string  $name
     * @return string
     */
    public function find($name)
    {
        if (isset($this->views[$name])) return $this->views[$name];

        return $this->views[$name] = $this->findViewFile($name);
    }

    /**
     * Find the given view in the list of paths.
     *
     * @param  string  $name
     * @param  array   $paths
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function findViewFile($name)
    {
        foreach ($this->getPossibleViewFiles($name) as $file) {
            if ($this->files->exists($file)) {
                return $file;
            }
        }
    }

    /**
     * Get an array of possible view files.
     *
     * @param  string  $name
     * @return array
     */
    protected function getPossibleViewFiles($name)
    {
        return array_map(function($extension) use ($name)
        {
            return str_replace('.', '/', $name) .'.' .$extension;

        }, $this->extensions);
    }

    /**
     * Register an extension with the view finder.
     *
     * @param  string  $extension
     * @return void
     */
    public function addExtension($extension)
    {
        if (($index = array_search($extension, $this->extensions)) !== false) {
            unset($this->extensions[$index]);
        }

        array_unshift($this->extensions, $extension);
    }

    /**
     * Get the filesystem instance.
     *
     * @return \Filesystem\Filesystem
     */
    public function getFilesystem()
    {
        return $this->files;
    }

    /**
     * Get registered extensions.
     *
     * @return array
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

}
