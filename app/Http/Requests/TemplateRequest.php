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
                    'paragraphs' => 'required|array',
                    'images' => 'required|array',
                    'content' => 'required',
                ];
            }
            // UPDATE
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'name' => 'required',
                    'paragraphs' => 'required|array',
                    'images' => 'required|array',
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
