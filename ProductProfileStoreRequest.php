<?php

namespace App\Http\Requests;

class ProductProfileStoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'nullable|email',
            'containers' => 'required|array|min:1',
            'containers.*.amount' => [
                'required',
                'numeric',
                'min:0',
                'max:' . config('ci-sites.projects.' . $this->project() . '.products.max', 9),
            ],
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function containers()
    {
        return collect($this->validated()['containers'])
            ->map(function ($value) {
                return $value['amount'];
            })
            ->filter(function ($amount) {
                return $amount > 0;
            });
    }
}
