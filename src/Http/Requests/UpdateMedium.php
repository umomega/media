<?php

namespace Umomega\Media\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMedium extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
        	'name' => 'required|max:255',
        	'alttext' => 'nullable|array',
            'alttext.*' => 'nullable',
        	'caption' => 'nullable|array',
            'caption.*' => 'nullable',
        	'description' => 'nullable|array',
            'description.*' => 'nullable',
            'public_url' => 'required',
        ];
    }
}