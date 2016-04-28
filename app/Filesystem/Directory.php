<?php

namespace App\Filesystem;
use Illuminate\Filesystem\Filesystem;
use App\Exceptions\Filesystem\MissingPathException;

class Directory
{
    /**
     * @var string $path
     */
    protected $path;
    /**
     * @var Filesystem $filesystem
     */
    private $filesystem;

    /**
     * @var array $files All files within the directory.
     */
    protected $files = [];

    /**
     * @var array $directories All sub-directories within the directory.
     */
    protected $directories = [];

    /**
     * @param Filesystem $filesystem
     * @param string|null $path
     */
    public function __construct(Filesystem $filesystem, $path = null)
    {
        $this->filesystem = $filesystem;
        $this->path = $path;
    }

    /**
     * Set the directory path.
     *
     * @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Getter for path.
     *
     * @return string
     */
    public function path()
    {
        return $this->path;
    }

    /**
     * @return array
     */
    public function files()
    {
        $this->guardAgainstInvalidPath();

        $this->files = $this->filesystem->files($this->path);
        return $this->files;
    }

    /**
     * @return array
     */
    public function directories()
    {
        $this->guardAgainstInvalidPath();

        $this->directories = $this->filesystem->directories($this->path);
        return $this->directories;
    }

    /**
     * @private
     * @throws MissingPathException
     */
    private function guardAgainstInvalidPath()
    {
        if (is_null($this->path)) {
            throw new MissingPathException();
        }
    }

}