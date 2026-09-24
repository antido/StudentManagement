<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates the search / sort / direction query string used by index pages.
 */
abstract class IndexRequest extends FormRequest
{
    /**
     * Sort keys the listing accepts.
     *
     * @return array<int, string>
     */
    abstract protected function sortable(): array;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', 'string', Rule::in($this->sortable())],
            'direction' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
        ];
    }

    public function search(): ?string
    {
        return $this->validated('search');
    }

    public function sortField(): string
    {
        return $this->validated('sort') ?? 'id';
    }

    public function sortDirection(): string
    {
        return $this->validated('direction') ?? 'desc';
    }
}
