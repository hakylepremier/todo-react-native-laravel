<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTodoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:255'],
            'completed' => ['required', 'boolean'],
            'priority' => ['required', 'boolean'],
            'due_date' => ['date', 'after_or_equal:today', "nullable"]
        ];
    }

    /**
     * Prepare the data for validation.
     */
    // protected function prepareForValidation(): void
    // {
    //     dd($this->all());
    //     // $this->merge([
    //     //     'due_date' => $this->due_date ? Carbon::parse($this->due_date)->format('Y-m-d') : null,
    //     // ]);
    // }
}
