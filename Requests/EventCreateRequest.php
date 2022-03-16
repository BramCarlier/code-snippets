<?php

namespace App\Http\Requests;

use Illuminate\Support\Arr;

class EventCreateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'type' => ['required'],
            'category' => ['required'],
            'poi_id' => ['required', 'numeric'],
            'telephone_number' => ['nullable'],
            'email' => ['nullable'],
        ];

        return array_merge($rules, $this->eventRules(Arr::get($this->validationData(), 'category')));
    }

    public function eventRules($category)
    {
        $eventRules = [
            'action_event' => [
                'profile_id' => ['required'],
                'product_id' => ['required'],
                'intervention_date' => ['required', 'date_format:Y-m-d'],
            ],
            'other_garbage_event' => [
                'memo' => ['required'],
                'intervention_date' => ['required', 'date_format:Y-m-d'],
                'profile_id' => ['required'],
            ],
            'mail_event' => [
                'memo' => ['required'],
                'product_id' => ['nullable'],
            ],
        ];

        return $eventRules[$category] ?? [];
    }
}
