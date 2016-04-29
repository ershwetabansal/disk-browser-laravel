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
        $availableDisks = collect(config('disks'));

        return [
            'disk'  => 'required|in:' . array_keys($availableDisks),
            'path'  => 'required',
            'file'  => 'required|mimes:pdf,xls,xlsx,doc,docx,gif,png,jpg,bmp',
        ];
    }
}
