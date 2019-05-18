<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rule;

class OriginRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'channel' => [
                'required',
                Rule::in([5118, 'naipan']),
            ],
            'isTitle' => 'required|boolean',
            'start_path' => 'required',
            'over_path' => 'required',
            'th' => 'required|numeric',
        ];
    }
}
