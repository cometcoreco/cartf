<?php

namespace Modules\MobileApp\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMobileIntroRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "title" => "required",
            "description" => "required",
            "image_id" => "required|exists:media_uploaders,id"
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
