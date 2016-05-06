<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class GetFilesRequest extends Request
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
//            'path'  => 'path_exists',
        ];
    }

//    public function messages()
//    {
//        return [
//            'path.path_exists' => 'Path does not exist in given disk.',
//        ];
//    }
}
