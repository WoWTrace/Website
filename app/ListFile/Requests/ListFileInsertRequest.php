<?php
declare(strict_types=1);

namespace App\ListFile\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListFileInsertRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'id'   => 'integer|min:1|nullable',
            'path' => 'required|string|min:2|max:265'
        ];
    }
}
