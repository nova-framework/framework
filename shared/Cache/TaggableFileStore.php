<?php

namespace Shared\Cache;

use Nova\Cache\FileStore;
use Nova\Filesystem\Filesystem;
use Nova\Support\Arr;
use Nova\Support\Str;

use Shared\Cache\FileTagSet;
use Shared\Cache\TaggableFileStore;


class TaggableFileStore extends FileStore
{
    /**
     * @var string
     */
    public $separator;

    /**
     * @var string
     */
    protected $queue;

    /**
     * Create a new file cache store instance.
     *
     * @param  \Nova\Filesystem\Filesystem $files
     * @param  string                      $directory
     * @param  array                       $options
     */
    public function __construct(Filesystem $files, $directory, array $options)
    {
        $this->separator = Arr::get($options, 'separator', '~#~');

        $this->queue = Arr::get($options, 'queue');

        parent::__construct($files, $directory);
    }

    /**
     * Get the full path for the given cache key.
     *
     * @param  string $key
     *
     * @return string
     */
    protected function path($key)
    {
        $folder = '';

        //
        $isTag = false;

        $segments = explode($this->separator, $key);

        if (count($segments) > 1) {
            $folder = reset($segments);

            if ($folder === 'cache_tags') {
                $folder = 'tags';

                $isTag = true;
            } else {
                $folder = str_replace('|', '_', $folder);
            }

            $key = end($segments);
        }

        if ($isTag) {
            $hash = $key;

            $parts = array();
        } else {
            $parts = array_slice(str_split($hash = sha1($key), 2), 0, 2);
        }

        $path = $this->directory .(! empty($folder) ? DS .$folder : '');

        return $path .DS .(count($parts) > 0 ? implode(DS, $parts) .DS : '') .$hash;
    }

    /**
     * Begin executing a new tags operation.
     *
     * @param  string  $name
     * @return \Nova\Cache\TaggedCache
     */
    public function section($name)
    {
        return $this->tags($name);
    }

    /**
     * Begin executing a new tags operation.
     *
     * @param  array|mixed $names
     *
     * @return \Nova\Cache\TaggedCache
     */
    public function tags($names)
    {
        return new TaggedFileCache($this, new FileTagSet($this, is_array($names) ? $names : func_get_args()));
    }

    /**
     * @param string $tagId
     */
    public function flushOldTag($tagId)
    {
        foreach ($this->files->directories($this->directory) as $directory) {
            if (Str::contains(basename($directory), $tagId)) {
                $this->files->deleteDirectory($directory);
            }
        }
    }
}
