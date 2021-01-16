<?php

namespace App\Http\Requests;

class TemplateRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch($this->method())
        {
            // CREATE
            case 'POST':
            {
                return [
                    'name' => 'required',
                    'fixed_paragraphs' => 'nullable',
                    'custom_paragraphs' => 'nullable|array',
                    'custom_params' => 'nullable|array',
                    'fixed_params' => 'nullable|array',
                    'images' => 'required',
                    'content' => 'required',
                ];
            }
            // UPDATE
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'name' => 'required',
                    'fixed_paragraphs' => 'nullable',
                    'custom_paragraphs' => 'nullable|array',
                    'custom_params' => 'nullable|array',
                    'fixed_params' => 'nullable|array',
                    'images' => 'required',
                    'content' => 'required',
                ];
            }
            case 'GET':
            case 'DELETE':
            default:
            {
                return [];
            };
        }
    }
}
