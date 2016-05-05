<?php

namespace App\Http\Requests;

use App\DiskSpecifics;
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
        $diskName = $this->input('disk');

        $allowedExtensions = DiskSpecifics::getAllowedFileMimeTypesFor($diskName);

        return [
            'disk'  => 'required|in:' . implode(',',array_keys($disks)),
            'path'  => 'required|path_exists',
            'file'  => 'required' . (($allowedExtensions != '' && $allowedExtensions != null) ? ('|mimes:' . $allowedExtensions) : ''),
        ];
    }

    public function messages()
    {
        return [
            'path.path_exists' => 'Path does not exist in given disk.',
        ];
    }
}
