<?php

namespace App\Http\Requests;


class ArticleRequest extends Request
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
                    'template_id' => 'required|integer',
                    'name' => 'required',
                    'desc' => 'nullable',
                    // 'title' => 'required',
                    // 'keywords' => 'required',
                    // 'description' => 'required',
                    // 'content' => 'required',
                    // 'param_names' => 'required',
                    // 'param_contents' => 'required',
                ];
            }
            // UPDATE
            case 'PUT':
            case 'PATCH':
            {
                return [
                    // UPDATE ROLES
                    'template_id' => 'required|integer',
                    'name' => 'required',
                    'desc' => 'nullable',
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
