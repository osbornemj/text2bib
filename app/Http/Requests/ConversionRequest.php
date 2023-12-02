<?php

namespace App\Http\Requests;

use App\Models\Conversion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'file' => ['required', 'mimetypes:text/plain', 'max:100'],
            'item_separator' => ['required', 'string', 'in:line,cr'],
            'first_component' => ['required', 'string', 'in:authors,year'],
            'label_style' => ['required', 'string', 'in:short,long,gs'],
            'override_labels' => ['required'],
            'line_endings' => ['required', 'string', 'in:w,l'],
            'char_encoding' => ['required', 'string', 'in:utf8,utf8leave,ascii,windows1252'],
            'percent_comment' => ['required'],
            'include_source' => ['required'],
            'report_type' => ['required', ' string', 'in:standard,detailed'],
            'debug' => [],
        ];
    }
}
