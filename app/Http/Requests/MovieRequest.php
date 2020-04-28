<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MovieRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string',
            'description' => 'required|string',
            'image_url' => 'required|url',
            'genres' => 'required|array|min:1'
        ];
    }

    public function messages() 
    {
        return [
            'genres.required' => 'At least one genre must be selected.',
            'image_url.required' => 'Image URL is required. Must be JPG or JPEG.',
        ];
    }
}
