<?php

namespace App\Http\Requests\DiskBrowser;

use App\Filesystem\Directory;
use App\Http\Requests\Request;

class CreateNewDirectoryRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $disks = config('filesystems.disks');

        return [
            'disk'  => 'required|in:' . implode(',',array_keys($disks)),
            'name'  => 'required',
            'path'  => 'required',
        ];
    }
}
