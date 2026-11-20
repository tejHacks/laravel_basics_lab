<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for creating and updating tasks (see the "Form Requests" lesson).
 */
class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // the demo has no owners; real apps check a policy here
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'priority' => ['required', 'integer', Rule::in(array_keys(Task::PRIORITIES))],
            'due_on' => ['nullable', 'date'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'done' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Every task needs a title.',
            'category_id.exists' => 'Pick a category from the list.',
        ];
    }

    /** Normalise input before the rules run. */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => trim((string) $this->input('title')),
            'done' => $this->boolean('done'),
        ]);
    }
}
