<?php

namespace App\Http\Requests;

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

        $allowedExtensions = '';

        if (isset($disks[$diskName]) && isset($disks[$diskName]['type'])) {
            $allowedExtensions = $disks[$this->input('disk')]['type'];
        }

        return [
            'disk'  => 'required|in:' . implode(',',array_keys($disks)),
            'path'  => 'required',
            'file'  => 'required' . (($allowedExtensions != '') ? ('|mimes:' . $allowedExtensions) : ''),
        ];
    }
}
