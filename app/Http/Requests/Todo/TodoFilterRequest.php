<?php

namespace App\Http\Requests\Todo;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class TodoFilterRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'q' => ['nullable', 'string'],
            'sort_by' => ['nullable', 'string',  'in:asc,desc,ASC,DESC'],
            'order_by' => ['nullable', 'string', Rule::in(['id', 'title', 'date', 'status'])],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    public function prepareForValidation()
    {
        $this->mergeIfMissing([
            'order_by' => "id",
            'sort_by' => "desc",
        ]);
    }
}
