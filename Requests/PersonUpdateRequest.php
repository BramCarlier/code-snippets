<?php

namespace App\Http\Requests;

class PersonUpdateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => ['required', 'numeric'],
            'type' => ['required'],
            'mobile' => ['nullable'],
            'email' => ['required', 'email'],
            'privacy_check' => ['required', 'accepted'],
            'properties' => ['nullable', 'array'],
        ];
    }
}
