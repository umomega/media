<?php

namespace Umomega\Media\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadMedium extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file' => 'required|file|max:' . (int)max_upload_size()/1024 . '|mimetypes:' . allowed_mimetypes(',') . '|mimes:' . allowed_extensions(',')
        ];
    }
}