<?php

namespace Umomega\Media\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmbedMedium extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return ['url' => 'required|url'];
    }
}