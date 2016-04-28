<?php

namespace App\Filesystem;

use App\User;
use Illuminate\Database\Eloquent\Model;

class File
{
    protected $model;
    protected $name;
    protected $path;
    protected $size;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function size()
    {
        return \File::size($this->model->path);
    }

    public function extension()
    {
        return pathinfo($this->model->path)['extension'];
    }

    public function canBeAccessedBy(User $user)
    {
        return true;
    }
}