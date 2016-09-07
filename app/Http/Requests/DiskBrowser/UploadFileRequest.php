<?php

namespace App\Http\Requests\DiskBrowser;

use App\DiskBrowser\Disk;
use App\Http\Requests\Request;

class UploadFileRequest extends Request
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

        $extensions = ($this->input('disk')) ? Disk::extensionsFor($this->input('disk')) : [];

        return [
            'disk'  => 'required|in:' . implode(',',array_keys($disks)),
            'file'  => 'required' . (($extensions != []) ? ('|mimes:' . implode(',', $extensions)) : ''),
            'path'  => 'required',
        ];
    }
}
