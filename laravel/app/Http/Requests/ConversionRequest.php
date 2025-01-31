<?php

namespace App\Http\Requests;

// use App\Models\Conversion;
use Illuminate\Foundation\Http\FormRequest;

// use Illuminate\Validation\Rule;

class ConversionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'file' => ['required', 'max:100', 'mimes:txt'],
            'item_separator' => ['required', 'string', 'in:line,cr'],
            'label_style' => ['required', 'string', 'in:short,long,gs'],
            'override_labels' => ['required'],
            'line_endings' => ['required', 'string', 'in:w,l'],
            'char_encoding' => ['required', 'string', 'in:utf8,utf8leave'],
            'percent_comment' => ['required'],
            'include_source' => ['required'],
            'report_type' => ['required', ' string', 'in:standard,detailed'],
            'debug' => [],
        ];
    }

    public function messages(): array
    {
        return [
            'file.mimes' => 'The file you selected is not plain text',
            'item_separator.required' => 'Please select an option',
            'label_style.required' => 'Please select an option',
            'override_labels' => 'Please select an option',
            'line_endings.required' => 'Please select an option',
            'char_encoding.required' => 'Please select an option',
            'percent_comment' => 'Please select an option',
            'include_source' => 'Please select an option',
            'report_type' => 'Please select an option',
        ];
    }
}
