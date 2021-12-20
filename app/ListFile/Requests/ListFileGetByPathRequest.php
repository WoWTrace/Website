<?php
declare(strict_types=1);

namespace App\ListFile\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListFileGetByPathRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'path' => 'required|string|min:2|max:265'
        ];
    }
}
